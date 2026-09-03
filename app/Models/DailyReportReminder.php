<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'date',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'date' => 'date',
        'sent_at' => 'datetime',
    ];

    public function intern(): BelongsTo
    {
        return $this->belongsTo(Intern::class);
    }
}