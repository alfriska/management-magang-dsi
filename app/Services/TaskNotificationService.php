<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\Notification;
use App\Mail\TaskAssignedMail;
use App\Mail\TaskReminderMail;
use App\Mail\TaskStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TaskNotificationService
{
    /**
     * Send notification when a new task is assigned
     */
    public static function notifyTaskAssigned(Task $task): void
    {
        $intern = $task->intern;
        if (!$intern || !$intern->user) {
            return;
        }

        $user = $intern->user;

        // Create in-app notification
        Notification::notify(
            $user->id,
            Notification::TYPE_TASK_ASSIGNED,
            'Tugas Baru',
            "Anda mendapat tugas baru: {$task->title}",
            route('tasks.show', $task->id),
            ['task_id' => $task->id]
        );

        // Send email notification
        try {
            Mail::to($user->email)->queue(new TaskAssignedMail($task, $user));
            Log::info("Task assigned email sent to {$user->email} for task: {$task->title}");
        } catch (\Exception $e) {
            Log::error("Failed to send task assigned email: " . $e->getMessage());
        }
    }

    /**
     * Send deadline reminder email
     */
    public static function sendDeadlineReminder(Task $task, string $reminderType = 'tomorrow'): void
    {
        $intern = $task->intern;
        if (!$intern || !$intern->user) {
            return;
        }

        $user = $intern->user;

        // Create in-app notification
        $title = $reminderType === 'today' ? 'Deadline Hari Ini!' : 'Deadline Besok';
        $message = $reminderType === 'today'
            ? "Tugas \"{$task->title}\" harus diselesaikan hari ini!"
            : "Tugas \"{$task->title}\" deadline besok. Segera selesaikan!";

        Notification::notify(
            $user->id,
            Notification::TYPE_TASK_DEADLINE,
            $title,
            $message,
            route('tasks.show', $task->id),
            ['task_id' => $task->id, 'reminder_type' => $reminderType]
        );

        // Send email
        try {
            Mail::to($user->email)->queue(new TaskReminderMail($task, $user, $reminderType));
            Log::info("Deadline reminder ({$reminderType}) email sent to {$user->email} for task: {$task->title}");
        } catch (\Exception $e) {
            Log::error("Failed to send deadline reminder email: " . $e->getMessage());
        }
    }

    /**
     * Send notification when task status changes
     */
    public static function notifyTaskStatusChange(Task $task, string $statusType, ?User $notifyUser = null): void
    {
        // Determine who to notify
        $user = $notifyUser;

        if (!$user) {
            if ($statusType === 'submitted') {
                // Notify admin/pembimbing who assigned the task
                $user = $task->assignedBy;
            } else {
                // Notify intern
                $intern = $task->intern;
                $user = $intern?->user;
            }
        }

        if (!$user) {
            return;
        }

        // Create in-app notification
        $notificationType = match($statusType) {
            'approved' => Notification::TYPE_TASK_APPROVED,
            'revision' => Notification::TYPE_TASK_REVISION,
            'submitted' => Notification::TYPE_TASK_SUBMITTED,
            default => Notification::TYPE_TASK_APPROVED,
        };

        $title = match($statusType) {
            'approved' => 'Tugas Disetujui!',
            'revision' => 'Perlu Revisi',
            'submitted' => 'Tugas Dikirim',
            default => 'Update Tugas',
        };

        $message = match($statusType) {
            'approved' => "Tugas \"{$task->title}\" telah disetujui dengan nilai {$task->score}.",
            'revision' => "Tugas \"{$task->title}\" perlu direvisi. Cek feedback dari pembimbing.",
            'submitted' => "Tugas \"{$task->title}\" telah dikirim oleh intern untuk direview.",
            default => "Status tugas \"{$task->title}\" telah diperbarui.",
        };

        Notification::notify(
            $user->id,
            $notificationType,
            $title,
            $message,
            route('tasks.show', $task->id),
            ['task_id' => $task->id, 'status' => $statusType]
        );

        // Send email
        try {
            Mail::to($user->email)->queue(new TaskStatusMail($task, $user, $statusType));
            Log::info("Task status ({$statusType}) email sent to {$user->email} for task: {$task->title}");
        } catch (\Exception $e) {
            Log::error("Failed to send task status email: " . $e->getMessage());
        }
    }
}
