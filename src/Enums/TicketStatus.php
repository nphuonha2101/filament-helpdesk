<?php

namespace Nphuonha\FilamentHelpdesk\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TicketStatus: string implements HasColor, HasLabel
{
    case Open = 'open';
    case Pending = 'pending';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Pending => 'Pending',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Open => 'success',
            self::Pending => 'warning',
            self::Resolved => 'info',
            self::Closed => 'gray',
        };
    }
}
