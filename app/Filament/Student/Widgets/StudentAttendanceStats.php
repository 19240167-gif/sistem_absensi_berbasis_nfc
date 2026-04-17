<?php

namespace App\Filament\Student\Widgets;

use App\Models\Attendance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentAttendanceStats extends BaseWidget
{
    protected function getStats(): array
    {
        $studentId = auth()->id();

        if (! $studentId) {
            return [];
        }

        $query = Attendance::query()->where('student_user_id', $studentId);
        $total = (clone $query)->count();
        $hadir = (clone $query)->where('status', 'hadir')->count();
        $izin = (clone $query)->where('status', 'izin')->count();
        $sakit = (clone $query)->where('status', 'sakit')->count();
        $alfa = (clone $query)->where('status', 'alfa')->count();
        $persentase = $total > 0 ? round(($hadir / $total) * 100, 2) : 0;

        return [
            Stat::make('Total Presensi', (string) $total),
            Stat::make('Hadir', (string) $hadir)->color('success'),
            Stat::make('Izin / Sakit', (string) ($izin + $sakit))->color('warning'),
            Stat::make('Alfa', (string) $alfa)->color('danger'),
            Stat::make('Kehadiran', $persentase.'%')->color('info'),
        ];
    }
}
