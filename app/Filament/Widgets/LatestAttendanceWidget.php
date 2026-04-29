<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestAttendanceWidget extends BaseWidget
{
    protected static ?string $heading = 'Aktivitas Absensi Terbaru';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = auth()->user();

        $query = Attendance::query()
            ->with(['student.studentProfile.classroom'])
            ->latest('check_in_at')
            ->whereDate('attendance_date', today())
            ->limit(10);

        if ($user?->isGuru()) {
            $query->whereHas('student.studentProfile.classroom', function ($q) use ($user) {
                $q->where('homeroom_teacher_user_id', $user->id);
            });
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->icon('heroicon-m-user'),
                Tables\Columns\TextColumn::make('student.studentProfile.classroom.name')
                    ->label('Kelas')
                    ->placeholder('-')
                    ->badge()
                    ->color('gray'),
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
                    ->dateTime('H:i:s')
                    ->placeholder('-')
                    ->icon('heroicon-m-clock'),
                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nfc' => 'info',
                        default => 'gray',
                    }),
            ])
            ->paginated(false);
    }
}
