<?php

namespace App\Jobs;

use App\Models\DailyReportReminder;
use App\Models\Intern;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDailyReportReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $internId,
        public string $date
    ) {}

    public function handle(WhatsAppService $whatsAppService): void
    {
        $intern = Intern::with('user')->find($this->internId);

        if (!$intern) {
            Log::warning('SendDailyReportReminder: intern tidak ditemukan', ['intern_id' => $this->internId]);
            return;
        }

        $reminder = DailyReportReminder::firstOrCreate(
            ['intern_id' => $this->internId, 'date' => $this->date],
            ['status' => 'pending']
        );

        // Cegah kirim dobel: kalau sudah pernah sukses terkirim, jangan kirim lagi
        if ($reminder->status === 'sent') {
            return;
        }

        if (empty($intern->phone)) {
            $reminder->update([
                'status' => 'failed',
                'error_message' => 'Nomor WhatsApp intern kosong.',
            ]);
            Log::warning('SendDailyReportReminder: nomor kosong', ['intern_id' => $this->internId]);
            return;
        }

        $name = $intern->user->name ?? 'Intern';
        $message = "Halo {$name}, ini adalah pengingat bahwa Daily Report untuk hari ini belum diisi. Silakan mengisi Daily Report melalui InternHub. Terima kasih.";

        $result = $whatsAppService->send($intern->phone, $message);

        if ($result['success']) {
            $reminder->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } else {
            $reminder->update([
                'status' => 'failed',
                'error_message' => $result['message'],
            ]);
            Log::warning('SendDailyReportReminder: gagal kirim', [
                'intern_id' => $this->internId,
                'error' => $result['message'],
            ]);
        }
    }
}