<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\TaskNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:send-reminders {--type=all : Type of reminder (today, tomorrow, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deadline reminder emails for tasks due today or tomorrow';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->option('type');
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $this->info('Starting to send task reminders...');

        // Tasks due TODAY
        if ($type === 'all' || $type === 'today') {
            $tasksDueToday = Task::whereDate('deadline', $today)
                ->whereNotIn('status', ['completed', 'submitted'])
                ->with(['intern.user'])
                ->get();

            $this->info("Found {$tasksDueToday->count()} tasks due today.");

            foreach ($tasksDueToday as $task) {
                TaskNotificationService::sendDeadlineReminder($task, 'today');
                $this->line("  ✓ Reminder sent for: {$task->title}");
            }
        }

        // Tasks due TOMORROW
        if ($type === 'all' || $type === 'tomorrow') {
            $tasksDueTomorrow = Task::whereDate('deadline', $tomorrow)
                ->whereNotIn('status', ['completed', 'submitted'])
                ->with(['intern.user'])
                ->get();

            $this->info("Found {$tasksDueTomorrow->count()} tasks due tomorrow.");

            foreach ($tasksDueTomorrow as $task) {
                TaskNotificationService::sendDeadlineReminder($task, 'tomorrow');
                $this->line("  ✓ Reminder sent for: {$task->title}");
            }
        }

        $this->info('Task reminders sent successfully!');

        return self::SUCCESS;
    }
}
