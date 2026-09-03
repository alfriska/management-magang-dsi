<?php

namespace App\Livewire\Assessments;

use App\Models\Assessment;
use App\Models\Intern;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Daftar Penilaian')]
class AssessmentIndex extends Component
{
    use WithPagination;

    public $intern_id = '';

    // Bulk action properties
    public $selectedAssessments = [];
    public $selectAll = false;
    public $bulkAction = '';

    protected $queryString = [
        'intern_id' => ['except' => ''],
    ];

    public function updatingInternId()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedAssessments = $this->getFilteredQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedAssessments = [];
        }
    }

    public function resetBulkSelection()
    {
        $this->selectedAssessments = [];
        $this->selectAll = false;
        $this->bulkAction = '';
    }

    public function deleteAssessment($id)
    {
        $assessment = Assessment::findOrFail($id);
        $assessment->delete();

        session()->flash('success', 'Penilaian berhasil dihapus!');
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedAssessments)) {
            session()->flash('error', 'Pilih minimal satu data terlebih dahulu!');
            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                $this->bulkDelete();
                break;
            default:
                session()->flash('error', 'Pilih aksi yang valid!');
                return;
        }

        $this->resetBulkSelection();
    }

    public function bulkDelete()
    {
        $count = Assessment::whereIn('id', $this->selectedAssessments)->delete();
        session()->flash('success', "{$count} penilaian berhasil dihapus!");
    }

    private function getFilteredQuery()
    {
        $query = Assessment::with(['intern.user', 'task', 'assessedBy']);

        if ($this->intern_id) {
            $query->where('intern_id', $this->intern_id);
        }

        return $query;
    }

    public function render()
    {
        $assessments = $this->getFilteredQuery()->latest()->paginate(10);
        $interns = Intern::with('user')->where('status', 'active')->get();

        return view('livewire.assessments.assessment-index', compact('assessments', 'interns'));
    }
}
