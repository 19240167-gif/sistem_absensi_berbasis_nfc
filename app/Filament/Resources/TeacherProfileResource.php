<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherProfileResource\Pages;
use App\Models\TeacherProfile;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TeacherProfileResource extends Resource
{
    protected static ?string $model = TeacherProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Data Master Sekolah';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Akun Guru')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->whereHas('role', fn (Builder $q) => $q->where('slug', 'guru'))
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('nip')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('phone')
                    ->label('No. Telepon')
                    ->maxLength(255),
                Textarea::make('address')
                    ->label('Alamat')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Guru')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('homeroomClassrooms_count')
                    ->label('Jumlah Kelas Wali')
                    ->counts('homeroomClassrooms'),
            ])
            ->filters([
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
            'index' => Pages\ListTeacherProfiles::route('/'),
            'create' => Pages\CreateTeacherProfile::route('/create'),
            'edit' => Pages\EditTeacherProfile::route('/{record}/edit'),
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
