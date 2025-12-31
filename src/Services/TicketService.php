<?php

namespace Nphuonha\FilamentHelpdesk\Services;

use Illuminate\Support\Str;
use Nphuonha\FilamentHelpdesk\Models\Ticket;
use Nphuonha\FilamentHelpdesk\Notifications\NewTicketMessageNotification;

class TicketService
{
    public function processIncomingMessage(string $email, string $subject, string $body, array $attachments = [], ?string $messageId = null, ?string $receivedAtEmail = null, string $channel = 'web'): Ticket
    {
        // Try to find ticket ID in subject like [#123]
        preg_match('/\[#(\d+)\]/', $subject, $matches);
        $ticketId = $matches[1] ?? null;

        $ticket = null;
        if ($ticketId) {
            $ticket = Ticket::find($ticketId);
        }

        $userModel = config('filament-helpdesk.user_model', \App\Models\User::class);
        $user = $userModel::where('email', $email)->first();

        if (! $ticket) {
            $ticket = Ticket::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user?->id,
                'email' => $email,
                'received_at_email' => $receivedAtEmail,
                'subject' => $subject,
                'status' => \Nphuonha\FilamentHelpdesk\Enums\TicketStatus::Open,
                'priority' => \Nphuonha\FilamentHelpdesk\Enums\TicketPriority::Normal,
                'channel' => $channel,
            ]);
        } else {
            // Re-open ticket if it was closed
            if ($ticket->status === \Nphuonha\FilamentHelpdesk\Enums\TicketStatus::Closed) {
                $ticket->update(['status' => \Nphuonha\FilamentHelpdesk\Enums\TicketStatus::Open]);
            }
        }

        $message = $ticket->messages()->create([
            'user_id' => $user?->id,
            'body' => $body,
            'attachments' => $attachments,
            'is_admin_reply' => false,
        ]);

        // Notify assigned agent if exists
        if ($ticket->assignedTo) {
            $ticket->assignedTo->notify(new NewTicketMessageNotification($ticket, $message));
        }

        return $ticket;
    }
}
