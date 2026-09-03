<?php

namespace App\Http\Controllers;

use App\Exports\InternsExport;
use App\Exports\AttendancesExport;
use App\Exports\TasksExport;
use App\Imports\InternsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ExportImportController extends Controller
{
    /**
     * Export Interns Data
     */
    public function exportInterns(Request $request)
    {
        $status = $request->get('status');
        $filename = 'Data_Peserta_Magang_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new InternsExport($status), $filename);
    }

    /**
     * Export Attendances Data
     */
    public function exportAttendances(Request $request)
    {
        $internId = $request->get('intern_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');

        $filename = 'Data_Presensi_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new AttendancesExport($internId, $startDate, $endDate, $status),
            $filename
        );
    }

    /**
     * Export Tasks Data
     */
    public function exportTasks(Request $request)
    {
        $internId = $request->get('intern_id');
        $status = $request->get('status');

        $filename = 'Data_Tugas_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new TasksExport($internId, $status), $filename);
    }

    /**
     * Show Import Form
     */
    public function showImportForm()
    {
        return view('imports.interns');
    }

    /**
     * Import Interns from Excel
     */
    public function importInterns(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        $supervisorId = $request->get('supervisor_id');
        $import = new InternsImport($supervisorId);

        try {
            Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $failures = $import->failures();

            if ($failures->count() > 0) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                }

                return back()->with([
                    'warning' => "Import selesai dengan beberapa error. {$imported} data berhasil diimport.",
                    'import_errors' => $errorMessages,
                ]);
            }

            return back()->with('success', "{$imported} data peserta magang berhasil diimport!");

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * Download Import Template
     */
    public function downloadTemplate()
    {
        $templatePath = public_path('templates/template_import_interns.xlsx');

        // If template doesn't exist, create it
        if (!file_exists($templatePath)) {
            $this->createImportTemplate();
        }

        return response()->download($templatePath, 'Template_Import_Peserta_Magang.xlsx');
    }

    /**
     * Create Import Template File
     */
    private function createImportTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers precisely matching the image
        $headers = [
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
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Example data precisely matching the image
        $example = [
            '1', // Example ID
            'Sabil Murti',
            'isabilmurti@gmail.com',
            '1234',
            'SMK N 9 Semarang',
            'PPLG',
            '0882003427575',
            'Jl. Bukit Cemara Permai IV, No. DN-28, Meteseh, Tembalang.',
            'Budi Santoso', // Supervisor Name
            '05/01/2026',
            '05/04/2026',
            'Aktif',
        ];

        $sheet->fromArray($example, null, 'A2');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '10B981']
            ],
        ];

        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);

        // Auto-size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }


        // Save
        $templateDir = public_path('templates');
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0755, true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($templateDir . '/template_import_interns.xlsx');
    }
}
