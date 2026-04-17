<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportCsv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('reports.attendances.export'), shouldOpenInNewTab: true)
                ->visible(fn (): bool => auth()->user()?->isAdminTu() ?? false),
            Actions\CreateAction::make()
                ->visible(fn (): bool => auth()->user()?->isAdminTu() ?? false),
        ];
    }
}
