<?php

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileRegisterController;
use App\Http\Controllers\Api\MobileStudentController;
use App\Http\Controllers\Api\MobileTeacherController;
use App\Http\Controllers\Api\NfcScanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/nfc/tap', [NfcScanController::class, 'store'])
    ->middleware('throttle:120,1')
    ->name('api.nfc.tap');

Route::post('/nfc/phone-tap', [NfcScanController::class, 'store'])
    ->middleware('throttle:120,1')
    ->name('api.nfc.phone-tap');

Route::prefix('mobile')->group(function () {
    Route::post('/login/student', [MobileAuthController::class, 'loginStudent'])
        ->middleware('throttle:30,1');

    Route::post('/login/teacher', [MobileAuthController::class, 'loginTeacher'])
        ->middleware('throttle:30,1');

    Route::post('/register', [MobileRegisterController::class, 'store'])
        ->middleware(['auth:sanctum', 'throttle:10,1']);

    Route::middleware('auth:sanctum')->get('/teacher/absences', [MobileTeacherController::class, 'absences']);

    Route::middleware('auth:sanctum')->get('/student/summary', [MobileStudentController::class, 'summary']);
});
