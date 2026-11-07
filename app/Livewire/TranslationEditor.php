<?php

namespace App\Livewire;

use App\Models\Content;
use App\Services\ContentInput;
use App\Services\Publication;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Component;

class TranslationEditor extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions, InteractsWithSchemas;

    #[Locked]
    public int $contentId;

    #[Locked]
    public string $locale;

    public ?array $data = [];

    public function mount(int $contentId, string $locale): void
    {
        Gate::authorize('content');
        $this->contentId = $contentId;
        $this->locale = $locale;
        $translation = Content::findOrFail($contentId)->translations()->firstOrNew(['locale' => $locale]);
        $this->form->fill($translation->toArray() + ['status' => 'draft']);
    }

    public function form(Schema $schema): Schema
    {
        $isProject = Content::findOrFail($this->contentId)->type === 'project';

        return $schema->components([
            Group::make([
                TextInput::make('title')->label('Başlık')->required()->maxLength(255),
                Textarea::make('excerpt')->label('Özet')->maxLength(4000),
                RichEditor::make('body')->label('Gövde')
                    ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('articles')
                    ->fileAttachmentsVisibility('public')
                    ->fileAttachmentsAcceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->fileAttachmentsMaxSize(5120)
                    ->toolbarButtons(['bold', 'italic', 'h2', 'h3', 'bulletList', 'orderedList', 'link', 'attachFiles', 'undo', 'redo']),
                Textarea::make('problem')->label('Sorun')->visible($isProject),
                Textarea::make('solution')->label('Çözüm')->visible($isProject),
                Textarea::make('deliverables')->label('Teslimatlar')->visible($isProject),
                KeyValue::make('gallery_alts')->label('Galeri alt metinleri (dosya yolu → bu dilde açıklama)')->visible($isProject),
            ])->columnSpan(['default' => 1, 'lg' => 2]),
            Group::make([
                TextInput::make('slug')->label('Adres')->required()->maxLength(180),
                Select::make('status')->label('Durum')->options(['draft' => 'Taslak', 'published' => 'Yayında / planlı'])->required(),
                DateTimePicker::make('published_at')->label('Yayın tarihi (UTC)'),
                TextInput::make('cover_alt')->label('Kapak alt metni')->maxLength(255),
                TextInput::make('meta_title')->label('SEO başlığı')->maxLength(255),
                Textarea::make('meta_description')->label('SEO açıklaması')->maxLength(1000),
            ]),
        ])->columns(['default' => 1, 'lg' => 3])->statePath('data');
    }

    public function save(): void
    {
        Gate::authorize('content');
        Content::findOrFail($this->contentId);
        $input = Request::create('/', 'POST', $this->form->getState());
        $data = ContentInput::translation($input);
        if ($data['status'] === 'published') {
            if (! trim(strip_tags($data['body'] ?? ''))) {
                $this->addError('data.body', 'Yayın için gövde gerekli.');

                return;
            }
            $data['published_at'] ??= now();
        }
        app(Publication::class)->save($this->contentId, $this->locale, $data);
        session()->flash('notice', 'Dil sürümü kaydedildi.');
        $this->redirect("/admin/yazilar/{$this->contentId}/{$this->locale}");
    }

    public function render()
    {
        Gate::authorize('content');

        return view('livewire.translation-editor');
    }
}
