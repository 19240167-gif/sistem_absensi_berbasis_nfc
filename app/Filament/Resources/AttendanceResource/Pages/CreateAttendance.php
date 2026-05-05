<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['source'] = $data['source'] ?? 'manual';

        $user = auth()->user();

        if ($user?->isAdminTu() || $user?->isGuru()) {
            $data['approved_by_user_id'] = $user->id;
            $data['approved_at'] = now();
        }

        return $data;
    }
}
