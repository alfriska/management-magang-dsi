<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Intern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Report::with(['intern.user', 'createdBy']);

        // If intern, only show their own reports
        if ($user->isIntern() && $user->intern) {
            $query->where('intern_id', $user->intern->id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('intern_id')) {
            $query->where('intern_id', $request->intern_id);
        }

        $reports = $query->latest()->paginate(10);
        $interns = Cache::activeInterns(50);

        return view('reports.index', compact('reports', 'interns'));
    }

    public function create()
    {
        $interns = Cache::activeInterns(50);
        return view('reports.create', compact('interns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'intern_id' => 'required|exists:interns,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:weekly,monthly,final',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        Report::create([
            ...$validated,
            'created_by' => auth()->id(),
            'status' => 'submitted',
        ]);

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dibuat!');
    }

    public function show(Report $report)
    {
        $report->load(['intern.user', 'createdBy']);
        return view('reports.show', compact('report'));
    }

    public function edit(Report $report)
    {
        $interns = Cache::activeInterns(50);
        return view('reports.edit', compact('report', 'interns'));
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:weekly,monthly,final',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'status' => 'required|in:draft,submitted,reviewed',
            'feedback' => 'nullable|string',
        ]);

        $report->update($validated);

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil diperbarui!');
    }

    public function destroy(Report $report)
    {
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dihapus!');
    }

    // Add feedback to report
    public function addFeedback(Request $request, Report $report)
    {
        $validated = $request->validate([
            'feedback' => 'required|string',
        ]);

        $report->update([
            'feedback' => $validated['feedback'],
            'status' => 'reviewed',
        ]);

        return back()->with('success', 'Feedback berhasil ditambahkan!');
    }

    /**
     * Generate and download PDF report for an intern
     */
    public function downloadInternReport(Intern $intern)
    {
        $intern->load(['user', 'supervisor', 'tasks', 'attendances', 'assessments']);

        // Calculate task statistics
        $taskStats = [
            'total' => $intern->tasks->count(),
            'completed' => $intern->tasks->where('status', 'completed')->count(),
            'in_progress' => $intern->tasks->where('status', 'in_progress')->count(),
            'pending' => $intern->tasks->where('status', 'pending')->count(),
            'revision' => $intern->tasks->where('status', 'revision')->count(),
            'on_time' => $intern->tasks->where('status', 'completed')->where('is_late', false)->count(),
            'late' => $intern->tasks->where('status', 'completed')->where('is_late', true)->count(),
            'average_score' => $intern->tasks->where('status', 'completed')->whereNotNull('score')->avg('score') ?? 0,
        ];

        // Calculate attendance statistics
        $attendanceStats = [
            'total' => $intern->attendances->count(),
            'present' => $intern->attendances->where('status', 'present')->count(),
            'late' => $intern->attendances->where('status', 'late')->count(),
            'absent' => $intern->attendances->where('status', 'absent')->count(),
            'sick' => $intern->attendances->where('status', 'sick')->count(),
            'permission' => $intern->attendances->where('status', 'permission')->count(),
        ];

        // Calculate attendance percentage
        if ($attendanceStats['total'] > 0) {
            $attendanceStats['percentage'] = round(
                (($attendanceStats['present'] + $attendanceStats['late']) / $attendanceStats['total']) * 100,
                1
            );
        } else {
            $attendanceStats['percentage'] = 0;
        }

        // Calculate assessment scores
        $assessmentStats = [
            'count' => $intern->assessments->count(),
            'quality' => round($intern->assessments->avg('quality_score') ?? 0, 1),
            'speed' => round($intern->assessments->avg('speed_score') ?? 0, 1),
            'initiative' => round($intern->assessments->avg('initiative_score') ?? 0, 1),
            'teamwork' => round($intern->assessments->avg('teamwork_score') ?? 0, 1),
            'communication' => round($intern->assessments->avg('communication_score') ?? 0, 1),
        ];

        // Calculate overall score
        $assessmentStats['overall'] = round(
            ($assessmentStats['quality'] + $assessmentStats['speed'] +
             $assessmentStats['initiative'] + $assessmentStats['teamwork'] +
             $assessmentStats['communication']) / 5,
            1
        );

        // Get recent tasks (last 10)
        $recentTasks = $intern->tasks()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get recent attendances (last 10)
        $recentAttendances = $intern->attendances()
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        // Calculate internship duration
        $startDate = $intern->start_date;
        $endDate = $intern->end_date ?? Carbon::now();
        $duration = $startDate->diffInDays($endDate);
        $daysCompleted = $startDate->diffInDays(Carbon::now());
        $progress = min(100, round(($daysCompleted / max(1, $duration)) * 100, 1));

        // Prepare avatar URL
        $avatarUrl = null;
        if ($intern->user->avatar) {
            $avatar = $intern->user->avatar;
            
            // Check if avatar is an external URL (Google OAuth, etc.)
            if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
                try {
                    // Download external image and convert to base64
                    $imageContent = @file_get_contents($avatar);
                    if ($imageContent !== false) {
                        // Detect MIME type from content
                        $finfo = new \finfo(FILEINFO_MIME_TYPE);
                        $mimeType = $finfo->buffer($imageContent);
                        
                        // Only accept image MIME types
                        if (str_starts_with($mimeType, 'image/')) {
                            $avatarUrl = 'data:' . $mimeType . ';base64,' . base64_encode($imageContent);
                        }
                    }
                } catch (\Exception $e) {
                    // If failed to download, leave avatarUrl as null (will use placeholder)
                    $avatarUrl = null;
                }
            } else {
                // Local file - use storage_path for direct file access (storage/app/public/avatars/)
                $avatarPath = storage_path('app/public/avatars/' . $avatar);
                if (file_exists($avatarPath)) {
                    $extension = strtolower(pathinfo($avatarPath, PATHINFO_EXTENSION));
                    // Map common extensions to MIME types
                    $mimeTypes = [
                        'jpg' => 'jpeg',
                        'jpeg' => 'jpeg',
                        'png' => 'png',
                        'gif' => 'gif',
                        'webp' => 'webp',
                    ];
                    $mimeType = $mimeTypes[$extension] ?? $extension;
                    $avatarUrl = 'data:image/' . $mimeType . ';base64,' . base64_encode(file_get_contents($avatarPath));
                }
            }
        }

        $data = [
            'intern' => $intern,
            'taskStats' => $taskStats,
            'attendanceStats' => $attendanceStats,
            'assessmentStats' => $assessmentStats,
            'recentTasks' => $recentTasks,
            'recentAttendances' => $recentAttendances,
            'duration' => $duration,
            'daysCompleted' => $daysCompleted,
            'progress' => $progress,
            'avatarUrl' => $avatarUrl,
            'generatedAt' => Carbon::now()->format('d F Y H:i'),
        ];

        $pdf = PDF::loadView('reports.intern-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Laporan_' . str_replace(' ', '_', $intern->user->name) . '_' . Carbon::now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
