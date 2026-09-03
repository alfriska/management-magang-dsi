<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'created_by',
        'title',
        'content',
        'type',
        'period_start',
        'period_end',
        'status',
        'feedback',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function intern()
    {
        return $this->belongsTo(Intern::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan',
            'final' => 'Akhir',
            default => 'Lainnya',
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'reviewed' => 'success',
            'submitted' => 'primary',
            'draft' => 'secondary',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'reviewed' => 'Sudah Direview',
            'submitted' => 'Diajukan',
            'draft' => 'Draft',
            default => 'Unknown',
        };
    }
}
