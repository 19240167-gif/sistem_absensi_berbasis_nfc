<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\RfidTag;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NfcScanController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $deviceKey = config('app.nfc_device_key');

        if ($deviceKey && $request->header('X-Device-Key') !== $deviceKey) {
            return response()->json([
                'message' => 'Unauthorized device key.',
            ], 401);
        }

        $validated = $request->validate([
            'uid' => ['nullable', 'string', 'max:64', 'required_without:token'],
            'token' => ['nullable', 'string', 'max:64', 'required_without:uid'],
            'scanned_at' => ['nullable', 'date'],
        ]);

        $identifier = trim((string) ($validated['uid'] ?? $validated['token'] ?? ''));

        $tag = RfidTag::query()
            ->with(['user.studentProfile.classroom'])
            ->whereRaw('LOWER(uid) = ?', [mb_strtolower($identifier)])
            ->where('is_active', true)
            ->first();

        if (! $tag || ! $tag->user || ! $tag->user->isSiswa()) {
            return response()->json([
                'message' => 'Identifier is not registered to an active student account.',
                'identifier' => $identifier,
            ], 404);
        }

        $scannedAt = isset($validated['scanned_at']) ? Carbon::parse($validated['scanned_at']) : now();

        $attendance = DB::transaction(function () use ($tag, $scannedAt) {
            $attendance = Attendance::query()->firstOrNew([
                'student_user_id' => $tag->user_id,
                'attendance_date' => $scannedAt->toDateString(),
            ]);

            if (! $attendance->exists) {
                $attendance->status = 'hadir';
                $attendance->source = 'nfc';
            }

            $attendance->check_in_at = $scannedAt;
            $attendance->save();

            $tag->update([
                'last_seen_at' => $scannedAt,
            ]);

            return $attendance;
        });

        $student = $tag->user;
        $photoPath = $student->studentProfile?->photo_path ?? $student->profile_photo_path;

        $scanEntry = [
            'student_name' => $student->name,
            'classroom' => $student->studentProfile?->classroom?->name,
            'photo_url' => $photoPath ? Storage::disk('public')->url($photoPath) : null,
            'status' => $attendance->status,
            'check_in_at' => optional($attendance->check_in_at)->format('H:i:s'),
            'scanned_at' => $scannedAt->toDateTimeString(),
        ];

        Cache::put('kiosk.latest_scan', $scanEntry, now()->addMinutes(5));

        // Push to recent scans list (max 10, LIFO) for kiosk timeline
        $recentScans = Cache::get('kiosk.recent_scans', []);
        array_unshift($recentScans, $scanEntry);
        $recentScans = array_slice($recentScans, 0, 10);
        Cache::put('kiosk.recent_scans', $recentScans, now()->addMinutes(30));

        return response()->json([
            'message' => 'NFC tap processed successfully.',
            'data' => [
                'identifier' => $identifier,
                'attendance_id' => $attendance->id,
                'student_name' => $student->name,
                'status' => $attendance->status,
                'attendance_date' => $attendance->attendance_date?->toDateString(),
                'check_in_at' => optional($attendance->check_in_at)->toDateTimeString(),
            ],
        ]);
    }
}
