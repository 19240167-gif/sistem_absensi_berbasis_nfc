<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    public const STATUS_OPTIONS = [
        'hadir' => 'Hadir',
        'izin' => 'Izin',
        'sakit' => 'Sakit',
        'alfa' => 'Alfa',
    ];

    public const SOURCE_OPTIONS = [
        'nfc' => 'NFC',
        'manual' => 'Manual',
    ];

    protected $fillable = [
        'student_user_id',
        'attendance_date',
        'check_in_at',
        'status',
        'source',
        'note',
        'approved_by_user_id',
        'approved_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}
