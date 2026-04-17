<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::upsert([
            [
                'name' => 'Admin TU',
                'slug' => 'admin_tu',
                'description' => 'Superadmin pengelola master data dan laporan absensi sekolah.',
            ],
            [
                'name' => 'Guru',
                'slug' => 'guru',
                'description' => 'Pemantau absensi kelas dan verifikasi status kehadiran siswa.',
            ],
            [
                'name' => 'Siswa',
                'slug' => 'siswa',
                'description' => 'Pengguna untuk melihat riwayat dan statistik kehadiran pribadi.',
            ],
        ], ['slug'], ['name', 'description']);
    }
}
