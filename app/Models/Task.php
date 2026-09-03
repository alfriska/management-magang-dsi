<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_assignment_id',
        'title',
        'description',
        'intern_id',
        'assigned_by',
        'priority',
        'status',
        'deadline',
        'deadline_time',
        'start_date',
        'started_at',
        'completed_at',
        'submitted_at',
        'is_late',
        'submission_links',
        'submission_notes',
        'score',
        'admin_feedback',
        'approved_at',
    ];

    protected $casts = [
        'deadline' => 'date',
        'start_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_late' => 'boolean',
        'submission_links' => 'array',
    ];

    public function intern()
    {
        return $this->belongsTo(Intern::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function taskAssignment()
    {
        return $this->belongsTo(TaskAssignment::class);
    }

    public function assessment()
    {
        return $this->hasOne(Assessment::class);
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'success',
            default => 'secondary',
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'success',
            'submitted' => 'info',
            'in_progress' => 'primary',
            'revision' => 'warning',
            'pending' => 'secondary',
            'scheduled' => 'slate',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute()
    {
        $label = match($this->status) {
            'completed' => 'Disetujui',
            'submitted' => 'Menunggu Review',
            'in_progress' => 'Dikerjakan',
            'revision' => 'Perlu Revisi',
            'pending' => 'Belum Mulai',
            'scheduled' => 'Terjadwal',
            default => 'Unknown',
        };

        if ($this->status === 'completed' && $this->is_late) {
            $label .= ' (Terlambat)';
        }

        return $label;
    }

    public function getDeadlineDatetimeAttribute()
    {
        if ($this->deadline) {
            $time = $this->deadline_time ?? '23:59:59';
            return $this->deadline->setTimeFromTimeString($time);
        }
        return null;
    }

    public function getCompletionTimeHours()
    {
        if (!$this->started_at || !$this->completed_at) return null;
        return $this->started_at->diffInHours($this->completed_at);
    }

    public function isOverdue()
    {
        if (!$this->deadline || $this->status === 'completed') return false;
        $deadlineDatetime = $this->deadline_datetime;
        if ($deadlineDatetime) {
            return now()->isAfter($deadlineDatetime);
        }
        return $this->deadline->isPast();
    }

    // Check if task is scheduled for future
    public function isScheduled()
    {
        return $this->start_date && $this->start_date->isFuture();
    }

    // Submit task and check if late
    public function submit()
    {
        $now = now();
        $this->submitted_at = $now;
        $this->status = 'submitted';

        // Check if late
        $deadlineDatetime = $this->deadline_datetime;
        if ($deadlineDatetime && $now->isAfter($deadlineDatetime)) {
            $this->is_late = true;
        }

        $this->save();
        return $this;
    }

    public function approve($score, $feedback = null)
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->approved_at = now();
        $this->score = $score;
        $this->admin_feedback = $feedback;
        $this->save();
    }

    public function requestRevision($feedback)
    {
        $this->status = 'revision';
        $this->admin_feedback = $feedback;
        $this->save();
    }

    // Activate scheduled task
    public function activate()
    {
        if ($this->status === 'scheduled') {
            $this->status = 'pending';
            $this->save();
        }
        return $this;
    }
}
