<?php

namespace App\Filament\Resources;

use App\Models\Media;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaResource extends AuthorizedResource
{
    protected static ?string $model = Media::class;

    protected static ?string $modelLabel = 'Medya';

    protected static ?string $pluralModelLabel = 'Medya';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('path')->label('Görsel')->disk('public')->visibility('public')
                ->directory('media')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(5120)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('ID'), ImageColumn::make('path')->disk('public')->label('Görsel'),
            TextColumn::make('path')->label('Dosya'), TextColumn::make('mime')->label('Tür'),
            TextColumn::make('size')->label('Bayt'), TextColumn::make('width')->label('Genişlik'),
        ])->recordActions([DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => MediaResource\Pages\ListMedia::route('/'), 'create' => MediaResource\Pages\CreateMedia::route('/create')];
    }
}
