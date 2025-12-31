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
        'mailgun_secret' => env('HELPDESK_MAILGUN_SECRET'),
        'ses_secret' => env('HELPDESK_SES_SECRET'), // Optional: for custom verification
    ],
    'user_model' => \App\Models\User::class, // Model for customer users
    'agent_model' => \App\Models\User::class, // Model for helpdesk agents

    /*
    |--------------------------------------------------------------------------
    | Mailer Configuration
    |--------------------------------------------------------------------------
    |
    | Specify the mailer to use for sending helpdesk notifications.
    | If null, the default application mailer will be used.
    | You can define a custom mailer in your config/mail.php and reference it here.
    |
    */
    'mailer' => env('HELPDESK_MAILER'),

    /*
    |--------------------------------------------------------------------------
    | Email Sending Configuration
    |--------------------------------------------------------------------------
    |
    | If set to true, the system will try to send the reply from the email address
    | that received the original ticket (e.g. sales@domain.com).
    | This is useful for services like Mailgun/SES.
    |
    | If set to false, it will always use the default MAIL_FROM_ADDRESS.
    | This is recommended for standard SMTP (Gmail, Outlook) to avoid spam/blocking.
    |
    */
    'enable_dynamic_sender' => false,
];
