<?php

namespace App\Filament\Student\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;

class StudentAttendanceChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Kehadiran';

    protected static ?int $sort = 1;

    protected static ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $studentId = auth()->id();

        if (! $studentId) {
            return ['datasets' => [], 'labels' => []];
        }

        $query = Attendance::query()->where('student_user_id', $studentId);
        $hadir = (clone $query)->where('status', 'hadir')->count();
        $izin = (clone $query)->where('status', 'izin')->count();
        $sakit = (clone $query)->where('status', 'sakit')->count();
        $alfa = (clone $query)->where('status', 'alfa')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Kehadiran',
                    'data' => [$hadir, $izin, $sakit, $alfa],
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.85)',
                        'rgba(251, 191, 36, 0.85)',
                        'rgba(96, 165, 250, 0.85)',
                        'rgba(248, 113, 113, 0.85)',
                    ],
                    'borderColor' => [
                        'rgb(16, 185, 129)',
                        'rgb(251, 191, 36)',
                        'rgb(96, 165, 250)',
                        'rgb(248, 113, 113)',
                    ],
                    'borderWidth' => 2,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => ['Hadir', 'Izin', 'Sakit', 'Alfa'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 16,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                    ],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
