<?php

namespace App\Livewire\Attendances;

use App\Models\Attendance;
use App\Models\Intern;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Presensi Magang')]
class AttendanceIndex extends Component
{
    use WithPagination;

    public $date = '';
    public $month = '';
    public $status = '';
    public $intern_id = '';

    // Bulk action properties
    public $selectedAttendances = [];
    public $selectAll = false;
    public $bulkAction = '';

    protected $queryString = [
        'date' => ['except' => ''],
        'month' => ['except' => ''],
        'status' => ['except' => ''],
        'intern_id' => ['except' => ''],
    ];

    public function updatingDate()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatingMonth()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatingStatus()
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
            $this->selectedAttendances = $this->getFilteredQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedAttendances = [];
        }
    }

    public function resetBulkSelection()
    {
        $this->selectedAttendances = [];
        $this->selectAll = false;
        $this->bulkAction = '';
    }

    public function deleteAttendance($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        session()->flash('success', 'Presensi berhasil dihapus!');
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedAttendances)) {
            session()->flash('error', 'Pilih minimal satu data terlebih dahulu!');
            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                $this->bulkDelete();
                break;
            case 'present':
                $this->bulkUpdateStatus('present');
                break;
            case 'late':
                $this->bulkUpdateStatus('late');
                break;
            case 'absent':
                $this->bulkUpdateStatus('absent');
                break;
            case 'sick':
                $this->bulkUpdateStatus('sick');
                break;
            case 'permission':
                $this->bulkUpdateStatus('permission');
                break;
            default:
                session()->flash('error', 'Pilih aksi yang valid!');
                return;
        }

        $this->resetBulkSelection();
    }

    public function bulkDelete()
    {
        $count = Attendance::whereIn('id', $this->selectedAttendances)->delete();
        session()->flash('success', "{$count} presensi berhasil dihapus!");
    }

    public function bulkUpdateStatus($status)
    {
        $statusLabels = [
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Tidak Hadir',
            'sick' => 'Sakit',
            'permission' => 'Izin',
        ];

        $count = Attendance::whereIn('id', $this->selectedAttendances)->update(['status' => $status]);
        $label = $statusLabels[$status] ?? $status;

        session()->flash('success', "{$count} presensi berhasil diubah ke status {$label}!");
    }

    private function getFilteredQuery()
    {
        $user = auth()->user();
        $query = Attendance::with(['intern.user']);

        // If user is intern, only show their attendances
        if ($user->isIntern()) {
            $query->where('intern_id', $user->intern->id);
        }

        if ($this->date) {
            $query->whereDate('date', $this->date);
        }

        if ($this->month) {
            $query->whereYear('date', substr($this->month, 0, 4))
                  ->whereMonth('date', substr($this->month, 5, 2));
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->intern_id && $user->canManage()) {
            $query->where('intern_id', $this->intern_id);
        }

        return $query;
    }

    public function render()
    {
        $attendances = $this->getFilteredQuery()->latest('date')->paginate(10);
        $interns = Intern::with('user')->where('status', 'active')->get();

        return view('livewire.attendances.attendance-index', compact('attendances', 'interns'));
    }
}
