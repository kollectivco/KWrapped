# Changelog

All notable changes to this project will be documented in this file.

## [1.1.0] - 2026-03-25

### Added
- New Wrapped music/chart slide types: Music Top Cards, Music Chart Week, Music Top Grid, and Music Spotlight.
- Structured builder fields for music covers, ranked chart rows, curated grid items, and spotlight metadata.
- New music-forward viewer templates and styling tuned for premium portrait story slides.

### Improved
- Existing ranking, spotlight, and mosaic templates now better respect structured slide data.

## [1.0.3] - 2026-03-25

### Fixed
- Replaced the blank Overview menu target with a real admin dashboard renderer.

### Added
- Overview dashboard cards for total, published, and draft edition counts.
- Quick actions, getting-started guidance, recent editions, and empty-state messaging on the Overview page.

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
