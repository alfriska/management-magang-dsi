<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'task_id',
        'assessed_by',
        'quality_score',
        'speed_score',
        'initiative_score',
        'teamwork_score',
        'communication_score',
        'strengths',
        'improvements',
        'comments',
    ];

    public function intern()
    {
        return $this->belongsTo(Intern::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function assessedBy()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function getAverageScoreAttribute()
    {
        return round(($this->quality_score + $this->speed_score + 
                     $this->initiative_score + $this->teamwork_score + 
                     $this->communication_score) / 5, 1);
    }

    public function getGradeAttribute()
    {
        $avg = $this->average_score;
        return match(true) {
            $avg >= 90 => 'A',
            $avg >= 80 => 'B',
            $avg >= 70 => 'C',
            $avg >= 60 => 'D',
            default => 'E',
        };
    }

    public function getGradeColorAttribute()
    {
        return match($this->grade) {
            'A' => 'success',
            'B' => 'primary',
            'C' => 'warning',
            'D' => 'danger',
            'E' => 'dark',
            default => 'secondary',
        };
    }
}
