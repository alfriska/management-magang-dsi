<?php

namespace App\Livewire\Supervisors;

use App\Models\User;
use App\Models\Supervisor;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Daftar Pembimbing')]
class SupervisorIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    // Bulk action properties
    public $selectedSupervisors = [];
    public $selectAll = false;
    public $bulkAction = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
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

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedSupervisors = $this->getFilteredQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedSupervisors = [];
        }
    }

    public function resetBulkSelection()
    {
        $this->selectedSupervisors = [];
        $this->selectAll = false;
        $this->bulkAction = '';
    }

    public function approveSupervisor($id)
    {
        $supervisor = Supervisor::findOrFail($id);
        $supervisor->update(['status' => 'active']);
        
        session()->flash('success', 'Pendaftaran ' . $supervisor->user->name . ' berhasil disetujui!');
    }

    public function rejectSupervisor($id)
    {
        $supervisor = Supervisor::findOrFail($id);
        $user = $supervisor->user;
        $name = $user->name;
        $supervisor->delete();
        $user->delete();
        
        session()->flash('success', 'Pendaftaran ' . $name . ' ditolak dan dihapus.');
    }

    public function deleteSupervisor($id)
    {
        $supervisor = Supervisor::findOrFail($id);
        $user = $supervisor->user;

        // Check if supervisor has any interns assigned
        if ($user->supervisedInterns()->count() > 0) {
            session()->flash('error', 'Tidak dapat menghapus pembimbing karena masih memiliki siswa magang yang ditugaskan!');
            return;
        }

        $supervisor->delete();
        $user->delete();
        session()->flash('success', 'Pembimbing berhasil dihapus!');
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedSupervisors)) {
            session()->flash('error', 'Pilih minimal satu data terlebih dahulu!');
            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                $this->bulkDelete();
                break;
            case 'approve':
                $this->bulkApprove();
                break;
            case 'reject':
                $this->bulkReject();
                break;
            default:
                session()->flash('error', 'Pilih aksi yang valid!');
                return;
        }

        $this->resetBulkSelection();
    }

    public function bulkApprove()
    {
        $count = Supervisor::whereIn('id', $this->selectedSupervisors)
            ->where('status', 'pending')
            ->update(['status' => 'active']);

        session()->flash('success', "{$count} pendaftaran berhasil disetujui!");
    }

    public function bulkReject()
    {
        $supervisors = Supervisor::whereIn('id', $this->selectedSupervisors)
            ->where('status', 'pending')
            ->with('user')
            ->get();
        $count = $supervisors->count();

        foreach ($supervisors as $supervisor) {
            $user = $supervisor->user;
            $supervisor->delete();
            if ($user) {
                $user->delete();
            }
        }

        session()->flash('success', "{$count} pendaftaran ditolak dan dihapus!");
    }

    public function bulkDelete()
    {
        $supervisors = Supervisor::whereIn('id', $this->selectedSupervisors)
            ->with(['user' => function($q) {
                $q->withCount('supervisedInterns');
            }])
            ->get();
        $deleted = 0;
        $skipped = 0;

        foreach ($supervisors as $supervisor) {
            if ($supervisor->user && $supervisor->user->supervised_interns_count > 0) {
                $skipped++;
                continue;
            }
            $user = $supervisor->user;
            $supervisor->delete();
            if ($user) {
                $user->delete();
            }
            $deleted++;
        }

        if ($skipped > 0) {
            session()->flash('warning', "{$deleted} pembimbing dihapus, {$skipped} dilewati karena masih memiliki siswa magang.");
        } else {
            session()->flash('success', "{$deleted} pembimbing berhasil dihapus!");
        }
    }

    public function getPendingCountProperty()
    {
        return Supervisor::where('status', 'pending')->count();
    }

    private function getFilteredQuery()
    {
        $query = Supervisor::with(['user' => function($q) {
            $q->withCount('supervisedInterns');
        }]);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })->orWhere('institution', 'like', "%{$this->search}%")
              ->orWhere('nip', 'like', "%{$this->search}%");
        }

        return $query;
    }

    public function render()
    {
        $supervisors = $this->getFilteredQuery()->latest()->paginate(10);

        return view('livewire.supervisors.supervisor-index', compact('supervisors'));
    }
}
