<?php

namespace App\Filament\Resources;

use App\Models\Locale;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LocaleResource extends AuthorizedResource
{
    protected static string $ability = 'manage';

    protected static ?string $model = Locale::class;

    protected static ?string $modelLabel = 'Dil';

    protected static ?string $pluralModelLabel = 'Diller';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('native_name')->label('Dil adı')->required(),
            TextInput::make('sort_order')->label('Sıra')->integer()->required(),
            Toggle::make('is_active')->label('Etkin'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort_order')->columns([
            TextColumn::make('code')->label('Kod'), TextColumn::make('native_name')->label('Dil'),
            IconColumn::make('is_active')->label('Etkin')->boolean(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => LocaleResource\Pages\ListLocales::route('/'), 'edit' => LocaleResource\Pages\EditLocale::route('/{record}/edit')];
    }
}
