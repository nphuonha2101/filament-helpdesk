<?php

namespace Nphuonha\FilamentHelpdesk\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Nphuonha\FilamentHelpdesk\Services\TicketService;

class WebhookController extends Controller
{
    public function handleMailgun(Request $request, TicketService $ticketService)
    {
        // Verify signature (omitted for brevity)

        $sender = $request->input('sender');
        $subject = $request->input('subject');
        $body = $request->input('body-plain');

        // Handle attachments if any
        $attachments = [];

        $ticketService->processIncomingMessage($sender, $subject, $body, $attachments);

        return response()->json(['status' => 'ok']);
    }
}
