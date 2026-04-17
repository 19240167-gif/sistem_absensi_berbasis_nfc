<?php

namespace App\Filament\Student\Widgets;

use App\Models\Attendance;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class StudentAttendanceHistory extends BaseWidget
{
    protected static ?string $heading = 'Riwayat Absensi Pribadi';

    public function table(Table $table): Table
    {
        $studentId = auth()->id();

        return $table
            ->query(
                Attendance::query()
                    ->where('student_user_id', $studentId ?? 0)
                    ->latest('attendance_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('attendance_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'izin', 'sakit' => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => Attendance::STATUS_OPTIONS[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('check_in_at')
                    ->label('Jam Tap')
                    ->dateTime('H:i')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('note')
                    ->label('Catatan')
                    ->limit(40)
                    ->placeholder('-'),
            ]);
    }
}
