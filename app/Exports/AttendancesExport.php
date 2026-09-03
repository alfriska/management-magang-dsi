<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AttendancesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $internId;
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($internId = null, $startDate = null, $endDate = null, $status = null)
    {
        $this->internId = $internId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Attendance::with('intern.user');

        if ($this->internId) {
            $query->where('intern_id', $this->internId);
        }

        if ($this->startDate) {
            $query->whereDate('date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('date', '<=', $this->endDate);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Siswa',
            'NIS',
            'Tanggal',
            'Hari',
            'Jam Masuk',
            'Jam Pulang',
            'Status',
            'Alasan Terlambat',
            'Catatan',
            'Jarak (meter)',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->id,
            $attendance->intern->user->name ?? '-',
            $attendance->intern->nis ?? '-',
            $attendance->date->format('d/m/Y'),
            $this->getDayName($attendance->date),
            $attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : '-',
            $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : '-',
            $this->getStatusLabel($attendance->status),
            $attendance->late_reason ?? '-',
            $attendance->notes ?? '-',
            $attendance->distance_meters ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6']
                ],
            ],
        ];
    }

    private function getStatusLabel($status): string
    {
        return match($status) {
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Tidak Hadir',
            'sick' => 'Sakit',
            'permission' => 'Izin',
            default => $status,
        };
    }

    private function getDayName($date): string
    {
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        return $days[$date->format('l')] ?? $date->format('l');
    }
}
