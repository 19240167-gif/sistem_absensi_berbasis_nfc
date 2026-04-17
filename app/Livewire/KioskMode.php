<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class KioskMode extends Component
{
    public array $latestScan = [];

    public function mount(): void
    {
        $this->refreshLatestScan();
    }

    public function refreshLatestScan(): void
    {
        $this->latestScan = Cache::get('kiosk.latest_scan', [
            'student_name' => null,
            'classroom' => null,
            'photo_url' => null,
            'status' => null,
            'check_in_at' => null,
            'scanned_at' => null,
        ]);
    }

    public function render()
    {
        return view('livewire.kiosk-mode');
    }
}
