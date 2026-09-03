<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Task $task;
    public User $user;
    public string $reminderType; // 'today' or 'tomorrow'

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, User $user, string $reminderType = 'tomorrow')
    {
        $this->task = $task;
        $this->user = $user;
        $this->reminderType = $reminderType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $urgency = $this->reminderType === 'today' ? '⚠️ HARI INI' : '⏰ Besok';
        return new Envelope(
            subject: $urgency . ' Deadline: ' . $this->task->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.task-reminder',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
