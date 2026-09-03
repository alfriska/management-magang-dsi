<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Intern;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Assessment::with(['intern.user', 'task', 'assessedBy']);

        if ($request->filled('intern_id')) {
            $query->where('intern_id', $request->intern_id);
        }

        $assessments = $query->latest()->paginate(10);
        $interns = Cache::activeInterns(50);
        
        return view('assessments.index', compact('assessments', 'interns'));
    }

    public function create(Request $request)
    {
        $interns = Cache::activeInterns(50);
        $tasks = Task::with('intern.user')->latest()->limit(100)->get();
        
        $selectedIntern = null;
        $selectedTask = null;
        
        if ($request->filled('intern_id')) {
            $selectedIntern = Intern::find($request->intern_id);
            $tasks = Task::where('intern_id', $request->intern_id)->latest()->limit(50)->get();
        }
        
        if ($request->filled('task_id')) {
            $selectedTask = Task::find($request->task_id);
        }
        
        return view('assessments.create', compact('interns', 'tasks', 'selectedIntern', 'selectedTask'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'intern_id' => 'required|exists:interns,id',
            'task_id' => 'nullable|exists:tasks,id',
            'quality_score' => 'required|integer|min:0|max:100',
            'speed_score' => 'required|integer|min:0|max:100',
            'initiative_score' => 'required|integer|min:0|max:100',
            'teamwork_score' => 'required|integer|min:0|max:100',
            'communication_score' => 'required|integer|min:0|max:100',
            'strengths' => 'nullable|string',
            'improvements' => 'nullable|string',
            'comments' => 'nullable|string',
        ]);

        Assessment::create([
            ...$validated,
            'assessed_by' => auth()->id(),
        ]);

        return redirect()->route('assessments.index')
            ->with('success', 'Penilaian berhasil ditambahkan!');
    }

    public function show(Assessment $assessment)
    {
        $assessment->load(['intern.user', 'task', 'assessedBy']);
        return view('assessments.show', compact('assessment'));
    }

    public function edit(Assessment $assessment)
    {
        $interns = Cache::activeInterns(50);
        $tasks = Task::where('intern_id', $assessment->intern_id)->latest()->limit(50)->get();
        
        return view('assessments.edit', compact('assessment', 'interns', 'tasks'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'quality_score' => 'required|integer|min:0|max:100',
            'speed_score' => 'required|integer|min:0|max:100',
            'initiative_score' => 'required|integer|min:0|max:100',
            'teamwork_score' => 'required|integer|min:0|max:100',
            'communication_score' => 'required|integer|min:0|max:100',
            'strengths' => 'nullable|string',
            'improvements' => 'nullable|string',
            'comments' => 'nullable|string',
        ]);

        $assessment->update($validated);

        return redirect()->route('assessments.index')
            ->with('success', 'Penilaian berhasil diperbarui!');
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();

        return redirect()->route('assessments.index')
            ->with('success', 'Penilaian berhasil dihapus!');
    }
}
