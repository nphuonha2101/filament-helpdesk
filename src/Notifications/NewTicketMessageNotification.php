<?php

namespace Nphuonha\FilamentHelpdesk\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Nphuonha\FilamentHelpdesk\Models\Ticket;
use Nphuonha\FilamentHelpdesk\Models\TicketMessage;

class NewTicketMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public TicketMessage $message
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New reply on ticket [#{$this->ticket->id}]")
            ->line("A new reply has been posted on ticket #{$this->ticket->id}: {$this->ticket->subject}")
            ->line("From: {$this->ticket->email}")
            ->line("Message:")
            ->line($this->message->body)
            ->action('View Ticket', url('/admin/helpdesk/tickets/' . $this->ticket->id . '/edit'));
    }

    public function toArray($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'message_id' => $this->message->id,
            'title' => "New reply on ticket #{$this->ticket->id}",
            'body' => "From: {$this->ticket->email}",
        ];
    }
}
