<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RfidTagResource\Pages;
use App\Models\RfidTag;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RfidTagResource extends Resource
{
    protected static ?string $model = RfidTag::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Absensi NFC';

    protected static ?string $navigationLabel = 'Kartu / Tag NFC';

    protected static ?string $modelLabel = 'Tag NFC';

    protected static ?string $pluralModelLabel = 'Kartu / Tag NFC';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) RfidTag::where('is_active', true)->count();
    }

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
                TextInput::make('uid')
                    ->label('Identifier NFC (Kartu/HP)')
                    ->required()
                    ->maxLength(64)
                    ->helperText('Isi UID kartu atau token NFC dari aplikasi HP siswa.')
                    ->unique(ignoreRecord: true),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->required(),
                DateTimePicker::make('assigned_at')
                    ->label('Tanggal Didaftarkan')
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uid')
                    ->label('Identifier NFC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.studentProfile.classroom.name')
                    ->label('Kelas')
                    ->placeholder('-'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_seen_at')
                    ->label('Tap Terakhir')
                    ->since()
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
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
            'index' => Pages\ListRfidTags::route('/'),
            'create' => Pages\CreateRfidTag::route('/create'),
            'edit' => Pages\EditRfidTag::route('/{record}/edit'),
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
