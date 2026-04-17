<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'profile_photo_path',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function rfidTag(): HasOne
    {
        return $this->hasOne(RfidTag::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_user_id');
    }

    public function approvedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'approved_by_user_id');
    }

    public function hasRole(string $slug): bool
    {
        return $this->role?->slug === $slug;
    }

    public function isAdminTu(): bool
    {
        return $this->hasRole('admin_tu');
    }

    public function isGuru(): bool
    {
        return $this->hasRole('guru');
    }

    public function isSiswa(): bool
    {
        return $this->hasRole('siswa');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active || ! $this->role) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => $this->isAdminTu() || $this->isGuru(),
            'student' => $this->isSiswa(),
            default => false,
        };
    }
}
