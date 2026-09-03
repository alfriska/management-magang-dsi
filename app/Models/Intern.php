<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intern extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'school',
        'department',
        'phone',
        'address',
        'start_date',
        'end_date',
        'status',
        'supervisor_id',
        'certificate_number',
        'certificate_issued_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'certificate_issued_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function taskAssignments()
    {
        return $this->belongsToMany(TaskAssignment::class, 'task_assignment_interns');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function dailyReports(): HasMany
    {
       return $this->hasMany(DailyReport::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    // Task Statistics
    public function getCompletedTasksCount()
    {
        return $this->tasks()->where('status', 'completed')->count();
    }

    public function getCompletedOnTimeCount()
    {
        return $this->tasks()->where('status', 'completed')->where('is_late', false)->count();
    }

    public function getCompletedLateCount()
    {
        return $this->tasks()->where('status', 'completed')->where('is_late', true)->count();
    }

    public function getPendingTasksCount()
    {
        return $this->tasks()->whereIn('status', ['pending', 'in_progress'])->count();
    }

    public function getOverdueTasksCount()
    {
        return $this->tasks()
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', now())
            ->count();
    }

    // Get task statistics for charts
    public function getTaskStatistics()
    {
        return [
            'total' => $this->tasks()->count(),
            'completed_on_time' => $this->getCompletedOnTimeCount(),
            'completed_late' => $this->getCompletedLateCount(),
            'in_progress' => $this->tasks()->where('status', 'in_progress')->count(),
            'pending' => $this->tasks()->where('status', 'pending')->count(),
            'overdue' => $this->getOverdueTasksCount(),
        ];
    }

    public function getAttendancePercentage()
    {
        $total = $this->attendances()->count();
        if ($total === 0) return 0;
        
        $present = $this->attendances()->whereIn('status', ['present', 'late'])->count();
        return round(($present / $total) * 100, 1);
    }

    public function getAverageSpeed()
    {
        $completedTasks = $this->tasks()
            ->where('status', 'completed')
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->get();

        if ($completedTasks->isEmpty()) return 0;

        $totalSpeed = 0;
        foreach ($completedTasks as $task) {
            $estimatedHours = $task->estimated_hours ?: 8;
            $actualHours = $task->started_at->diffInHours($task->completed_at) ?: 1;
            $speed = ($estimatedHours / $actualHours) * 100;
            $totalSpeed += min($speed, 150); // Cap at 150%
        }

        return round($totalSpeed / $completedTasks->count(), 1);
    }

    public function getOverallScore()
    {
        $assessments = $this->assessments;
        if ($assessments->isEmpty()) return 0;

        $total = 0;
        foreach ($assessments as $assessment) {
            $total += ($assessment->quality_score + $assessment->speed_score + 
                      $assessment->initiative_score + $assessment->teamwork_score + 
                      $assessment->communication_score) / 5;
        }

        return round($total / $assessments->count(), 1);
    }

    // Get on-time submission rate (for GitHub-like visualization)
    public function getOnTimeRate()
    {
        $completed = $this->tasks()->where('status', 'completed')->count();
        if ($completed === 0) return 0;
        
        $onTime = $this->getCompletedOnTimeCount();
        return round(($onTime / $completed) * 100, 1);
    }
}
