<?php

namespace Nphuonha\FilamentHelpdesk\Commands;

use Illuminate\Console\Command;
use Nphuonha\FilamentHelpdesk\Services\TicketService;

class FetchMailCommand extends Command
{
    public $signature = 'helpdesk:fetch-mail {--chunk=10 : Number of emails to fetch per chunk} {--max=50 : Maximum number of emails to process}';

    public $description = 'Fetch incoming emails and create tickets';

    public function handle(TicketService $ticketService): int
    {
        if (! config('filament-helpdesk.imap.enabled')) {
            $this->info('IMAP fetching is disabled in config.');

            return self::SUCCESS;
        }

        $this->info('Connecting to IMAP server...');

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
            $this->info('Connected. Checking for unseen emails...');

            $folder = $client->getFolder(config('filament-helpdesk.imap.default_mailbox'));
            
            $totalUnseen = $folder->query()->unseen()->count();
            $this->info("Total unseen emails on server: {$totalUnseen}");

            if ($totalUnseen === 0) {
                return self::SUCCESS;
            }

            $max = (int) $this->option('max');
            $chunkSize = (int) $this->option('chunk');
            
            $toProcess = min($totalUnseen, $max);
            $this->info("Processing {$toProcess} emails in chunks of {$chunkSize}...");

            $bar = $this->output->createProgressBar($toProcess);
            $bar->start();

            $processed = 0;

            while ($processed < $toProcess) {
                $remaining = $toProcess - $processed;
                $currentLimit = min($chunkSize, $remaining);

                $messages = $folder->query()->unseen()->limit($currentLimit)->get();

                if ($messages->count() === 0) {
                    break;
                }

                foreach ($messages as $message) {
                    try {
                        $from = $message->getFrom()[0]->mail;
                        $to = $message->getTo()[0]->mail ?? null;   
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
                            $messageId,
                            $to,
                            'imap'
                        );

                        $message->setFlag('Seen');
                        $bar->advance();
                        $processed++;

                    } catch (\Exception $e) {
                        $this->error("Error processing message {$message->getMessageId()}: " . $e->getMessage());
                    }
                }
                
                // Optional: Free up memory
                unset($messages);
                gc_collect_cycles();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Successfully processed {$processed} emails.");

        } catch (\Exception $e) {
            $this->error('Error fetching emails: ' . $e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
