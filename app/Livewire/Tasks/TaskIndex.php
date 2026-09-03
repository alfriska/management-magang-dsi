<?php

namespace App\Livewire\Tasks;

use App\Models\Intern;
use App\Models\Task;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Daftar Pekerjaan')]
class TaskIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $priority = '';
    public $intern_id = '';

    // Bulk action properties
    public $selectedTasks = [];
    public $selectAll = false;
    public $bulkAction = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'priority' => ['except' => ''],
        'intern_id' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatingStatus()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatingPriority()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatingInternId()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedTasks = $this->getFilteredQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedTasks = [];
        }
    }

    public function resetBulkSelection()
    {
        $this->selectedTasks = [];
        $this->selectAll = false;
        $this->bulkAction = '';
    }

    public function deleteTask($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        session()->flash('success', 'Tugas berhasil dihapus!');
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedTasks)) {
            session()->flash('error', 'Pilih minimal satu data terlebih dahulu!');
            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                $this->bulkDelete();
                break;
            case 'pending':
                $this->bulkUpdateStatus('pending');
                break;
            case 'in_progress':
                $this->bulkUpdateStatus('in_progress');
                break;
            case 'completed':
                $this->bulkUpdateStatus('completed');
                break;
            case 'priority_high':
                $this->bulkUpdatePriority('high');
                break;
            case 'priority_medium':
                $this->bulkUpdatePriority('medium');
                break;
            case 'priority_low':
                $this->bulkUpdatePriority('low');
                break;
            default:
                session()->flash('error', 'Pilih aksi yang valid!');
                return;
        }

        $this->resetBulkSelection();
    }

    public function bulkDelete()
    {
        // Load tasks individually so Observer is triggered for each deletion
        $tasks = Task::whereIn('id', $this->selectedTasks)->get();
        $count = 0;
        
        foreach ($tasks as $task) {
            $task->delete(); // This triggers TaskObserver::deleted()
            $count++;
        }
        
        session()->flash('success', "{$count} tugas berhasil dihapus!");
    }

    public function bulkUpdateStatus($status)
    {
        $statusLabels = [
            'pending' => 'Belum Mulai',
            'in_progress' => 'Dikerjakan',
            'completed' => 'Selesai',
        ];

        $updateData = ['status' => $status];

        // If completing, set completed_at
        if ($status === 'completed') {
            $updateData['completed_at'] = now();
        }

        $count = Task::whereIn('id', $this->selectedTasks)->update($updateData);
        $label = $statusLabels[$status] ?? $status;

        session()->flash('success', "{$count} tugas berhasil diubah ke status {$label}!");
    }

    public function bulkUpdatePriority($priority)
    {
        $priorityLabels = [
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            'low' => 'Rendah',
        ];

        $count = Task::whereIn('id', $this->selectedTasks)->update(['priority' => $priority]);
        $label = $priorityLabels[$priority] ?? $priority;

        session()->flash('success', "{$count} tugas berhasil diubah ke prioritas {$label}!");
    }

    private function getFilteredQuery()
    {
        $user = auth()->user();
        $query = Task::with(['intern.user', 'assignedBy']);

        // If user is intern, only show their tasks (exclude scheduled)
        if ($user->isIntern()) {
            $query->where('intern_id', $user->intern->id)
                  ->where('status', '!=', 'scheduled');
        }

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->priority) {
            $query->where('priority', $this->priority);
        }

        if ($this->intern_id && $user->canManage()) {
            $query->where('intern_id', $this->intern_id);
        }

        return $query;
    }

    public function render()
    {
        $tasks = $this->getFilteredQuery()->latest()->paginate(10);
        $interns = Intern::with('user')->where('status', 'active')->get();

        return view('livewire.tasks.task-index', compact('tasks', 'interns'));
    }
}
