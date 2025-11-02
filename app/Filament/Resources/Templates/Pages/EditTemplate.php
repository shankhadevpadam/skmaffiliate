<?php

namespace App\Filament\Resources\Templates\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Pboivin\FilamentPeek\Pages\Actions\PreviewAction;
use App\Filament\Resources\Templates\TemplateResource;
use Pboivin\FilamentPeek\Pages\Concerns\HasPreviewModal;

class EditTemplate extends EditRecord
{
    use HasPreviewModal;
    
    protected static string $resource = TemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PreviewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getPreviewModalView(): ?string
    {
        return 'filament.peek.email-preview';
    }

    protected function getPreviewModalDataRecordKey(): ?string
    {
        return 'template';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
