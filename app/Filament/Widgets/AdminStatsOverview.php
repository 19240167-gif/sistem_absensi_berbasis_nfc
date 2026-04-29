<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $user = auth()->user();

        if (! $user || (! $user->isAdminTu() && ! $user->isGuru())) {
            return [];
        }

        $totalSiswa = User::whereHas('role', fn (Builder $q) => $q->where('slug', 'siswa'))
            ->where('is_active', true)
            ->count();

        $totalGuru = User::whereHas('role', fn (Builder $q) => $q->where('slug', 'guru'))
            ->where('is_active', true)
            ->count();

        $todayQuery = Attendance::whereDate('attendance_date', today());
        $todayHadir = (clone $todayQuery)->where('status', 'hadir')->count();
        $todayTotal = (clone $todayQuery)->count();
        $todayAlfa = (clone $todayQuery)->where('status', 'alfa')->count();
        $todayIzinSakit = (clone $todayQuery)->whereIn('status', ['izin', 'sakit'])->count();

        $persentase = $totalSiswa > 0 ? round(($todayHadir / $totalSiswa) * 100, 1) : 0;

        return [
            Stat::make('Total Siswa Aktif', (string) $totalSiswa)
                ->description('Terdaftar di sistem')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Total Guru Aktif', (string) $totalGuru)
                ->description('Pengajar & wali kelas')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->chart([3, 2, 4, 3, 4, 2, 3]),

            Stat::make('Kehadiran Hari Ini', $persentase . '%')
                ->description($todayHadir . ' dari ' . $totalSiswa . ' siswa')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($persentase >= 80 ? 'success' : ($persentase >= 50 ? 'warning' : 'danger'))
                ->chart([65, 70, 80, 75, 90, 85, (int) $persentase]),

            Stat::make('Tidak Hadir Hari Ini', (string) ($todayAlfa + $todayIzinSakit))
                ->description('Alfa: ' . $todayAlfa . ' | Izin/Sakit: ' . $todayIzinSakit)
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($todayAlfa > 0 ? 'danger' : 'warning'),
        ];
    }
}
