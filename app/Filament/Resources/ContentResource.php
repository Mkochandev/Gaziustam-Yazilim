<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentResource\Pages;
use App\Models\Category;
use App\Models\Content;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ContentResource extends Resource
{
    protected static ?string $model = Content::class;

    protected static ?string $modelLabel = 'İçerik';

    protected static ?string $pluralModelLabel = 'Haber ve portföy';

    public static function canAccess(): bool
    {
        return Gate::allows('content');
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canAccess();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')->label('Tür')->options(['news' => 'Haber', 'article' => 'Makale', 'project' => 'Proje'])->required(),
            Select::make('source_locale')->label('Kaynak dil')->options(fn () => DB::table('locales')->where('is_active', true)->pluck('native_name', 'code')->all())->required(),
            Select::make('category')->label('Kategori')->options(fn () => Category::with('translations')->get()->mapWithKeys(fn ($c) => [$c->code => $c->translations->firstWhere('locale', 'tr')?->name ?? $c->code])->all()),
            FileUpload::make('cover')->label('Kapak')->disk('public')->visibility('public')->directory('covers')->image()->preventFilePathTampering()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(5120),
            FileUpload::make('gallery')->label('Galeri')->disk('public')->visibility('public')->directory('gallery')->multiple()->reorderable()->image()->preventFilePathTampering()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(5120),
            TextInput::make('project_url')->label('Canlı proje URL')->url()->rules(['nullable', 'url:http,https'])->maxLength(255),
            TextInput::make('client_display_name')->label('Müşteri adı (isteğe bağlı)')->maxLength(255),
            TagsInput::make('technologies')->label('Teknolojiler'),
            Toggle::make('is_concept')->label('Konsept'), Toggle::make('featured')->label('Öne çıkar'),
            TextInput::make('sort_order')->label('Sıra')->integer()->default(0)->required(),
            DateTimePicker::make('archived_at')->label('Arşive al'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->modifyQueryUsing(fn ($query) => $query->with('translations'))->columns([
            TextColumn::make('translations.title')->label('Başlık')->limitList(1),
            TextColumn::make('type')->label('Tür'), TextColumn::make('category')->label('Kategori'),
            TextColumn::make('languages')->label('Dil sürümleri')->state(fn (Content $record) => collect(['de', 'en', 'tr'])->map(function ($l) use ($record) {
                $t = $record->translations->firstWhere('locale', $l);

                return strtoupper($l).' '.(! $t ? 'eksik' : ($t->status === 'draft' ? 'taslak' : ($t->published_at?->isFuture() ? 'planlı' : 'yayında')));
            })->implode(' · ')),
        ])->recordActions([Action::make('translations')->label('Dil sürümleri')->url(fn (Content $record) => url("/admin/yazilar/$record->id/de")), EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListContents::route('/'), 'create' => Pages\CreateContent::route('/create'), 'edit' => Pages\EditContent::route('/{record}/edit')];
    }
}
