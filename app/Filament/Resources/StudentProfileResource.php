<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentProfileResource\Pages;
use App\Models\Classroom;
use App\Models\RfidTag;
use App\Models\Role;
use App\Models\StudentProfile;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StudentProfileResource extends Resource
{
    protected static ?string $model = StudentProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Data Master Sekolah';

    protected static ?string $navigationLabel = 'Profil Siswa';

    protected static ?string $modelLabel = 'Profil Siswa';

    protected static ?string $pluralModelLabel = 'Profil Siswa';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('student_name')
                    ->label('Nama Siswa')
                    ->required()
<<<<<<< HEAD
                    ->unique(ignoreRecord: true)
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nama Siswa')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'email'),
                        TextInput::make('password')
                            ->password()
                            ->required()
                            ->minLength(6),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        $roleId = Role::where('slug', 'siswa')->value('id');

                        if (! $roleId) {
                            throw ValidationException::withMessages([
                                'user_id' => 'Role siswa belum tersedia. Jalankan seeder role terlebih dahulu.',
                            ]);
                        }

                        $user = User::create([
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'password' => Hash::make($data['password']),
                            'role_id' => $roleId,
                            'is_active' => true,
                        ]);

                        return $user->id;
                    }),
=======
                    ->maxLength(255)
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($record) => $record?->user?->name),
>>>>>>> fa091304 (Merubah di bagian create siswa)
                Select::make('classroom_id')
                    ->label('Kelas')
                    ->options(Classroom::query()->orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('nisn')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('nis')
                    ->label('NIS')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->required(),
                FileUpload::make('photo_path')
                    ->label('Foto Siswa')
                    ->directory('students')
                    ->disk('public')
                    ->image()
                    ->imageEditor(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('classroom.name')
                    ->label('Kelas')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nisn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nis')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('classroom_id')
                    ->relationship('classroom', 'name')
                    ->label('Kelas'),
            ])
            ->actions([
                Tables\Actions\Action::make('mappingNfc')
                    ->label('Mapping NFC')
                    ->icon('heroicon-o-credit-card')
                    ->visible(fn (): bool => auth()->user()?->isAdminTu() ?? false)
                    ->fillForm(function (StudentProfile $record): array {
                        $tag = $record->user?->rfidTag;

                        return [
                            'uid' => $tag?->uid,
                            'is_active' => $tag?->is_active ?? true,
                        ];
                    })
                    ->form([
                        TextInput::make('uid')
                            ->label('Identifier NFC (Kartu/HP)')
                            ->required()
                            ->maxLength(64)
                            ->helperText('Isi UID kartu atau token NFC dari aplikasi HP.'),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->action(function (StudentProfile $record, array $data): void {
                        $user = $record->user;

                        if (! $user) {
                            throw ValidationException::withMessages([
                                'uid' => 'Akun siswa belum tersedia untuk mapping NFC.',
                            ]);
                        }

                        $existing = RfidTag::where('uid', $data['uid'])->first();

                        if ($existing && $existing->user_id !== $user->id) {
                            throw ValidationException::withMessages([
                                'uid' => 'Identifier NFC sudah dipakai oleh siswa lain.',
                            ]);
                        }

                        RfidTag::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'uid' => $data['uid'],
                                'is_active' => $data['is_active'] ?? true,
                                'assigned_at' => now(),
                            ]
                        );
                    }),
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
            'index' => Pages\ListStudentProfiles::route('/'),
            'create' => Pages\CreateStudentProfile::route('/create'),
            'edit' => Pages\EditStudentProfile::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdminTu() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdminTu() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isAdminTu() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdminTu() ?? false;
    }
}
