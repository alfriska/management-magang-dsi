<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TasksExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $internId;
    protected $status;

    public function __construct($internId = null, $status = null)
    {
        $this->internId = $internId;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Task::with(['intern.user', 'assignedBy']);

        if ($this->internId) {
            $query->where('intern_id', $this->internId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Judul',
            'Deskripsi',
            'Nama Siswa',
            'Prioritas',
            'Status',
            'Deadline',
            'Tanggal Submit',
            'Tanggal Selesai',
            'Tepat Waktu',
            'Nilai',
            'Feedback',
            'Dibuat Oleh',
            'Dibuat Pada',
        ];
    }

    public function map($task): array
    {
        return [
            $task->id,
            $task->title,
            strip_tags($task->description ?? '-'),
            $task->intern->user->name ?? '-',
            $this->getPriorityLabel($task->priority),
            $this->getStatusLabel($task->status),
            $task->deadline?->format('d/m/Y') ?? '-',
            $task->submitted_at?->format('d/m/Y H:i') ?? '-',
            $task->completed_at?->format('d/m/Y H:i') ?? '-',
            $task->is_late ? 'Terlambat' : ($task->status === 'completed' ? 'Tepat Waktu' : '-'),
            $task->score ?? '-',
            $task->admin_feedback ?? '-',
            $task->assignedBy->name ?? '-',
            $task->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F59E0B']
                ],
            ],
        ];
    }

    private function getStatusLabel($status): string
    {
        return match($status) {
            'pending' => 'Menunggu',
            'in_progress' => 'Dalam Proses',
            'submitted' => 'Disubmit',
            'revision' => 'Revisi',
            'completed' => 'Selesai',
            default => $status,
        };
    }

    private function getPriorityLabel($priority): string
    {
        return match($priority) {
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'urgent' => 'Mendesak',
            default => $priority ?? 'Sedang',
        };
    }
}
