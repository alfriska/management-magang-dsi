<?php

namespace App\Livewire\Interns;

use App\Models\Intern;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Daftar Anggota Magang')]
class InternIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    // Bulk action properties
    public $selectedInterns = [];
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
            $this->selectedInterns = $this->getFilteredQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedInterns = [];
        }
    }

    public function resetBulkSelection()
    {
        $this->selectedInterns = [];
        $this->selectAll = false;
        $this->bulkAction = '';
    }

    public function deleteIntern($id)
    {
        $intern = Intern::findOrFail($id);
        $user = $intern->user;
        $intern->delete();
        $user->delete();

        session()->flash('success', 'Anggota magang berhasil dihapus!');
    }

    public function approveIntern($id)
    {
        $intern = Intern::findOrFail($id);
        $intern->update(['status' => 'active']);

        session()->flash('success', 'Pendaftaran ' . $intern->user->name . ' berhasil disetujui!');
    }

    public function rejectIntern($id)
    {
        $intern = Intern::findOrFail($id);
        $user = $intern->user;
        $name = $user->name;
        $intern->delete();
        $user->delete();

        session()->flash('success', 'Pendaftaran ' . $name . ' ditolak dan dihapus.');
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedInterns)) {
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
            case 'activate':
                $this->bulkUpdateStatus('active');
                break;
            case 'complete':
                $this->bulkUpdateStatus('completed');
                break;
            case 'cancel':
                $this->bulkUpdateStatus('cancelled');
                break;
            default:
                session()->flash('error', 'Pilih aksi yang valid!');
                return;
        }

        $this->resetBulkSelection();
    }

    public function bulkApprove()
    {
        $count = Intern::whereIn('id', $this->selectedInterns)
            ->where('status', 'pending')
            ->update(['status' => 'active']);

        session()->flash('success', "{$count} pendaftaran berhasil disetujui!");
    }

    public function bulkReject()
    {
        $interns = Intern::whereIn('id', $this->selectedInterns)
            ->where('status', 'pending')
            ->get();
        $count = $interns->count();

        foreach ($interns as $intern) {
            $user = $intern->user;
            $intern->delete();
            if ($user) {
                $user->delete();
            }
        }

        session()->flash('success', "{$count} pendaftaran ditolak dan dihapus!");
    }

    public function bulkDelete()
    {
        $interns = Intern::whereIn('id', $this->selectedInterns)->get();
        $count = $interns->count();

        foreach ($interns as $intern) {
            $user = $intern->user;
            $intern->delete();
            if ($user) {
                $user->delete();
            }
        }

        session()->flash('success', "{$count} anggota magang berhasil dihapus!");
    }

    public function bulkUpdateStatus($status)
    {
        $statusLabels = [
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $count = Intern::whereIn('id', $this->selectedInterns)->update(['status' => $status]);
        $label = $statusLabels[$status] ?? $status;

        session()->flash('success', "{$count} anggota magang berhasil diubah ke status {$label}!");
    }

    public function getPendingCountProperty()
    {
        return Intern::where('status', 'pending')->count();
    }

    private function getFilteredQuery()
    {
        $query = Intern::with(['user', 'supervisor']);

        if ($this->search) {
            $search = $this->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('school', 'like', "%{$search}%")
                ->orWhere('department', 'like', "%{$search}%");
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function render()
    {
        $interns = $this->getFilteredQuery()->latest()->paginate(10);

        return view('livewire.interns.intern-index', compact('interns'));
    }
}
