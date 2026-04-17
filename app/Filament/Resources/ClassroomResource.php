<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassroomResource\Pages;
use App\Models\Classroom;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ClassroomResource extends Resource
{
    protected static ?string $model = Classroom::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Data Master Sekolah';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Rombel')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('Kode Rombel')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('grade_level')
                    ->label('Tingkat')
                    ->required()
                    ->maxLength(20),
                TextInput::make('academic_year')
                    ->label('Tahun Ajaran')
                    ->required()
                    ->maxLength(20)
                    ->placeholder('2026/2027'),
                Select::make('homeroom_teacher_user_id')
                    ->label('Wali Kelas')
                    ->relationship(
                        name: 'homeroomTeacher',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->whereHas('role', fn (Builder $q) => $q->where('slug', 'guru'))
                    )
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Rombel')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grade_level')
                    ->label('Tingkat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('academic_year')
                    ->label('Tahun Ajaran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('homeroomTeacher.name')
                    ->label('Wali Kelas')
                    ->placeholder('-')
                    ->searchable(),
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
            'index' => Pages\ListClassrooms::route('/'),
            'create' => Pages\CreateClassroom::route('/create'),
            'edit' => Pages\EditClassroom::route('/{record}/edit'),
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
