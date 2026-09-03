<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\Notification;
use App\Services\TaskNotificationService;
use Illuminate\Console\Command;

class SendScheduledTaskNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:send-scheduled-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for tasks scheduled to start today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();
        
        // Find all scheduled tasks that should start today
        $tasks = Task::where('status', 'scheduled')
            ->whereDate('start_date', $today)
            ->with(['intern.user', 'assignedBy'])
            ->get();

        $count = 0;

        foreach ($tasks as $task) {
            // Update status to pending
            $task->update(['status' => 'pending']);

            // Send in-app notification
            if ($task->intern && $task->intern->user_id) {
                Notification::notify(
                    $task->intern->user_id,
                    Notification::TYPE_TASK_ASSIGNED,
                    'Tugas Baru: ' . $task->title,
                    'Anda mendapat tugas baru dari ' . ($task->assignedBy->name ?? 'Admin') . '. Deadline: ' . ($task->deadline ? $task->deadline->format('d M Y') : 'Tidak ada'),
                    route('tasks.show', $task),
                    ['task_id' => $task->id]
                );

                // Send email notification
                TaskNotificationService::notifyTaskAssigned($task);
            }

            $count++;
        }

        $this->info("Sent notifications for {$count} scheduled tasks.");

        return Command::SUCCESS;
    }
}
