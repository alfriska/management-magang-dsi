<?php

namespace App\Http\Controllers;

use App\Models\Intern;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Generate internship certificate PDF using DomPDF
     */
    public function generate(Intern $intern)
    {
        // Check if intern is eligible (completed)
        if ($intern->status !== 'completed') {
            return back()->with('error', 'Sertifikat hanya dapat dibuat untuk siswa magang yang sudah menyelesaikan masa magang.');
        }

        // Generate certificate number if not exists
        if (!$intern->certificate_number) {
            $year = Carbon::now()->format('Y');
            $id = str_pad($intern->id, 4, '0', STR_PAD_LEFT);
            $intern->update([
                'certificate_number' => "MG-DSI/{$year}/{$id}",
                'certificate_issued_at' => Carbon::now(),
            ]);
        }

        $data = [
            'intern' => $intern,
            'title' => 'Sertifikat Magang',
            'generatedAt' => Carbon::now()->format('d F Y'),
        ];

        $pdf = PDF::loadView('pdf.certificate', $data);
        $pdf->setPaper('a4', 'landscape');

        $filename = 'Sertifikat_' . str_replace(' ', '_', $intern->user->name) . '_' . Carbon::now()->format('Y-m-d') . '.pdf';

        return $pdf->stream($filename);
    }
}
