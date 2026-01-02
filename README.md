# Filament Helpdesk

A complete Helpdesk solution for Filament, featuring ticket management, email integration (IMAP & Webhooks), and a public submission form.

## Features

- **Ticket Management**: Full CRUD interface for managing support tickets within the Filament Admin Panel.
- **Public Submission Form**: A Livewire component to allow guests and authenticated users to submit tickets from your frontend.
- **Email Integration**:
    - **Incoming (IMAP)**: Fetch emails via IMAP with smart threading, deduplication, and automatic ticket assignment.
    - **Email Threading**: Supports `In-Reply-To` and `References` headers for proper conversation threading.
    - **Noreply Filtering**: Automatically ignores emails from noreply addresses.
    - **Incoming (Webhooks)**: Support for incoming mail webhooks (e.g., Mailgun, AWS SES).
    - **Outgoing**: Automated email notifications to users when admins reply to tickets.
    - **Email Status Tracking**: Track email delivery status (sent/failed) with automatic retry capability.
- **Email Templates**: 
    - Customizable email templates with live preview.
    - Support for placeholders: `{ticket_id}`, `{subject}`, `{status}`, `{body}`.
    - Template selection when replying to tickets.
- **Smart Email Processing**:
    - Message-ID tracking for duplicate prevention.
    - Oldest-first processing to ensure proper threading.
    - Chunked fetching with progress bars for performance.
- **Retry Mechanism**: Failed email notifications can be retried with one click.
- **Filament v4 Ready**: Built using the latest Filament standards, including Enums for status and priority management.

## Requirements

- PHP 8.1+
- Laravel 10.0+
- Filament 3.0+ / 4.0+

## Installation

You can install the package via composer:

```bash
composer require nphuonha/filament-helpdesk
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="filament-helpdesk-migrations"
php artisan migrate
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="filament-helpdesk-config"
```

## Configuration

### 1. IMAP Configuration (Optional)

To enable fetching emails via IMAP (polling), add the following variables to your `.env` file:

```env
HELPDESK_IMAP_ENABLED=true
HELPDESK_IMAP_HOST=imap.gmail.com
HELPDESK_IMAP_PORT=993
HELPDESK_IMAP_ENCRYPTION=ssl
HELPDESK_IMAP_USERNAME=support@yourdomain.com
HELPDESK_IMAP_PASSWORD=your-app-password
HELPDESK_IMAP_MAILBOX=INBOX
```

Then, schedule the fetch command in your `app/Console/Kernel.php` (or using Laravel Scheduler):

```php
$schedule->command('helpdesk:fetch-mail')->everyMinute();
```

**IMAP Command Options:**

```bash
# Fetch with custom limits
php artisan helpdesk:fetch-mail --max=50 --chunk=10

# --max: Maximum emails to process (default: 100)
# --chunk: Number of emails per batch (default: 20)
```

### 2. Webhook Configuration (Optional)

To receive emails via Webhook (e.g., Mailgun), configure your email provider to POST to the following URL:

```
https://your-domain.com/api/helpdesk/webhook/mailgun
```

Enable the webhook in your `.env` file:

```env
HELPDESK_WEBHOOK_ENABLED=true
```

### 3. User Model

By default, the package uses `App\Models\User`. If you use a different model for authentication, update the `user_model` key in `config/filament-helpdesk.php`.

## Usage

### Admin Panel

After installation, the **Helpdesk** section will appear in your Filament Admin Panel.

#### Tickets
- View, filter, and reply to support tickets.
- Assign tickets to specific agents.
- Filter by "My Tickets" to see only tickets assigned to you.
- Track email delivery status for each admin reply.
- Retry failed email notifications with one click.

#### Email Templates
- Create and manage templates for outgoing emails.
- Live preview with placeholder replacement.
- Use placeholders: `{ticket_id}`, `{subject}`, `{status}`, `{body}`.
- Select templates when replying to tickets for quick responses.

#### Email Status Tracking
- Each admin reply shows email delivery status (✓ sent / ✗ failed).
- Failed emails display error message on hover.
- One-click retry for failed notifications.
- Automatic status updates via queue jobs and event listeners.

### Public Ticket Form

You can embed the ticket submission form in any Blade view or page on your frontend. This form supports file attachments and automatically detects logged-in users.

```blade
<livewire:filament-helpdesk::submit-ticket />
```

### Customizing Views

If you wish to customize the views (e.g., the email templates or the public form), you can publish them:

```bash
php artisan vendor:publish --tag="filament-helpdesk-views"
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Phuong Nha Nguyen](https://github.com/nphuonha2101)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
