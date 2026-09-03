<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'phone',
        'address',
        'institution',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function interns()
    {
        return $this->hasMany(Intern::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
