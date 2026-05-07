<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MobileAuthController extends Controller
{
    public function loginStudent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
        ]);

        $profile = StudentProfile::query()
            ->with(['user.role', 'classroom'])
            ->where('nis', $validated['nis'])
            ->whereDate('birth_date', $validated['birth_date'])
            ->first();

        if (! $profile || ! $profile->user || ! $profile->user->is_active || ! $profile->user->isSiswa()) {
            return response()->json([
                'message' => 'Login siswa gagal. Periksa NIS dan tanggal lahir.',
            ], 422);
        }

        $token = $profile->user->createToken('mobile-siswa')->plainTextToken;

        return response()->json([
            'message' => 'Login siswa berhasil.',
            'token' => $token,
            'role' => 'siswa',
            'user' => [
                'id' => $profile->user->id,
                'name' => $profile->user->name,
                'nis' => $profile->nis,
                'classroom' => $profile->classroom?->name,
            ],
        ]);
    }

    public function loginTeacher(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->with('role')
            ->where('email', $validated['email'])
            ->first();

        if (! $user || ! $user->is_active || ! $user->isGuru() || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Login guru gagal. Periksa email dan password.',
            ], 422);
        }

        $token = $user->createToken('mobile-guru')->plainTextToken;

        return response()->json([
            'message' => 'Login guru berhasil.',
            'token' => $token,
            'role' => 'guru',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }
}
