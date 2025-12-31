<?php

namespace Workbench\App\Providers;

use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Support\ServiceProvider;
use Nphuonha\FilamentHelpdesk\HelpdeskPlugin;

class WorkbenchServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => \Filament\Support\Colors\Color::Amber,
            ])
            ->plugins([
                new HelpdeskPlugin(),
            ]);
    }
}
