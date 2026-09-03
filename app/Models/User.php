<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'google_id',
        'provider',
        'google2fa_secret',
        'google2fa_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function intern()
    {
        return $this->hasOne(Intern::class);
    }

    public function supervisedInterns()
    {
        return $this->hasMany(Intern::class, 'supervisor_id');
    }

    public function supervisor()
    {
        return $this->hasOne(Supervisor::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPembimbing()
    {
        return $this->role === 'pembimbing';
    }

    public function isIntern()
    {
        return $this->role === 'intern';
    }

    public function canManage()
    {
        return in_array($this->role, ['admin', 'pembimbing']);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }
}
