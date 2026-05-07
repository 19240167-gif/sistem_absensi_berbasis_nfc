<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\StudentProfile;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileStudentController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! $user->isSiswa()) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'until' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->startOfMonth();

        $until = isset($validated['until'])
            ? Carbon::parse($validated['until'])->endOfDay()
            : now()->endOfMonth();

        $counts = Attendance::query()
            ->where('student_user_id', $user->id)
            ->whereDate('attendance_date', '>=', $from->toDateString())
            ->whereDate('attendance_date', '<=', $until->toDateString())
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $summary = [
            'hadir' => (int) ($counts['hadir'] ?? 0),
            'izin' => (int) ($counts['izin'] ?? 0),
            'sakit' => (int) ($counts['sakit'] ?? 0),
            'alfa' => (int) ($counts['alfa'] ?? 0),
        ];
        $summary['total'] = array_sum($summary);

        $profile = StudentProfile::query()
            ->with('classroom')
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'message' => 'Ringkasan absensi siswa.',
            'period' => [
                'from' => $from->toDateString(),
                'until' => $until->toDateString(),
            ],
            'summary' => $summary,
            'student' => [
                'name' => $user->name,
                'nis' => $profile?->nis,
                'classroom' => $profile?->classroom?->name,
            ],
        ]);
    }
}
