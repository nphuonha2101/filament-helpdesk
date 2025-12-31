<?php

use Illuminate\Support\Facades\Route;
use Nphuonha\FilamentHelpdesk\Http\Controllers\WebhookController;

Route::post('helpdesk/webhook/mailgun', [WebhookController::class, 'handleMailgun'])->name('helpdesk.webhook.mailgun');
Route::post('helpdesk/webhook/ses', [WebhookController::class, 'handleSes'])->name('helpdesk.webhook.ses');
