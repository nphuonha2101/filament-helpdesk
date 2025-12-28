<?php

namespace Nphuonha\FilamentHelpdesk\Filament\Resources\EmailTemplateResource\Pages;

use Nphuonha\FilamentHelpdesk\Filament\Resources\EmailTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailTemplates extends ListRecords
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
