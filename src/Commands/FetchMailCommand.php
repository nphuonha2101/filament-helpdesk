<?php

namespace Nphuonha\FilamentHelpdesk\Commands;

use Illuminate\Console\Command;
use Nphuonha\FilamentHelpdesk\Services\TicketService;

class FetchMailCommand extends Command
{
    public $signature = 'helpdesk:fetch-mail';

    public $description = 'Fetch incoming emails and create tickets';

    public function handle(TicketService $ticketService): int
    {
        if (! config('filament-helpdesk.imap.enabled')) {
            $this->info('IMAP fetching is disabled in config.');

            return self::SUCCESS;
        }

        $this->info('Fetching emails...');

        try {
            /** @var \Webklex\PHPIMAP\Client $client */
            $client = \Webklex\IMAP\Facades\Client::make([
                'host' => config('filament-helpdesk.imap.host'),
                'port' => config('filament-helpdesk.imap.port'),
                'encryption' => config('filament-helpdesk.imap.encryption'),
                'validate_cert' => config('filament-helpdesk.imap.validate_cert'),
                'username' => config('filament-helpdesk.imap.username'),
                'password' => config('filament-helpdesk.imap.password'),
                'protocol' => 'imap',
            ]);

            $client->connect();

            $folder = $client->getFolder(config('filament-helpdesk.imap.default_mailbox'));
            $messages = $folder->query()->unseen()->get();

            foreach ($messages as $message) {
                $from = $message->getFrom()[0]->mail;
                $subject = $message->getSubject();
                $body = $message->getTextBody() ?: $message->getHTMLBody();
                $messageId = $message->getMessageId();

                $attachments = [];
                if ($message->hasAttachments()) {
                    foreach ($message->getAttachments() as $attachment) {
                        $attachments[] = [
                            'name' => $attachment->getName(),
                            'content' => base64_encode($attachment->getContent()),
                            'mime' => $attachment->getMimeType(),
                        ];
                    }
                }

                $ticketService->processIncomingMessage(
                    $from,
                    $subject,
                    $body,
                    $attachments,
                    $messageId
                );

                $message->setFlag('Seen');
            }

            $this->info('Fetched ' . count($messages) . ' emails.');

        } catch (\Exception $e) {
            $this->error('Error fetching emails: ' . $e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
