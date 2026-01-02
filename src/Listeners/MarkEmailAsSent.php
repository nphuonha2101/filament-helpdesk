<?php

namespace Nphuonha\FilamentHelpdesk\Listeners;

use Illuminate\Notifications\Events\NotificationSent;
use Nphuonha\FilamentHelpdesk\Notifications\TicketReplyNotification;

class MarkEmailAsSent
{
    public function handle(NotificationSent $event): void
    {
        if ($event->notification instanceof TicketReplyNotification) {
            $event->notification->message->update([
                'email_sent' => true,
                'email_sent_at' => now(),
                'email_error' => null,
            ]);
        }
    }
}
