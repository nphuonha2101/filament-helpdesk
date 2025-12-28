<?php

use Illuminate\Support\Facades\Route;
use Nphuonha\FilamentHelpdesk\Http\Controllers\WebhookController;

Route::post('helpdesk/webhook/mailgun', [WebhookController::class, 'handleMailgun'])->name('helpdesk.webhook.mailgun');
