<?php

namespace App\Filament\Resources\ContentResource\Pages;

use App\Filament\Resources\ContentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContent extends CreateRecord
{
    protected static string $resource = ContentResource::class;

    protected function getRedirectUrl(): string
    {
        return url('/admin/yazilar/'.$this->record->id.'/'.$this->record->source_locale);
    }
}
