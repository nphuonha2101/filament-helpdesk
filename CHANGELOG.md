# Changelog

All notable changes to `nphuonha/filament-helpdesk` will be documented in this file.

## v2.1.5 - 2026-01-01

### Fixed
- Fixed email threading regex to support both `[#123]` and `[123]` formats in subject lines.
- Email replies with format "Re: Subject [1]" now correctly thread to existing tickets instead of creating duplicates.

## v2.1.4 - 2026-01-01

### Fixed
- Fixed "Attempt to read property on null" error in MessagesRelationManager table columns.

## v2.1.4 Alpha 1 - 2026-01-01

### Added
- **Email Status Tracking**: Added `email_sent`, `email_error`, and `email_sent_at` columns to track email delivery status.
- **Email Retry Mechanism**: Added "Retry Send" action for failed email notifications in MessagesRelationManager.
- **Event Listener**: Added `MarkEmailAsSent` listener to automatically update email status when notifications are sent.
- **Failed Job Handling**: Added `failed()` method in `TicketReplyNotification` to catch and log email failures.
- Email status icon column in messages table with tooltips showing sent time or error messages.

### Changed
- Updated `TicketReplyNotification` to use event-driven status tracking instead of try-catch blocks.
- Updated `MessagesRelationManager` to display email status icon with tooltips.

### Database Migrations
- `add_email_status_to_helpdesk_messages_table`: Added email status tracking columns.

## v2.1.3 Alpha 9 - 2026-01-01

### Fixed
- Fixed TicketStatus enum type conversion error by using `getLabel()` method instead of direct cast.
- Updated placeholder replacement to properly handle enum labels in email templates.

## v2.1.3 Alpha 8 - 2026-01-01

### Fixed
- Fixed template placeholders not being replaced when replying to customers.
- Updated template logic to replace placeholders at form level instead of notification level.

### Changed
- Template content now auto-replaces placeholders when selected in reply form.
- Email notifications use pre-processed body from database instead of re-processing templates.

## v2.1.3 Alpha 7 - 2026-01-01

### Fixed
- Fixed type hint error for `Set` parameter in Filament v4 by using correct namespace `Filament\Schemas\Components\Utilities\Set`.

## v2.1.3 Alpha 6 - 2026-01-01

### Changed
- Updated EmailTemplate form grid layout to 2 columns with `columnSpanFull()`.
- Added live preview panel for email templates with real-time placeholder replacement.
- Template selection in reply form now auto-fills body field with processed content.

### Fixed
- Fixed Alpine.js errors in RichEditor by disabling file attachments (`fileAttachmentsDisk` and `fileAttachmentsDirectory` set to null).

## v2.1.3 Alpha 5 - 2026-01-01

### Changed
- Updated EmailTemplateResource layout with proper 2-column grid structure.
- Improved live preview functionality for better UX.

## v2.1.3 Alpha 4 - 2026-01-01

### Changed
- Updated TicketResource and EmailTemplateResource UI layout improvements.
- Better form field organization and visual hierarchy.

## v2.1.2 Alpha 3 - 2026-01-01

### Changed
- **Database Schema Consolidation**: Refactored multiple migrations into single `create_helpdesk_tables` migration.
- Simplified migration structure for cleaner installation.

## v2.1.1 Alpha 2 - 2025-12-31

### Added
- **Email Threading Support**: 
  - Added `message_id` column to track IMAP Message-IDs.
  - Added support for `In-Reply-To` and `References` headers for proper email threading.
  - Updated `TicketService` to match replies to existing tickets via email headers.
- **Smart Email Processing**:
  - Added `setFetchOrder('asc')` to process oldest emails first for proper threading.
  - Added noreply email filtering to ignore automated system emails.
  - Added duplicate detection via Message-ID tracking.
- **IMAP Fetch Improvements**:
  - Added `--max` and `--chunk` options for better performance and memory management.
  - Added progress bar for visual feedback during email fetching.
- **Custom Email View**: Added simple email template without Laravel's default notification frame.

### Changed
- Updated `FetchMailCommand` to extract and pass email headers (`In-Reply-To`, `References`) to service.
- Updated `TicketService::processIncomingMessage()` to accept additional parameters for email threading.
- Updated `TicketMessage` model to include `message_id` in fillable fields.

### Fixed
- Fixed import statements for Filament v4 compatibility.

### Database Migrations
- `add_message_id_to_helpdesk_messages_table`: Added `message_id` column for email tracking.

## v2.1.0-Alpha 1 - 2025-12-31

### Added
- **Filament v4 Support:** Compatible with Filament v4.
>>>>>>> v2.1.5
- **Dynamic Sender:** Added `received_at_email` to tickets table to track which email address received the ticket.
- **Agent Assignment:** Added `assigned_to_user_id` to tickets table to assign tickets to specific agents.
- **Channel Tracking:** Added `channel` column to tickets table (web, imap, webhook).
- **Configurable Models:** Added `agent_model` config to support separate models for customers and agents.
- **Custom Mailer:** Added `mailer` config to allow using a specific mailer for helpdesk notifications.
- **Webhook Support:** Enhanced `WebhookController` to support Mailgun (with signature verification) and AWS SES.
- **Notifications:** Added `NewTicketMessageNotification` to notify agents when a customer replies.
- **UI Improvements:** Added "My Tickets" filter and "Assign to Me" action in TicketResource.

### Changed
- Updated `TicketReplyNotification` to support dynamic sender address based on `received_at_email`.
- Updated `FetchMailCommand` to capture `To` address and pass `channel='imap'`.
- Updated `TicketService` to handle new fields (`received_at_email`, `channel`, `assigned_to_user_id`).
- Refactored `Ticket` and `TicketMessage` models to use configured user models.

### Configuration Changes
- Added `agent_model` to `config/filament-helpdesk.php`.
- Added `enable_dynamic_sender` to `config/filament-helpdesk.php`.
- Added `mailer` to `config/filament-helpdesk.php`.
- Added `webhook.mailgun_secret` and `webhook.ses_secret` to `config/filament-helpdesk.php`.

## v2.0.0-Alpha - 2025-12-31

- First release (Alpha) for Filament v4.

## v1.0.0-Alpha - 2025-12-31

- First version (Alpha) for Filament v3
