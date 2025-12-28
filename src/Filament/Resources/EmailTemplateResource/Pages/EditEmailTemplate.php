<?php

namespace Nphuonha\FilamentHelpdesk\Filament\Resources\EmailTemplateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Nphuonha\FilamentHelpdesk\Filament\Resources\EmailTemplateResource;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
