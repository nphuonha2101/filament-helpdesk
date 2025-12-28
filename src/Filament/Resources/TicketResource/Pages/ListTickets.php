<?php

namespace Nphuonha\FilamentHelpdesk\Filament\Resources\TicketResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Nphuonha\FilamentHelpdesk\Filament\Resources\TicketResource;

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
