<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TaskAssignment;

class CleanupOrphanedTaskAssignments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:orphaned-tasks {--force : Force delete tanpa konfirmasi}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Hapus task assignments yang tidak memiliki task (orphaned groups)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Cari task assignments tanpa task
        $orphaned = TaskAssignment::doesntHave('tasks')->get();

        if ($orphaned->isEmpty()) {
            $this->info('✓ Tidak ada task group kosong untuk dihapus.');
            return Command::SUCCESS;
        }

        $count = $orphaned->count();
        $this->warn("Ditemukan {$count} task group kosong:");
        
        foreach ($orphaned as $assignment) {
            $this->line("  - {$assignment->title} (dibuat: {$assignment->created_at->format('d M Y')})");
        }

        if (!$this->option('force')) {
            if (!$this->confirm("\nYakin ingin menghapus {$count} task group kosong ini?")) {
                $this->info('Dibatalkan.');
                return Command::SUCCESS;
            }
        }

        // Hapus semua orphaned task assignments menggunakan method model
        // Ini memastikan cascade delete di pivot table juga berjalan
        $deleted = TaskAssignment::cleanupOrphaned();

        $this->info("✓ Berhasil menghapus {$deleted} task group kosong.");
        return Command::SUCCESS;
    }
}
