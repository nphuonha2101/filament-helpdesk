<?php

namespace Nphuonha\FilamentHelpdesk\Services;

use Illuminate\Support\Str;
use Nphuonha\FilamentHelpdesk\Models\Ticket;
use Nphuonha\FilamentHelpdesk\Notifications\NewTicketMessageNotification;

class TicketService
{
    public function processIncomingMessage(
        string $email, 
        string $subject, 
        string $body, 
        array $attachments = [], 
        ?string $messageId = null, 
        ?string $receivedAtEmail = null, 
        string $channel = 'web',
        ?string $inReplyTo = null,
        array $references = []
    ): ?Ticket
    {
        // Ignore noreply emails
        if (Str::contains(strtolower($email), ['noreply', 'no-reply'])) {
            return null;
        }

        // Check for duplicate message via Message-ID
        if ($messageId && \Nphuonha\FilamentHelpdesk\Models\TicketMessage::where('message_id', $messageId)->exists()) {
            // Return existing ticket associated with this message
            return \Nphuonha\FilamentHelpdesk\Models\TicketMessage::where('message_id', $messageId)->first()->ticket;
        }

        // Try to find ticket ID in subject like [#123] or [123]
        preg_match('/\[#?(\d+)\]/', $subject, $matches);
        $ticketId = $matches[1] ?? null;

        // Try to find parent via In-Reply-To
        if (! $ticketId && $inReplyTo) {
            $parentMessage = \Nphuonha\FilamentHelpdesk\Models\TicketMessage::where('message_id', $inReplyTo)->first();
            if ($parentMessage) {
                $ticketId = $parentMessage->ticket_id;
            }
        }

        // Try to find parent via References
        if (! $ticketId && ! empty($references)) {
            $parentMessage = \Nphuonha\FilamentHelpdesk\Models\TicketMessage::whereIn('message_id', $references)->first();
            if ($parentMessage) {
                $ticketId = $parentMessage->ticket_id;
            }
        }

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
            'message_id' => $messageId,
        ]);

        // Notify assigned agent if exists
        if ($ticket->assignedTo) {
            $ticket->assignedTo->notify(new NewTicketMessageNotification($ticket, $message));
        }

        return $ticket;
    }
}
