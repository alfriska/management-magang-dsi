<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'late_reason',
        'notes',
        'latitude',
        'longitude',
        'distance_meters',
        'proof_file',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
    ];

    public function intern()
    {
        return $this->belongsTo(Intern::class);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'present' => 'success',
            'late' => 'warning',
            'absent' => 'danger',
            'sick' => 'info',
            'permission' => 'primary',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Tidak Hadir',
            'sick' => 'Sakit',
            'permission' => 'Izin',
            default => 'Unknown',
        };
    }

    public function getWorkingHours()
    {
        if (!$this->check_in || !$this->check_out) return null;

        $checkIn = \Carbon\Carbon::parse($this->check_in);
        $checkOut = \Carbon\Carbon::parse($this->check_out);

        return $checkIn->diffInHours($checkOut);
    }
}
