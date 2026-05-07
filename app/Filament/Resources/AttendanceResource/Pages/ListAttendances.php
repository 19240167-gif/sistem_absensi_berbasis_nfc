<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-text')
                ->form([
                    Select::make('classroom')
                        ->label('Kelas')
                        ->options([
                            'X TE' => 'X TE',
                            'XI TEI' => 'XI TEI',
                            'XII TEI' => 'XII TEI',
                        ])
                        ->required(),
                    DatePicker::make('attendance_date')
                        ->label('Tanggal Rekap')
                        ->default(today())
                        ->required(),
                ])
                ->action(fn (array $data) => redirect(route('reports.attendances.export', [
                    'type' => 'pdf',
                    'classroom' => $data['classroom'],
                    'attendance_date' => $data['attendance_date'],
                ])))
                ->visible(fn (): bool => auth()->user()?->isAdminTu() ?? false),
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
