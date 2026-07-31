# Selfie Handler

`3neti/form-handler-selfie` captures a selfie through the browser MediaDevices
API as a step in `3neti/form-flow`.

## Features

- Front or rear camera capture with an optional face guide
- Configurable image dimensions, format, and quality
- Shared Form Flow screen and action components
- Default, compact, and immersive presentation variants
- Canonical `selfie` result field

## Installation

```bash
composer require 3neti/form-handler-selfie
php artisan selfie-handler:install --no-interaction
```

## Usage

```php
[
    'handler' => 'selfie',
    'config' => [
        'width' => 640,
        'height' => 480,
        'quality' => 0.85,
        'format' => 'image/jpeg',
        'facing_mode' => 'user',
        'show_guide' => true,
        'ui_variant' => 'compact',
    ],
]
```

Successful capture returns `selfie`, `timestamp`, `width`, `height`, and
`format`. The `selfie` value is a data URL. Hosts should apply an explicit
retention, encryption, access-control, and redaction policy before persisting
or logging biometric images.

## Requirements

- PHP 8.2 or newer
- Laravel 12 or 13
- Form Flow 1.8 or newer
- Inertia Laravel 2 or 3
- A secure browser context with MediaDevices support

## Testing

```bash
composer test
composer pint -- --test
composer audit
```

## License

MIT
