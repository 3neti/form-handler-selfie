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

## YAML presentation integration

The expanded tokens/screens/actions contract requires the companion Form Flow
changes in this development slice; it is not provided by the released v1.10.0
alone. Release the manager before its dependent handler versions. The portable
schema, inheritance and limits are documented in `docs/PRESENTATION.md` in
[3neti/form-flow](https://github.com/3neti/form-flow). x-change owns its Pay Code
profile and domain driver; this package owns the handler interaction.

In an existing complete YAML driver, merge this step configuration:

```yaml
steps:
  selfie:
    handler: selfie
    step_name: selfie_step
    title: Take a selfie
    config:
      presentation:
        layout:
          density: compact
        actions:
          placement: viewport_bottom
```

The driver may also select a registered profile in top-level `presentation`.
Step settings override driver settings, which override the selected profile
and manager defaults. The handler page uses the shared presentation and action
components; legacy `ui_variant` remains a fallback without that policy.

Camera dimensions, image quality, facing mode and guide settings remain in the
handler config beside `presentation`. Capture Photo and the captured-photo
review are different states; avoid a global primary label. Browser camera
permissions and capture/retake behavior remain handler-owned. Capture-surface
guidance is forwarded through `ui_layout`; the contained/fill-available policy is applied to the capture surface.

After package updates, publish this handler's Vue resources using its installer
and rebuild the standalone host. In x-change hosts, use
`php artisan x-change:publish --scope=build --force --verify --no-interaction`
and the host build workflow instead. Review intentional host customizations
before forced publication; never maintain fixes in generated host copies.

Verify a fresh fixture-based flow and preview after changes. Active flows retain
their frozen presentation. Test initial, processing, error, and completed/review
states on mobile; do not collect real identity evidence or send real OTPs merely
to verify appearance. Unsupported schema settings must fail validation.
