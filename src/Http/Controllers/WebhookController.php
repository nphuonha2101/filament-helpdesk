<?php

namespace Nphuonha\FilamentHelpdesk\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Nphuonha\FilamentHelpdesk\Services\TicketService;

class WebhookController extends Controller
{
    public function handleMailgun(Request $request, TicketService $ticketService)
    {
        if (! $this->verifyMailgunSignature($request)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $sender = $request->input('sender');
        $recipient = $request->input('recipient'); // This is the received_at_email
        $subject = $request->input('subject');
        $body = $request->input('body-plain') ?? $request->input('body-html');
        $messageId = $request->input('Message-Id');

        // Handle attachments (basic implementation)
        $attachments = [];
        if ($request->has('attachments')) {
            // Mailgun sends attachments as JSON string with URLs, or multipart
            // For multipart, Laravel handles them as UploadedFile
            foreach ($request->allFiles() as $file) {
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'content' => base64_encode(file_get_contents($file->getRealPath())),
                    'mime' => $file->getMimeType(),
                ];
            }
        }

        $ticketService->processIncomingMessage(
            $sender,
            $subject,
            $body,
            $attachments,
            $messageId,
            $recipient,
            'webhook'
        );

        return response()->json(['status' => 'ok']);
    }

    public function handleSes(Request $request, TicketService $ticketService)
    {
        // AWS SNS sends JSON payload
        $payload = json_decode($request->getContent(), true);

        if (! $payload || ! isset($payload['Type'])) {
             // Handle SubscriptionConfirmation
             if ($request->header('x-amz-sns-message-type') === 'SubscriptionConfirmation') {
                 // You should log the SubscribeURL or auto-visit it
                 Log::info('SES Subscription Confirmation: ' . json_decode($request->getContent())->SubscribeURL);
                 return response()->json(['status' => 'confirmation_logged']);
             }
             return response()->json(['error' => 'Invalid payload'], 400);
        }

        if ($payload['Type'] === 'Notification') {
            $message = json_decode($payload['Message'], true);

            // Check if this is a receipt notification
            if (isset($message['notificationType']) && $message['notificationType'] === 'Received') {
                $mail = $message['mail'];
                $receipt = $message['receipt'];

                $sender = $mail['source'];
                $recipient = $mail['destination'][0] ?? null;
                $subject = $mail['commonHeaders']['subject'] ?? 'No Subject';
                $messageId = $mail['messageId'];
                
                $body = $message['content'] ?? '(Body not available in simple SNS notification)';

                $ticketService->processIncomingMessage(
                    $sender,
                    $subject,
                    $body,
                    [], // Attachments hard to get from simple SNS JSON
                    $messageId,
                    $recipient,
                    'webhook'
                );
            }
        }

        return response()->json(['status' => 'ok']);
    }

    protected function verifyMailgunSignature(Request $request): bool
    {
        $signature = $request->input('signature');
        if (! $signature) return false;

        $timestamp = $signature['timestamp'];
        $token = $signature['token'];
        $providedSignature = $signature['signature'];

        if (abs(time() - $timestamp) > 15 * 60) {
            return false;
        }

        $secret = config('filament-helpdesk.webhook.mailgun_secret');
        if (! $secret) return true; // Allow if no secret configured (dev mode)

        $expectedSignature = hash_hmac('sha256', $timestamp . $token, $secret);

        return hash_equals($expectedSignature, $providedSignature);
    }
}
