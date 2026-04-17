<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Absensi NFC';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('student_user_id')
                    ->label('Siswa')
                    ->relationship(
                        name: 'student',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->whereHas('role', fn (Builder $q) => $q->where('slug', 'siswa'))
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn (): bool => auth()->user()?->isGuru() ?? false),
                DatePicker::make('attendance_date')
                    ->label('Tanggal')
                    ->required()
                    ->default(now())
                    ->disabled(fn (): bool => auth()->user()?->isGuru() ?? false),
                DateTimePicker::make('check_in_at')
                    ->label('Waktu Tap')
                    ->seconds(false)
                    ->disabled(fn (): bool => auth()->user()?->isGuru() ?? false),
                Select::make('status')
                    ->required()
                    ->options(Attendance::STATUS_OPTIONS),
                Select::make('source')
                    ->required()
                    ->options(Attendance::SOURCE_OPTIONS)
                    ->default('manual')
                    ->disabled(fn (): bool => auth()->user()?->isGuru() ?? false),
                Textarea::make('note')
                    ->label('Catatan')
                    ->rows(3),
                Select::make('approved_by_user_id')
                    ->label('Disetujui Oleh')
                    ->relationship('approvedBy', 'name')
                    ->searchable()
                    ->preload()
                    ->disabled(),
                DateTimePicker::make('approved_at')
                    ->label('Waktu Persetujuan')
                    ->seconds(false)
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.studentProfile.classroom.name')
                    ->label('Kelas')
                    ->placeholder('-')
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->badge(),
                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->label('Verifikator')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Attendance::STATUS_OPTIONS)
                    ->label('Status'),
                Tables\Filters\Filter::make('attendance_date')
                    ->form([
                        DatePicker::make('from')->label('Dari tanggal'),
                        DatePicker::make('until')->label('Sampai tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $builder, string $date): Builder => $builder->whereDate('attendance_date', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $builder, string $date): Builder => $builder->whereDate('attendance_date', '<=', $date)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with([
            'student.studentProfile.classroom',
            'approvedBy',
        ]);

        $user = auth()->user();

        if ($user?->isGuru()) {
            $query->whereHas('student.studentProfile.classroom', function (Builder $builder) use ($user) {
                $builder->where('homeroom_teacher_user_id', $user->id);
            });
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user?->isAdminTu() || $user?->isGuru();
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdminTu() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->isAdminTu()) {
            return true;
        }

        if ($user->isGuru() && $record instanceof Attendance) {
            return static::belongsToTeacherClass($record, $user->id);
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdminTu() ?? false;
    }

    protected static function belongsToTeacherClass(Attendance $attendance, int $teacherUserId): bool
    {
        return $attendance->student?->studentProfile?->classroom?->homeroom_teacher_user_id === $teacherUserId;
    }
}
