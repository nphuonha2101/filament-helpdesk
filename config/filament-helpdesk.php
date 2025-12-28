<?php

return [
    'imap' => [
        'enabled' => env('HELPDESK_IMAP_ENABLED', false),
        'host' => env('HELPDESK_IMAP_HOST', 'imap.gmail.com'),
        'port' => env('HELPDESK_IMAP_PORT', 993),
        'encryption' => env('HELPDESK_IMAP_ENCRYPTION', 'ssl'),
        'validate_cert' => env('HELPDESK_IMAP_VALIDATE_CERT', true),
        'username' => env('HELPDESK_IMAP_USERNAME'),
        'password' => env('HELPDESK_IMAP_PASSWORD'),
        'default_mailbox' => env('HELPDESK_IMAP_MAILBOX', 'INBOX'),
    ],
    'webhook' => [
        'enabled' => env('HELPDESK_WEBHOOK_ENABLED', false),
        'secret' => env('HELPDESK_WEBHOOK_SECRET'),
    ],
    'user_model' => \App\Models\User::class,
];
