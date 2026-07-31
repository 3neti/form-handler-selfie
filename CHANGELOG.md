# Changelog

All notable changes to `form-handler-selfie` will be documented in this file.

## v1.1.0 - 2026-07-31

### Added
- Shared Form Flow screen and action components
- Default, compact, and immersive UI variants
- Laravel 12/13 and Inertia 2/3 compatibility matrix

### Fixed
- Align handler tests and submission payloads on the canonical `selfie` field

### Changed
- Require Form Flow 1.8 or newer

## v1.0.1

### Fixed
- Return captured images through the collision-safe `selfie` field

## v1.0.0 - 2024-12-24

### Added
- Initial release
- Browser camera capture using MediaDevices API
- Base64 image encoding
- Configurable quality, dimensions, and format
- Face guide overlay (optional)
- Front/back camera support
- Auto-registration with Form Flow Manager
- Vue components with shadcn/ui integration
