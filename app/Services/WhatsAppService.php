<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim pesan WhatsApp lewat Fonnte.
     *
     * @return array{success: bool, message: string}
     */
    public function send(string $phone, string $message): array
    {
        $normalizedPhone = $this->normalizePhone($phone);

        if (!$normalizedPhone) {
            return [
                'success' => false,
                'message' => 'Nomor telepon tidak valid atau kosong.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => config('services.fonnte.token'),
            ])->asForm()->post(config('services.fonnte.url'), [
                'target' => $normalizedPhone,
                'message' => $message,
            ]);

            if ($response->successful() && ($response->json('status') === true || $response->json('status') === 'true')) {
                return [
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim.',
                ];
            }

            Log::warning('Fonnte WhatsApp gagal kirim', [
                'phone' => $normalizedPhone,
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Provider WhatsApp menolak permintaan: ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('Fonnte WhatsApp exception', [
                'phone' => $normalizedPhone,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghubungi provider WhatsApp: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Normalisasi nomor Indonesia ke format 62xxxxxxxxxx.
     * Mengembalikan null kalau nomor kosong/tidak valid.
     */
    public function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Buang semua karakter selain digit
        $digits = preg_replace('/\D/', '', $phone);

        if (empty($digits)) {
            return null;
        }

        // 08xxxxxxxxx -> 628xxxxxxxxx
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        // 8xxxxxxxxx (tanpa awalan) -> 628xxxxxxxxx
        if (!str_starts_with($digits, '62') && str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        // Validasi kasar: nomor Indonesia setelah normalisasi minimal 10 digit
        if (strlen($digits) < 10) {
            return null;
        }

        return $digits;
    }
}