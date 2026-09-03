<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Intern;
use App\Models\Notification;
use App\Services\TaskNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Task::with(['intern.user', 'assignedBy']);

        // Filter for intern - only show their tasks
        if ($user->isIntern()) {
            $intern = $user->intern;
            if (!$intern) {
                return redirect()->route('dashboard')->with('error', 'Profil magang tidak ditemukan.');
            }
            $query->where('intern_id', $intern->id);
            // Hide scheduled tasks from interns
            $query->where('status', '!=', 'scheduled');
        }

        // Search
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        // Filter by intern (for admin/pembimbing)
        if ($request->intern_id && $user->canManage()) {
            $query->where('intern_id', $request->intern_id);
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(15);
        $interns = $user->canManage() ? Intern::with('user')->where('status', 'active')->limit(50)->get() : collect();

        return view('tasks.index', compact('tasks', 'interns'));
    }

    public function create()
    {
        $interns = Intern::with('user')->where('status', 'active')->limit(100)->get();
        
        // Pre-format for JavaScript
        $internsJson = $interns->map(function($intern) {
            return [
                'id' => $intern->id,
                'name' => $intern->user->name ?? 'Unknown',
                'school' => $intern->school ?? '',
                'department' => $intern->department ?? '',
            ];
        })->values();
        
        return view('tasks.create', compact('interns', 'internsJson'));
    }

    // API endpoint for intern search
    public function searchInterns(Request $request)
    {
        $search = $request->get('q', '');
        
        $query = Intern::with('user')->where('status', 'active');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })
                ->orWhere('school', 'like', "%{$search}%")
                ->orWhere('department', 'like', "%{$search}%");
            });
        }
        
        $interns = $query->limit(20)->get()->map(function($intern) {
            return [
                'id' => $intern->id,
                'name' => $intern->user->name ?? 'Unknown',
                'school' => $intern->school ?? '',
                'department' => $intern->department ?? '',
                'label' => ($intern->user->name ?? 'Unknown') . ' - ' . ($intern->school ?? 'N/A') . ' (' . ($intern->department ?? 'N/A') . ')',
            ];
        });
        
        return response()->json($interns);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'deadline_time' => 'nullable|date_format:H:i',
            'assign_to' => 'required|in:all,selected',
            'intern_ids' => 'required_if:assign_to,selected|array',
            'intern_ids.*' => 'exists:interns,id',
        ]);

        // Default start_date to today if not provided
        $startDate = $request->start_date ?: now()->toDateString();
        $isScheduled = $startDate > now()->toDateString();

        DB::beginTransaction();
        try {
            // Create TaskAssignment for bulk assignment
            $taskAssignment = TaskAssignment::create([
                'title' => $request->title,
                'description' => $request->description,
                'assigned_by' => Auth::id(),
                'priority' => $request->priority,
                'start_date' => $startDate,
                'deadline' => $request->deadline,
                'deadline_time' => $request->deadline_time,
                'assign_to_all' => $request->assign_to === 'all',
            ]);

            // Get interns to assign
            if ($request->assign_to === 'all') {
                $interns = Intern::where('status', 'active')->get();
            } else {
                $interns = Intern::whereIn('id', $request->intern_ids)->get();
            }

            // Attach interns to task assignment
            $taskAssignment->interns()->attach($interns->pluck('id'));

            // Create individual tasks for each intern
            foreach ($interns as $intern) {
                $task = Task::create([
                    'task_assignment_id' => $taskAssignment->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'intern_id' => $intern->id,
                    'assigned_by' => Auth::id(),
                    'priority' => $request->priority,
                    'start_date' => $startDate,
                    'deadline' => $request->deadline,
                    'deadline_time' => $request->deadline_time,
                    'status' => $isScheduled ? 'scheduled' : 'pending',
                ]);

                // Only send notification if not scheduled for future
                if (!$isScheduled) {
                    // Send notification to intern (in-app + email)
                    Notification::notify(
                        $intern->user_id,
                        Notification::TYPE_TASK_ASSIGNED,
                        'Tugas Baru: ' . $request->title,
                        'Anda mendapat tugas baru dari ' . Auth::user()->name . '. Deadline: ' . ($request->deadline ? \Carbon\Carbon::parse($request->deadline)->format('d M Y') : 'Tidak ada'),
                        route('tasks.show', $task),
                        ['task_id' => $task->id]
                    );

                    // Send email notification
                    TaskNotificationService::notifyTaskAssigned($task);
                }
            }

            DB::commit();

            $count = $interns->count();
            if ($isScheduled) {
                return redirect()->route('tasks.index')
                    ->with('success', "Tugas terjadwal untuk {$count} siswa! Notifikasi akan dikirim pada tanggal mulai.");
            }
            return redirect()->route('tasks.index')
                ->with('success', "Tugas berhasil diberikan kepada {$count} siswa!");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal membuat tugas: ' . $e->getMessage());
        }
    }

    public function show(Task $task)
    {
        $user = Auth::user();

        // Check access for intern
        if ($user->isIntern()) {
            // Load intern relationship if not loaded
            $intern = $user->intern;

            // If intern profile doesn't exist, deny access
            if (!$intern) {
                abort(403, 'Profil magang tidak ditemukan.');
            }

            // Check if task belongs to this intern
            if ((int) $task->intern_id !== (int) $intern->id) {
                abort(403, 'Anda tidak memiliki akses ke tugas ini.');
            }
        }

        $task->load(['intern.user', 'assignedBy', 'assessment', 'taskAssignment']);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $interns = Intern::with('user')->where('status', 'active')->get();
        return view('tasks.edit', compact('task', 'interns'));
    }

    public function update(Request $request, Task $task)
    {
        // Handle "all_active" - duplicate task for all active interns
        if ($request->intern_id === 'all_active') {
            $interns = Intern::where('status', 'active')->get();

            if ($interns->isEmpty()) {
                return back()->with('error', 'Tidak ada siswa aktif ditemukan.');
            }

            // Update current task with first intern
            $firstIntern = $interns->shift();
            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'intern_id' => $firstIntern->id,
                'priority' => $request->priority,
                'status' => $request->status,
                'start_date' => $request->start_date,
                'deadline' => $request->deadline,
                'deadline_time' => $request->deadline_time,
            ]);

            // Create copies for remaining interns
            foreach ($interns as $intern) {
                Task::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'intern_id' => $intern->id,
                    'assigned_by' => $task->assigned_by,
                    'priority' => $request->priority,
                    'status' => 'pending', // New copies start as pending
                    'start_date' => $request->start_date,
                    'deadline' => $request->deadline,
                    'deadline_time' => $request->deadline_time,
                    'task_assignment_id' => $task->task_assignment_id,
                ]);
            }

            $totalInterns = Intern::where('status', 'active')->count();
            return redirect()->route('tasks.index')
                ->with('success', "Tugas berhasil diduplikasi ke {$totalInterns} siswa aktif!");
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'intern_id' => 'nullable|exists:interns,id',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed,revision,scheduled',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'deadline_time' => 'nullable|date_format:H:i',
            'admin_feedback' => 'nullable|string',
        ]);

        $data = $request->only(['title', 'description', 'intern_id', 'priority', 'status', 'start_date', 'deadline', 'deadline_time', 'admin_feedback']);

        // Handle empty intern_id (set to null)
        if (empty($data['intern_id'])) {
            $data['intern_id'] = null;
        }

        // Track status changes
        if ($request->status !== $task->status) {
            if ($request->status === 'in_progress' && !$task->started_at) {
                $data['started_at'] = now();
            }

            if ($request->status === 'completed' && !$task->completed_at) {
                $data['completed_at'] = now();
                $data['submitted_at'] = now();

                // Check if late
                $deadlineDatetime = $task->deadline_datetime;
                if ($deadlineDatetime && now()->isAfter($deadlineDatetime)) {
                    $data['is_late'] = true;
                }
            }
        }

        $task->update($data);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Tugas berhasil diperbarui!');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')
            ->with('success', 'Tugas berhasil dihapus!');
    }

    // Submit task by intern - Links only
    public function submit(Request $request, Task $task)
    {
        $user = Auth::user();

        // Verify task belongs to intern
        $intern = $user->intern;
        if (!$intern || (int) $task->intern_id !== (int) $intern->id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        // Validate links array
        $request->validate([
            'submission_notes' => 'nullable|string|max:1000',
            'links' => 'required|array|min:1',
            'links.*.label' => 'required|string|max:100',
            'links.*.url' => 'required|url',
        ], [
            'links.required' => 'Minimal masukkan satu link!',
            'links.min' => 'Minimal masukkan satu link!',
            'links.*.label.required' => 'Label link wajib diisi!',
            'links.*.url.required' => 'URL link wajib diisi!',
            'links.*.url.url' => 'Format URL tidak valid!',
        ]);

        // Clean and prepare links
        $links = collect($request->links)->filter(function($link) {
            return !empty($link['url']) && !empty($link['label']);
        })->values()->toArray();

        if (empty($links)) {
            return back()->with('error', 'Minimal masukkan satu link yang valid!');
        }

        $data = [
            'status' => 'submitted',
            'submitted_at' => now(),
            'submission_notes' => $request->submission_notes,
            'submission_links' => $links,
        ];

        // Check if late
        $deadlineDatetime = $task->deadline_datetime;
        if ($deadlineDatetime && now()->isAfter($deadlineDatetime)) {
            $data['is_late'] = true;
        } else {
            $data['is_late'] = false;
        }

        // Mark as in_progress if not started
        if (!$task->started_at) {
            $data['started_at'] = now();
        }

        $task->update($data);

        $message = $data['is_late']
            ? 'Tugas dikumpulkan (Terlambat)! Menunggu review pembimbing.'
            : 'Tugas dikumpulkan tepat waktu! 🎉 Menunggu review pembimbing.';

        return redirect()->route('tasks.show', $task)->with('success', $message);
    }

    // Review task by admin/pembimbing
    public function review(Request $request, Task $task)
    {
        // Check permission
        if (!Auth::user()->canManage()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'action' => 'required|in:approve,revision',
            'score' => 'required_if:action,approve|nullable|integer|between:0,100',
            'feedback' => 'nullable|string|max:1000',
        ]);

        if ($request->action === 'approve') {
            $task->approve($request->score, $request->feedback);
            $message = 'Tugas berhasil disetujui dan dinilai!';

            // Notify intern about approval (in-app + email)
            Notification::notify(
                $task->intern->user_id,
                Notification::TYPE_TASK_APPROVED,
                'Tugas Disetujui: ' . $task->title,
                'Tugas Anda telah disetujui dengan nilai ' . $request->score . '/100. ' . ($request->feedback ? 'Feedback: ' . $request->feedback : ''),
                route('tasks.show', $task),
                ['task_id' => $task->id, 'score' => $request->score]
            );

            // Send email notification
            TaskNotificationService::notifyTaskStatusChange($task, 'approved');
        } else {
            $task->requestRevision($request->feedback);
            $message = 'Tugas dikembalikan untuk revisi.';

            // Notify intern about revision request (in-app + email)
            Notification::notify(
                $task->intern->user_id,
                Notification::TYPE_TASK_REVISION,
                'Revisi Diperlukan: ' . $task->title,
                'Tugas Anda memerlukan revisi. ' . ($request->feedback ? 'Catatan: ' . $request->feedback : 'Silakan periksa kembali.'),
                route('tasks.show', $task),
                ['task_id' => $task->id]
            );

            // Send email notification
            TaskNotificationService::notifyTaskStatusChange($task, 'revision');
        }

        return redirect()->back()->with('success', $message);
    }

    // Update status by intern (simple status change without submission)
    public function updateStatus(Request $request, Task $task)
    {
        $user = Auth::user();

        // Verify task belongs to intern
        if ($user->isIntern()) {
            $intern = $user->intern;
            if (!$intern || (int) $task->intern_id !== (int) $intern->id) {
                abort(403, 'Anda tidak memiliki akses ke tugas ini.');
            }
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'in_progress' && !$task->started_at) {
            $data['started_at'] = now();
        }

        $task->update($data);

        return back()->with('success', 'Status tugas diperbarui!');
    }

    // Task Assignments Index - Grouped View
    public function assignmentsIndex(Request $request)
    {
        $user = Auth::user();

        if (!$user->canManage()) {
            return redirect()->route('tasks.index');
        }

        $query = TaskAssignment::with(['assignedBy', 'tasks.intern.user'])
            ->withCount('tasks')
            ->having('tasks_count', '>', 0); // Hanya tampilkan yang masih ada task

        // Search
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by priority
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        $taskAssignments = $query->latest()->paginate(10);

        // Calculate statistics for each assignment
        foreach ($taskAssignments as $assignment) {
            $assignment->stats = $this->calculateAssignmentStats($assignment);
        }

        return view('task-assignments.index', compact('taskAssignments'));
    }

    // Task Assignment Detail with Statistics
    public function assignmentShow(TaskAssignment $taskAssignment)
    {
        $user = Auth::user();

        if (!$user->canManage()) {
            return redirect()->route('tasks.index');
        }

        $taskAssignment->load(['assignedBy', 'tasks.intern.user']);

        // Calculate detailed statistics
        $stats = $this->calculateAssignmentStats($taskAssignment);

        // Group tasks by status for easier display
        $tasksByStatus = [
            'completed' => $taskAssignment->tasks->where('status', 'completed'),
            'submitted' => $taskAssignment->tasks->where('status', 'submitted'),
            'in_progress' => $taskAssignment->tasks->where('status', 'in_progress'),
            'revision' => $taskAssignment->tasks->where('status', 'revision'),
            'pending' => $taskAssignment->tasks->where('status', 'pending'),
            'scheduled' => $taskAssignment->tasks->where('status', 'scheduled'),
        ];

        return view('task-assignments.show', compact('taskAssignment', 'stats', 'tasksByStatus'));
    }

    // Helper to calculate assignment statistics
    private function calculateAssignmentStats(TaskAssignment $assignment)
    {
        $tasks = $assignment->tasks;
        $total = $tasks->count();

        if ($total === 0) {
            return [
                'total' => 0,
                'completed' => 0,
                'completed_on_time' => 0,
                'completed_late' => 0,
                'submitted' => 0,
                'in_progress' => 0,
                'revision' => 0,
                'pending' => 0,
                'scheduled' => 0,
                'progress_percentage' => 0,
                'average_score' => 0,
            ];
        }

        $completed = $tasks->where('status', 'completed')->count();
        $completedOnTime = $tasks->where('status', 'completed')->where('is_late', false)->count();
        $completedLate = $tasks->where('status', 'completed')->where('is_late', true)->count();
        $submitted = $tasks->where('status', 'submitted')->count();
        $inProgress = $tasks->where('status', 'in_progress')->count();
        $revision = $tasks->where('status', 'revision')->count();
        $pending = $tasks->where('status', 'pending')->count();
        $scheduled = $tasks->where('status', 'scheduled')->count();

        $scores = $tasks->where('status', 'completed')->whereNotNull('score')->pluck('score');
        $averageScore = $scores->count() > 0 ? round($scores->avg(), 1) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'completed_on_time' => $completedOnTime,
            'completed_late' => $completedLate,
            'submitted' => $submitted,
            'in_progress' => $inProgress,
            'revision' => $revision,
            'pending' => $pending,
            'scheduled' => $scheduled,
            'progress_percentage' => round(($completed / $total) * 100),
            'average_score' => $averageScore,
        ];
    }
}
