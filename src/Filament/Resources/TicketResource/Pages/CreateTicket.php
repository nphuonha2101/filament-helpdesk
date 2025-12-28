<?php

namespace Nphuonha\FilamentHelpdesk\Filament\Resources\TicketResource\Pages;

use Nphuonha\FilamentHelpdesk\Filament\Resources\TicketResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uuid'] = (string) Str::uuid();
        
        return $data;
    }
}
