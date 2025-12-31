<?php

namespace Nphuonha\FilamentHelpdesk\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Nphuonha\FilamentHelpdesk\Models\EmailTemplate;
use Nphuonha\FilamentHelpdesk\Models\Ticket;
use Nphuonha\FilamentHelpdesk\Models\TicketMessage;

class TicketReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public TicketMessage $message
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Try to find a template (e.g. 'ticket_reply')
        $template = EmailTemplate::where('name', 'ticket_reply')->first();

        $subject = $template
            ? $this->replacePlaceholders($template->subject_template)
            : "Re: [#{$this->ticket->id}] {$this->ticket->subject}";

        $body = $template
            ? $this->replacePlaceholders($template->body_template)
            : $this->message->body;

        $fromEmail = $this->ticket->received_at_email ?? config('mail.from.address');
        $fromName = config('mail.from.name');

        return (new MailMessage)
            ->from($fromEmail, $fromName)
            ->subject($subject)
            ->line($body)
            ->action('View Ticket', url('/helpdesk/tickets/' . $this->ticket->uuid));
    }

    protected function replacePlaceholders(string $text): string
    {
        return str_replace(
            ['{ticket_id}', '{subject}', '{status}', '{body}'],
            [$this->ticket->id, $this->ticket->subject, $this->ticket->status, $this->message->body],
            $text
        );
    }
}
