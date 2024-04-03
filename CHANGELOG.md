# Release Notes

## [Unreleased](https://github.com/sunaoka/push-notifications-php/compare/1.0.5...develop)

### Changed

- Support PHPUnit 11.x
- Deprecated FCM HTTP legacy APIs
  - Because the FCM HTTP legacy APIs was deprecated on June 20, 2023, and will be removed in June 2024.

## [v1.0.5 (2023-02-08)](https://github.com/sunaoka/push-notifications-php/compare/1.0.4...1.0.5)

### Added

- Support PHPUnit 10.x

## [v1.0.4 (2022-12-09)](https://github.com/sunaoka/push-notifications-php/compare/1.0.3...1.0.4)

### Added

- Support PHP 8.2

## [v1.0.3 (2021-10-14)](https://github.com/sunaoka/push-notifications-php/compare/1.0.2...1.0.3)

### Added

- Added examples

## [v1.0.2 (2021-10-13)](https://github.com/sunaoka/push-notifications-php/compare/1.0.1...1.0.2)

### Added

- Added DriverOption::httpOptions (Guzzle Request Options)

### Fixed

- Fixed a bug that APNs by using token failed

## [v1.0.1 (2021-10-13)](https://github.com/sunaoka/push-notifications-php/compare/1.0.0...1.0.1)

### Changed

- No longer need `file://` prefix for APNs\Token\Option::authKey

## [v1.0.0 (2021-10-12)](https://github.com/sunaoka/push-notifications-php/compare/bbc5601...1.0.0)

- first release.
