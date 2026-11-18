# Changelog

All notable changes to this project will be documented in this file.

## 1.1.0 - 2026-11-18

### Changed

- Requires the 2.0 of the suite core, which runs on [laravel-privacy](https://github.com/jeffersongoncalves/laravel-privacy), the engine shared by the privacy suites. No change to this package's API, config or tables.

## 1.0.0 - 2026-11-17

First release of laravel-us-privacy-notices, the privacy notice the US state privacy laws require (on top of laravel-us-privacy).

- Processing activities: purpose, data categories, models, sources, recipients by role (processor, third party, affiliate), retention.
- `<x-us-privacy-notices::privacy-policy />`: data categories and sensitive data, purposes, third parties and the categories shared, sale / targeted advertising / profiling disclosures, rights and the appeal process. English and Spanish.
- `<x-us-privacy-notices::privacy-choices />`: the "Your Privacy Choices" link when you sell, target ads or profile, and the sensitive data consent link.
- `php artisan us-privacy:notices [--json]`.

## [Unreleased]
