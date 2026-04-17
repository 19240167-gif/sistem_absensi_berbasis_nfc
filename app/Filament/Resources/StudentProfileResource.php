<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentProfileResource\Pages;
use App\Models\StudentProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StudentProfileResource extends Resource
{
    protected static ?string $model = StudentProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Data Master Sekolah';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Akun Siswa')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->whereHas('role', fn (Builder $q) => $q->where('slug', 'siswa'))
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('classroom_id')
                    ->label('Rombel')
                    ->relationship('classroom', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('nisn')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('nis')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir'),
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
                    ->label('Rombel')
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
                    ->label('Rombel'),
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
