<?php

namespace Nphuonha\FilamentHelpdesk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Nphuonha\FilamentHelpdesk\Helpdesk
 */
class Helpdesk extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Nphuonha\FilamentHelpdesk\Helpdesk::class;
    }
}
