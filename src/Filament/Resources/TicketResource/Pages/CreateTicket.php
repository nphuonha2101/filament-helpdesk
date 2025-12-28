<?php

namespace Nphuonha\FilamentHelpdesk\Filament\Resources\TicketResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Nphuonha\FilamentHelpdesk\Filament\Resources\TicketResource;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uuid'] = (string) Str::uuid();

        return $data;
    }
}
