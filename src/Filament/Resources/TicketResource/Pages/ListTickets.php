<?php

namespace Nphuonha\FilamentHelpdesk\Filament\Resources\TicketResource\Pages;

use Nphuonha\FilamentHelpdesk\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
