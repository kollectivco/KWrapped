# Changelog

All notable changes to this project will be documented in this file.

## [1.0.2] - 2026-03-25

### Fixed
- Removed unsafe fallback to GitHub source ZIPs for plugin updates.
- Limited update offers to releases that include the expected packaged plugin asset.

### Improved
- Tightened GitHub release parsing for production update reliability.

## [1.0.1] - 2026-03-25

### Added
- Dedicated GitHub updater service scoped to Kontentainment Wrapped only.
- `readme.txt` metadata for WordPress versioning and stable tag support.
- GitHub Actions workflow for packaging release ZIP artifacts with the correct plugin root folder.

### Changed
- Updated plugin metadata and constants for versioned GitHub release distribution.

### Improved
- Added a human-readable changelog process for release alignment.
