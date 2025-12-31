# Changelog

All notable changes to `nphuonha/filament-helpdesk` will be documented in this file.

## 1.1.0-Alpha 1 - 2025-12-31

### Added
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

## 1.0.0-Alpha - 2025-12-31

- First version (Alpha) for Filament v3
