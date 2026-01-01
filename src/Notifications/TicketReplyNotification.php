<?php

namespace Nphuonha\FilamentHelpdesk\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Nphuonha\FilamentHelpdesk\Models\EmailTemplate;
use Nphuonha\FilamentHelpdesk\Models\Ticket;
use Nphuonha\FilamentHelpdesk\Models\TicketMessage;

class TicketReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public TicketMessage $message,
        public ?EmailTemplate $template = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $subject = $this->template
            ? $this->replacePlaceholders($this->template->subject_template)
            : "Re: [#{$this->ticket->id}] {$this->ticket->subject}";
        
        $body = $this->message->body;

        $fromEmail = config('filament-helpdesk.enable_dynamic_sender') && $this->ticket->received_at_email
            ? $this->ticket->received_at_email
            : config('mail.from.address');

        $fromName = config('mail.from.name');

        return (new MailMessage)
            ->mailer(config('filament-helpdesk.mailer'))
            ->from($fromEmail, $fromName)
            ->subject($subject)
            ->view('filament-helpdesk::emails.simple', [
                'body' => $body,
                'subject' => $subject
            ]);
    }

    protected function replacePlaceholders(string $text): string
    {
        return str_replace(
            ['{ticket_id}', '{subject}', '{status}', '{body}'],
            [
                $this->ticket->id, 
                $this->ticket->subject, 
                $this->ticket->status->getLabel() ?? (string) $this->ticket->status, 
                $this->message->body
            ],
            $text
        );
    }
}
