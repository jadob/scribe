# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.3.1] - 2026-07-11

### Fixed
- `DoctrineDbalMessageRepository`: changed `recorded_at` field to datetime

## [0.3.0] - 2026-07-11

### Added
- decrypting encrypted payload events in `EncryptionAwareMessageNormalizer`

### Changed
- `AggregateRootRepository::load()` now throws an exception when no events was found in persistence.

### Fixed
- `EncryptionAwareMessageNormalizer`: data for normalization is now taken from normalizer output, not from reflection
- `EncryptionAwareMessageNormalizer`: payload is restored to its original form after decrypting

## [0.2.0] - 2026-07-11

### Added
- added Event listener contracts to be used in eg. projections
- aggregate repository is now able to load events from persistence and recreate the aggregate

### Changed
- `EventInterface` is now deprecated
- events are now processed by using `cuyz/valinor`

## [0.1.1] - 2026-07-05

### Added
- Introduced `AggregateRootRepositoryFactory` for easier repository creation

### Changed
- `EventInterface` lost `getEventId` method from its contract and `eventId` in `reconstitute()`

## [0.1.0] - 2026-07-04

### Added
- First public release.