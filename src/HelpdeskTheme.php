<?php

namespace Nphuonha\FilamentHelpdesk;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Assets\Theme;
use Filament\Support\Color;
use Filament\Support\Facades\FilamentAsset;

class HelpdeskTheme implements Plugin
{
    public function getId(): string
    {
        return 'filament-helpdesk-theme';
    }

    public function register(Panel $panel): void
    {
        FilamentAsset::register([
            Theme::make('filament-helpdesk', __DIR__ . '/../resources/dist/filament-helpdesk.css'),
        ]);

        $panel
            ->font('DM Sans')
            ->primaryColor(Color::Amber)
            ->secondaryColor(Color::Gray)
            ->warningColor(Color::Amber)
            ->dangerColor(Color::Rose)
            ->successColor(Color::Green)
            ->grayColor(Color::Gray)
            ->theme('filament-helpdesk');
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
