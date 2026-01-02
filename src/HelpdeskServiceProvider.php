<?php

namespace Nphuonha\FilamentHelpdesk;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Nphuonha\FilamentHelpdesk\Commands\HelpdeskCommand;
use Nphuonha\FilamentHelpdesk\Testing\TestsHelpdesk;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HelpdeskServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-helpdesk';

    public static string $viewNamespace = 'filament-helpdesk';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('nphuonha/filament-helpdesk');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }

        if (file_exists($package->basePath('/../routes/api.php'))) {
            $package->hasRoute('api');
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Event Listeners
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Notifications\Events\NotificationSent::class,
            \Nphuonha\FilamentHelpdesk\Listeners\MarkEmailAsSent::class
        );

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-helpdesk/{$file->getFilename()}"),
                ], 'filament-helpdesk-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsHelpdesk);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'nphuonha/filament-helpdesk';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-helpdesk', __DIR__ . '/../resources/dist/components/filament-helpdesk.js'),
            // Css::make('filament-helpdesk-styles', __DIR__ . '/../resources/dist/filament-helpdesk.css'),
            // Js::make('filament-helpdesk-scripts', __DIR__ . '/../resources/dist/filament-helpdesk.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            HelpdeskCommand::class,
            Commands\FetchMailCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_helpdesk_tables',
            'add_message_id_to_helpdesk_messages_table',
            'add_email_status_to_helpdesk_messages_table',
        ];
    }
}
