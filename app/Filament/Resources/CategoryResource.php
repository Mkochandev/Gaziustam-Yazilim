<?php

namespace App\Filament\Resources;

use App\Models\Category;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CategoryResource extends AuthorizedResource
{
    protected static ?string $model = Category::class;

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategoriler';

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')->label('Kalıcı kod')->required()->regex('/^[a-z0-9-]+$/')->maxLength(50)->unique(ignoreRecord: true)->disabledOn('edit'),
            Select::make('scope')->label('Kapsam')->options(['all' => 'Tümü', 'blog' => 'Blog', 'projects' => 'Portföy'])->required()->default('all'),
            Repeater::make('translations')->label('Dil sürümleri')->relationship()->schema([
                Select::make('locale')->label('Dil')->options(fn()=>\Illuminate\Support\Facades\DB::table('locales')->orderBy('sort_order')->pluck('native_name','code')->all())->required()->distinct(),
                TextInput::make('name')->label('Ad')->required()->maxLength(255),
                TextInput::make('slug')->label('Adres')->required()->regex('/^[a-z0-9-]+$/')->maxLength(180),
            ])->columns(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->modifyQueryUsing(fn ($query) => $query->with('translations'))->columns([
            TextColumn::make('code')->label('Kod'), TextColumn::make('translations.name')->label('Dil sürümleri'),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => CategoryResource\Pages\ListCategories::route('/'), 'create' => CategoryResource\Pages\CreateCategory::route('/create'), 'edit' => CategoryResource\Pages\EditCategory::route('/{record}/edit')];
    }
}
