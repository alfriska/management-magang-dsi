<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'assigned_by',
        'priority',
        'deadline',
        'deadline_time',
        'start_date',
        'assign_to_all',
    ];

    protected $casts = [
        'deadline' => 'date',
        'start_date' => 'date',
        'assign_to_all' => 'boolean',
    ];

    // Relationships
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function interns()
    {
        return $this->belongsToMany(Intern::class, 'task_assignment_interns');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Helper methods
    public function getDeadlineDatetimeAttribute()
    {
        if ($this->deadline) {
            $time = $this->deadline_time ?? '23:59:59';
            return $this->deadline->setTimeFromTimeString($time);
        }
        return null;
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'secondary',
            default => 'secondary',
        };
    }

    // Get task statistics
    public function getStatistics()
    {
        $tasks = $this->tasks;
        return [
            'total' => $tasks->count(),
            'completed_on_time' => $tasks->where('status', 'completed')->where('is_late', false)->count(),
            'completed_late' => $tasks->where('status', 'completed')->where('is_late', true)->count(),
            'pending' => $tasks->whereIn('status', ['pending', 'in_progress'])->count(),
        ];
    }

    /**
     * Delete all TaskAssignments that have no tasks
     * Call this method after bulk task deletions if observer doesn't trigger
     * 
     * @return int Number of orphaned assignments deleted
     */
    public static function cleanupOrphaned(): int
    {
        $orphaned = self::doesntHave('tasks')->get();
        $count = 0;
        
        foreach ($orphaned as $assignment) {
            // This cascade deletes pivot table entries too
            $assignment->delete();
            $count++;
        }
        
        return $count;
    }
}
