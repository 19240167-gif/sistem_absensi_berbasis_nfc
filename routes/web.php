<?php

use App\Http\Controllers\AttendanceReportExportController;
use App\Livewire\KioskMode;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/admin');

Route::middleware(['auth', 'role:admin_tu,guru'])->group(function () {
    Route::get('/kiosk-mode', KioskMode::class)->name('kiosk.mode');
});

Route::middleware(['auth', 'role:admin_tu'])->group(function () {
    Route::get('/reports/attendances/export', AttendanceReportExportController::class)
        ->name('reports.attendances.export');
});
