# Change Log
All Notable changes to `realpage/json-api-for-lumen` will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

Updates follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [1.4.2] - 2020-06-29
### Updated
- Requires Laravel/Lumen 5.8

## [1.0.2] - 2016-04-27
### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Fix functional args and request parsing within the `MediaTypeGuard`

### Removed
- Nothing

### Security
- Nothing

## [1.0.1] - 2016-04-27
### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Config loading into lumen repository due to config paths being incorrect
- Add class registration for managing lazy dependency injection within the `EnforceMediaType` middleware

### Removed
- Nothing

### Security
- Nothing

## [1.0.0] - 2016-04-26
### Added
- Initial Release!
- Pre-configured [Lumen middleware](https://lumen.laravel.com/docs/5.2/middleware) to enforce [json-api v1.0](http://jsonapi.org/format/1.0/) standards

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing
