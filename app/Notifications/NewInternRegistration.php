<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewInternRegistration extends Notification implements ShouldQueue
{
    use Queueable;

    protected $intern;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $intern)
    {
        $this->intern = $intern;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $internData = $this->intern->intern;
        
        return (new MailMessage)
            ->subject('Pendaftaran Magang Baru - ' . $this->intern->name)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Ada pendaftaran magang baru yang memerlukan persetujuan Anda.')
            ->line('')
            ->line('**Detail Pendaftar:**')
            ->line('• Nama: ' . $this->intern->name)
            ->line('• Email: ' . $this->intern->email)
            ->line('• Sekolah: ' . ($internData->school ?? '-'))
            ->line('• Jurusan: ' . ($internData->department ?? '-'))
            ->line('• Periode: ' . ($internData->start_date?->format('d M Y') ?? '-') . ' - ' . ($internData->end_date?->format('d M Y') ?? '-'))
            ->action('Lihat & Approve Pendaftaran', url('/interns?status=pending'))
            ->line('Silakan review dan approve pendaftaran ini melalui dashboard.')
            ->salutation('Terima kasih, InternHub');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_intern_registration',
            'user_id' => $this->intern->id,
            'intern_id' => $this->intern->intern?->id,
            'title' => 'Pendaftaran Magang Baru',
            'message' => $this->intern->name . ' telah mendaftar sebagai peserta magang dan menunggu persetujuan.',
            'url' => '/interns?status=pending',
        ];
    }
}
