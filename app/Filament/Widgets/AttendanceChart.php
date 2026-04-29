<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class AttendanceChart extends ChartWidget
{
    protected static ?string $heading = 'Rekap Kehadiran 7 Hari Terakhir';

    protected static ?int $sort = 1;

    protected static ?string $maxHeight = '280px';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $user = auth()->user();

        if (! $user || (! $user->isAdminTu() && ! $user->isGuru())) {
            return ['datasets' => [], 'labels' => []];
        }

        $period = CarbonPeriod::create(now()->subDays(6)->startOfDay(), now());
        $labels = [];
        $hadirData = [];
        $izinData = [];
        $sakitData = [];
        $alfaData = [];

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $labels[] = $date->translatedFormat('d M');

            $query = Attendance::whereDate('attendance_date', $dateStr);

            // If guru, scope to their homeroom class
            if ($user->isGuru()) {
                $query->whereHas('student.studentProfile.classroom', function ($q) use ($user) {
                    $q->where('homeroom_teacher_user_id', $user->id);
                });
            }

            $hadirData[] = (clone $query)->where('status', 'hadir')->count();
            $izinData[] = (clone $query)->where('status', 'izin')->count();
            $sakitData[] = (clone $query)->where('status', 'sakit')->count();
            $alfaData[] = (clone $query)->where('status', 'alfa')->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Hadir',
                    'data' => $hadirData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Izin',
                    'data' => $izinData,
                    'backgroundColor' => 'rgba(251, 191, 36, 0.8)',
                    'borderColor' => 'rgb(251, 191, 36)',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Sakit',
                    'data' => $sakitData,
                    'backgroundColor' => 'rgba(96, 165, 250, 0.8)',
                    'borderColor' => 'rgb(96, 165, 250)',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Alfa',
                    'data' => $alfaData,
                    'backgroundColor' => 'rgba(248, 113, 113, 0.8)',
                    'borderColor' => 'rgb(248, 113, 113)',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
