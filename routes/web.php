<?php

use App\Http\Controllers\AttendanceReportExportController;
use App\Http\Controllers\Auth\DemoLoginController;
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

Route::get('/demo-login', [DemoLoginController::class, 'index'])->name('demo.login');
Route::post('/demo-login/{user}', [DemoLoginController::class, 'login'])->name('demo.login.post');

Route::get('/', function () {
    if (app()->environment('local') || env('DEMO_AUTH')) {
        if (! auth()->check()) {
            return redirect()->route('demo.login');
        }
    }

    return redirect('/admin');
});

Route::middleware(['auth', 'role:admin_tu,guru'])->group(function () {
    Route::get('/kiosk-mode', KioskMode::class)->name('kiosk.mode');
});

Route::middleware(['auth', 'role:admin_tu'])->group(function () {
    Route::get('/reports/attendances/export', AttendanceReportExportController::class)
        ->name('reports.attendances.export');
});
