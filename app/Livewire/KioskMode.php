<?php

namespace App\Livewire;

use App\Models\Attendance;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class KioskMode extends Component
{
    public array $latestScan = [];

    public array $recentScans = [];

    public string $currentTime = '';

    public string $currentDate = '';

    public array $todayStats = [
        'hadir' => 0,
        'izin' => 0,
        'sakit' => 0,
        'alfa' => 0,
        'total' => 0,
    ];

    public function mount(): void
    {
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $this->latestScan = Cache::get('kiosk.latest_scan', [
            'student_name' => null,
            'classroom' => null,
            'photo_url' => null,
            'status' => null,
            'check_in_at' => null,
            'scanned_at' => null,
        ]);

        $this->recentScans = Cache::get('kiosk.recent_scans', []);

        $this->currentTime = now()->format('H:i:s');
        $this->currentDate = now()->translatedFormat('l, d F Y');

        $todayQuery = Attendance::whereDate('attendance_date', today());
        $this->todayStats = [
            'hadir' => (clone $todayQuery)->where('status', 'hadir')->count(),
            'izin' => (clone $todayQuery)->where('status', 'izin')->count(),
            'sakit' => (clone $todayQuery)->where('status', 'sakit')->count(),
            'alfa' => (clone $todayQuery)->where('status', 'alfa')->count(),
            'total' => (clone $todayQuery)->count(),
        ];
    }

    public function render()
    {
        return view('livewire.kiosk-mode');
    }
}
