<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'link',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Notification types
    const TYPE_TASK_ASSIGNED = 'task_assigned';
    const TYPE_TASK_DEADLINE = 'task_deadline';
    const TYPE_TASK_APPROVED = 'task_approved';
    const TYPE_TASK_REVISION = 'task_revision';
    const TYPE_TASK_SUBMITTED = 'task_submitted';
    const TYPE_NEW_INTERN_REGISTRATION = 'new_intern_registration';
    const TYPE_NEW_SUPERVISOR_REGISTRATION = 'new_supervisor_registration';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function getIconClassAttribute()
    {
        return match ($this->type) {
            self::TYPE_TASK_ASSIGNED => 'fas fa-tasks text-primary',
            self::TYPE_TASK_DEADLINE => 'fas fa-clock text-warning',
            self::TYPE_TASK_APPROVED => 'fas fa-check-circle text-success',
            self::TYPE_TASK_REVISION => 'fas fa-redo text-warning',
            self::TYPE_TASK_SUBMITTED => 'fas fa-paper-plane text-info',
            self::TYPE_NEW_INTERN_REGISTRATION => 'fas fa-user-plus text-amber-500',
            default => 'fas fa-bell text-secondary',
        };
    }

    public function getColorAttribute()
    {
        return match ($this->type) {
            self::TYPE_TASK_ASSIGNED => '#3b82f6',
            self::TYPE_TASK_DEADLINE => '#f59e0b',
            self::TYPE_TASK_APPROVED => '#22c55e',
            self::TYPE_TASK_REVISION => '#f97316',
            self::TYPE_TASK_SUBMITTED => '#06b6d4',
            self::TYPE_NEW_INTERN_REGISTRATION => '#f59e0b',
            default => '#6b7280',
        };
    }

    // Static helper to create notifications
    public static function notify($userId, $type, $title, $message, $link = null, $data = null)
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'data' => $data,
        ]);
    }
}
