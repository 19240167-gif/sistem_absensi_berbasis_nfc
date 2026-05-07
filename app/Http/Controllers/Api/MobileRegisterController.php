<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RfidTag;
use App\Models\StudentProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileRegisterController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_code' => ['required', 'string', 'max:255'],
            'token' => ['required', 'string', 'max:64'],
            'device_label' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:50'],
        ]);

        $studentCode = trim($validated['student_code']);

        $profile = StudentProfile::query()
            ->with('user')
            ->where('nis', $studentCode)
            ->orWhere('nisn', $studentCode)
            ->orWhereHas('user', fn ($query) => $query->where('email', $studentCode))
            ->first();

        if (! $profile || ! $profile->user || ! $profile->user->is_active || ! $profile->user->isSiswa()) {
            return response()->json([
                'message' => 'Siswa tidak ditemukan atau belum aktif.',
            ], 404);
        }

        $existing = RfidTag::where('uid', $validated['token'])->first();

        if ($existing && $existing->user_id !== $profile->user_id) {
            return response()->json([
                'message' => 'Token sudah dipakai oleh siswa lain.',
            ], 409);
        }

        RfidTag::updateOrCreate(
            ['user_id' => $profile->user_id],
            [
                'uid' => $validated['token'],
                'is_active' => true,
                'assigned_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Token berhasil didaftarkan.',
            'student_id' => $profile->user_id,
        ]);
    }
}
