<?php

namespace App\Console\Commands;

use App\Jobs\SendDailyReportReminder;
use App\Models\DailyReport;
use App\Models\Intern;
use Illuminate\Console\Command;

class SendDailyReportReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily-report:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim reminder WhatsApp ke intern aktif yang belum membuat Daily Report hari ini';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = now();

        // Minggu tidak ada reminder sama sekali
        if ($today->isSunday()) {
            $this->info('Hari ini Minggu, reminder tidak dikirim.');
            return self::SUCCESS;
        }

        $todayDate = $today->toDateString();

        $this->info("Mengecek Daily Report intern aktif untuk tanggal {$todayDate}...");

        $activeInterns = Intern::where('status', 'active')->get();

        $this->info("Ditemukan {$activeInterns->count()} intern aktif.");

        $internIdsWithReport = DailyReport::whereDate('date', $todayDate)
            ->whereIn('intern_id', $activeInterns->pluck('id'))
            ->pluck('intern_id')
            ->toArray();

        $internsToRemind = $activeInterns->reject(function ($intern) use ($internIdsWithReport) {
            return in_array($intern->id, $internIdsWithReport);
        });

        $this->info("{$internsToRemind->count()} intern belum membuat Daily Report, mengirim reminder...");

        foreach ($internsToRemind as $intern) {
            SendDailyReportReminder::dispatch($intern->id, $todayDate);
            $this->line("  ✓ Reminder di-antrikan untuk: {$intern->user->name}");
        }

        $this->info('Selesai.');

        return self::SUCCESS;
    }
}