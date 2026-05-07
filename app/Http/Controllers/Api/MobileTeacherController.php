<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\StudentProfile;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileTeacherController extends Controller
{
    public function absences(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! $user->isGuru()) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        $validated = $request->validate([
            'date' => ['nullable', 'date'],
        ]);
        $targetDate = isset($validated['date']) ? Carbon::parse($validated['date']) : now();

        $students = StudentProfile::query()
            ->with(['user', 'classroom'])
            ->whereHas('classroom', fn ($query) => $query->where('homeroom_teacher_user_id', $user->id))
            ->get();

        if ($students->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada siswa di kelas wali.',
                'date' => $targetDate->toDateString(),
                'total_students' => 0,
                'total_absent' => 0,
                'absences' => [],
            ]);
        }

        $attendanceMap = Attendance::query()
            ->whereIn('student_user_id', $students->pluck('user_id'))
            ->whereDate('attendance_date', $targetDate->toDateString())
            ->get()
            ->keyBy('student_user_id');

        $absences = $students->map(function (StudentProfile $profile) use ($attendanceMap) {
            $attendance = $attendanceMap->get($profile->user_id);
            $status = $attendance?->status ?? 'belum_absen';

            if ($attendance && $attendance->status === 'hadir') {
                return null;
            }

            return [
                'student_id' => $profile->user_id,
                'student_name' => $profile->user?->name,
                'nis' => $profile->nis,
                'classroom' => $profile->classroom?->name,
                'status' => $status,
                'attendance_id' => $attendance?->id,
                'check_in_at' => $attendance?->check_in_at?->toDateTimeString(),
            ];
        })->filter()->values();

        return response()->json([
            'message' => 'Data siswa tidak hadir.',
            'date' => $targetDate->toDateString(),
            'total_students' => $students->count(),
            'total_absent' => $absences->count(),
            'absences' => $absences,
        ]);
    }
}
