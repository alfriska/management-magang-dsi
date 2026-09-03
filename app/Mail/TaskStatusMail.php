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

class TaskStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Task $task;
    public User $user;
    public string $statusType; // 'approved', 'revision', 'submitted'

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, User $user, string $statusType)
    {
        $this->task = $task;
        $this->user = $user;
        $this->statusType = $statusType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->statusType) {
            'approved' => '✅ Tugas Disetujui: ' . $this->task->title,
            'revision' => '🔄 Perlu Revisi: ' . $this->task->title,
            'submitted' => '📤 Tugas Dikirim: ' . $this->task->title,
            default => 'Update Tugas: ' . $this->task->title,
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.task-status',
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
