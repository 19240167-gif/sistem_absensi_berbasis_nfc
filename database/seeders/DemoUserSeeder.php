<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\RfidTag;
use App\Models\Role;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin_tu')->firstOrFail();
        $guruRole = Role::where('slug', 'guru')->firstOrFail();
        $siswaRole = Role::where('slug', 'siswa')->firstOrFail();

        User::updateOrCreate(
            ['email' => 'admin.tu@sekolah.test'],
            [
                'name' => 'Admin Tata Usaha',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );

        $guru = User::updateOrCreate(
            ['email' => 'guru@sekolah.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role_id' => $guruRole->id,
                'is_active' => true,
            ]
        );

        $siswa = User::updateOrCreate(
            ['email' => 'siswa@sekolah.test'],
            [
                'name' => 'Siti Aminah',
                'password' => Hash::make('password'),
                'role_id' => $siswaRole->id,
                'is_active' => true,
            ]
        );

        TeacherProfile::updateOrCreate(
            ['user_id' => $guru->id],
            [
                'nip' => '198012012010011001',
                'phone' => '081234567890',
            ]
        );

        $classroom = Classroom::updateOrCreate(
            ['code' => 'X-IPA-1'],
            [
                'name' => 'X IPA 1',
                'grade_level' => '10',
                'academic_year' => '2026/2027',
                'homeroom_teacher_user_id' => $guru->id,
            ]
        );

        StudentProfile::updateOrCreate(
            ['user_id' => $siswa->id],
            [
                'classroom_id' => $classroom->id,
                'nisn' => '0012345678',
                'nis' => 'S001',
                'gender' => 'P',
            ]
        );

        RfidTag::updateOrCreate(
            ['uid' => '04A1B2C3D4'],
            [
                'user_id' => $siswa->id,
                'is_active' => true,
                'assigned_at' => now(),
            ]
        );

        Attendance::updateOrCreate(
            [
                'student_user_id' => $siswa->id,
                'attendance_date' => now()->toDateString(),
            ],
            [
                'check_in_at' => now()->subMinutes(5),
                'status' => 'hadir',
                'source' => 'nfc',
                'approved_by_user_id' => $guru->id,
                'approved_at' => now(),
            ]
        );
    }
}
