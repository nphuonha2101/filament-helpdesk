<?php

namespace Nphuonha\FilamentHelpdesk\Commands;

use Illuminate\Console\Command;

class HelpdeskCommand extends Command
{
    public $signature = 'helpdesk:test';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
