<?php

namespace App\Filament\Resources\StudentProfileResource\Pages;

use App\Filament\Resources\StudentProfileResource;
use App\Models\Role;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentProfile extends CreateRecord
{
    protected static string $resource = StudentProfileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $studentName = $data['student_name'] ?? null;
        unset($data['student_name']);

        if ($studentName) {
            // Cari user dengan nama yang sama
            $user = User::where('name', $studentName)->first();

            if (!$user) {
                // Jika user belum ada, buat user baru dengan role siswa
                $studentRole = Role::where('slug', 'siswa')->first();
                $user = User::create([
                    'name' => $studentName,
                    'email' => strtolower(str_replace(' ', '.', $studentName)) . '@siswa.local',
                    'password' => bcrypt('password'),
                    'role_id' => $studentRole?->id,
                    'is_active' => true,
                ]);
            }

            $data['user_id'] = $user->id;
        }

        return $data;
    }
}
