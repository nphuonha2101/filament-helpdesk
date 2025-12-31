<?php

namespace Nphuonha\FilamentHelpdesk;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Nphuonha\FilamentHelpdesk\Filament\Resources\EmailTemplateResource;
use Nphuonha\FilamentHelpdesk\Filament\Resources\TicketResource;

class HelpdeskPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-helpdesk';
    }

    public function register(Panel $panel): void
    {
        // dd('Plugin Registered');
        $panel
            ->resources([
                TicketResource::class,
                EmailTemplateResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
