<?php

namespace App\Exports;

use App\Models\Intern;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InternsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $status;

    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function collection()
    {
        $query = Intern::with(['user', 'supervisor', 'tasks', 'attendances', 'assessments']);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Email',
            'NIS',
            'Sekolah',
            'Jurusan',
            'No. Telepon',
            'Alamat',
            'Pembimbing',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Status',
            'Total Tugas',
            'Tugas Selesai',
            'Tingkat Kehadiran (%)',
            'Skor Rata-rata',
        ];
    }

    public function map($intern): array
    {
        $totalTasks = $intern->tasks->count();
        $completedTasks = $intern->tasks->where('status', 'completed')->count();

        $totalAttendance = $intern->attendances->count();
        $presentCount = $intern->attendances->whereIn('status', ['present', 'late'])->count();
        $attendanceRate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : 0;

        $avgScore = $intern->tasks->where('status', 'completed')->whereNotNull('score')->avg('score') ?? 0;

        return [
            $intern->id,
            $intern->user->name ?? '-',
            $intern->user->email ?? '-',
            $intern->nis ?? '-',
            $intern->school ?? '-',
            $intern->department ?? '-',
            $intern->phone ?? '-',
            $intern->address ?? '-',
            $intern->supervisor->name ?? '-',
            $intern->start_date?->format('d/m/Y') ?? '-',
            $intern->end_date?->format('d/m/Y') ?? '-',
            $this->getStatusLabel($intern->status),
            $totalTasks,
            $completedTasks,
            $attendanceRate,
            round($avgScore, 1),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '10B981']
                ],
            ],
        ];
    }

    private function getStatusLabel($status): string
    {
        return match($status) {
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $status,
        };
    }
}
