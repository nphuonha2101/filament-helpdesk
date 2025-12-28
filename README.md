# :package_description

[![Latest Version on Packagist](https://img.shields.io/packagist/v/:vendor_slug/:package_slug.svg?style=flat-square)](https://packagist.org/packages/:vendor_slug/:package_slug)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/:vendor_slug/:package_slug/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/:vendor_slug/:package_slug/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/:vendor_slug/:package_slug/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/:vendor_slug/:package_slug/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/:vendor_slug/:package_slug.svg?style=flat-square)](https://packagist.org/packages/:vendor_slug/:package_slug)

<!--delete-->
---
This repo can be used to scaffold a Filament plugin. Follow these steps to get started:

1. Press the "Use this template" button at the top of this repo to create a new repo with the contents of this skeleton.
2. Run "php ./configure.php" to run a script that will replace all placeholders throughout all the files.
3. Make something great!
---
<!--/delete-->

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require nphuonha/filament-helpdesk
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-helpdesk-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-helpdesk-config"
```

## Configuration

### IMAP (Incoming Mail)

To enable fetching emails via IMAP, add the following to your `.env` file:

```env
HELPDESK_IMAP_ENABLED=true
HELPDESK_IMAP_HOST=imap.gmail.com
HELPDESK_IMAP_PORT=993
HELPDESK_IMAP_ENCRYPTION=ssl
HELPDESK_IMAP_USERNAME=your-email@gmail.com
HELPDESK_IMAP_PASSWORD=your-app-password
```

Then schedule the command in your `app/Console/Kernel.php`:

```php
$schedule->command('helpdesk:fetch-mail')->everyMinute();
```

### Webhook (Incoming Mail)

To receive emails via Webhook (e.g. Mailgun), configure your provider to POST to:

`https://your-domain.com/api/helpdesk/webhook/mailgun`

And set the secret in `.env` (if applicable):

```env
HELPDESK_WEBHOOK_ENABLED=true
```

## Usage

### Public Ticket Form

You can embed the ticket submission form in any Blade view:

```blade
<livewire:filament-helpdesk::submit-ticket />
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag=":package_slug-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$variable = new VendorName\Skeleton();
echo $variable->echoPhrase('Hello, VendorName!');
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

- [:author_name](https://github.com/:author_username)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
