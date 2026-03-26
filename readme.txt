=== Kontentainment Wrapped ===
Contributors: kollectivco
Stable tag: 1.2.2
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Premium story-driven wrapped editions for WordPress.

== Description ==

Kontentainment Wrapped is a standalone WordPress plugin for creating curated, story-first Wrapped editions with an immersive frontend viewer and a guided admin builder.

== Installation ==

1. Upload the plugin ZIP through WordPress or install it from a GitHub Release package.
2. Activate the plugin.
3. Open `Kontentainment Wrapped` in the WordPress admin.

== Changelog ==

= 1.2.2 =
* Added duplicate-load guards in the plugin bootstrap to prevent constant redefinition warnings.
* Fixed auto-update hook compatibility when WordPress passes null.
* Hardened canonical plugin identity handling for updater safety.

= 1.2.1 =
* Added a manual Check for Updates flow in the plugin admin.
* Improved WordPress-native update and auto-update support for GitHub releases.

= 1.2.0 =
* Switched the Wrapped builder to a clearer light-mode editing surface.
* Upgraded Music Top Cards to support dynamic cards plus swipeable carousel behavior in the viewer.
* Reworked Music Chart Week into a stronger Top 10 chart board layout.

= 1.1.0 =
* Added music-focused Wrapped slide types for top cards, chart week, top grid, and music spotlight.
* Added structured authoring fields and viewer templates for premium music/chart story slides.

= 1.0.3 =
* Fixed the blank Kontentainment Wrapped Overview admin page by rendering a real dashboard.
* Added overview stats, quick actions, getting started guidance, and empty-state messaging.

= 1.0.2 =
* Hardened GitHub updater release selection and package safety checks.
* Improved release QA readiness for production auto-updates.

= 1.0.1 =
* Added GitHub release-based update support.
* Added changelog and release packaging workflow.
* Improved version consistency for standalone plugin releases.
