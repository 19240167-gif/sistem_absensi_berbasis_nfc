<?php

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
