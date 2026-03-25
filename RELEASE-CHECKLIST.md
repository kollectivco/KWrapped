# Kontentainment Wrapped Release Checklist

Use this checklist before publishing any new GitHub release for the plugin.

---

## 1) Version bump

Confirm the new version has been updated consistently in all required places:

- [ ] `kontentainment-wrapped.php` plugin header
- [ ] `KT_WRAPPED_VERSION` constant
- [ ] `readme.txt` Stable tag
- [ ] `CHANGELOG.md` new version entry

Versioning rules:
- Patch: fixes / small improvements (`1.0.2` → `1.0.3`)
- Minor: meaningful feature additions (`1.0.2` → `1.1.0`)
- Major: breaking changes only

---

## 2) Changelog readiness

- [ ] Add a new section in `CHANGELOG.md`
- [ ] Use clear headings:
  - Added
  - Changed
  - Fixed
  - Improved
  - Security (when relevant)
- [ ] Make sure the changelog matches the actual code changes
- [ ] Prepare GitHub Release notes from the same content

---

## 3) Plugin identity safety

Confirm plugin identity is still stable:

- [ ] Plugin folder remains `kontentainment-wrapped`
- [ ] Main plugin file remains `kontentainment-wrapped/kontentainment-wrapped.php`
- [ ] `Update URI` still points to the correct GitHub repo
- [ ] Updater remains scoped only to this plugin
- [ ] No broad upgrader hooks affect unrelated plugins

---

## 4) GitHub tag + release prep

- [ ] Create the correct semantic version tag (example: `v1.0.3`)
- [ ] GitHub tag matches plugin version exactly (`vX.Y.Z` ↔ `X.Y.Z`)
- [ ] Push the tag to GitHub
- [ ] Confirm the GitHub Actions workflow runs successfully
- [ ] Confirm the release is **not** a draft
- [ ] Confirm the release is **not** a prerelease

---

## 5) Release asset validation

The updater only accepts a release when the asset exists with the exact expected name.

- [ ] Confirm the attached release asset is exactly:
  - `kontentainment-wrapped.zip`
- [ ] Confirm the ZIP contains a single plugin root folder:
  - `kontentainment-wrapped/`
- [ ] Confirm the main plugin file exists inside the ZIP:
  - `kontentainment-wrapped/kontentainment-wrapped.php`

---

## 6) Staging WordPress update test

Before trusting a release in production, test on staging.

### Setup
- [ ] Install the currently older version on staging
- [ ] Example test path:
  - Installed version: `1.0.2`
  - GitHub release version: `1.0.3`

### Update detection
- [ ] Confirm WordPress shows an update notice for **Kontentainment Wrapped**
- [ ] Confirm unrelated plugins do **not** show false update interference
- [ ] Confirm the plugin details modal loads correctly

### Update execution
- [ ] Run update from the Plugins screen
- [ ] Confirm plugin folder remains:
  - `kontentainment-wrapped`
- [ ] Confirm main file remains:
  - `kontentainment-wrapped/kontentainment-wrapped.php`
- [ ] Confirm plugin stays activated after update
- [ ] Confirm no duplicate plugin entry appears
- [ ] Confirm installed version updates correctly
- [ ] After updating, WordPress no longer offers the same version again

---

## 7) Negative-path checks

Run these checks occasionally or whenever updater logic changes:

- [ ] Draft release is ignored
- [ ] Prerelease is ignored
- [ ] Release without `kontentainment-wrapped.zip` is ignored
- [ ] Missing or malformed asset does not trigger broken update offers
- [ ] Unrelated plugins remain unaffected

---

## 8) Final release sign-off

- [ ] Version bump confirmed
- [ ] Changelog updated
- [ ] GitHub Release published correctly
- [ ] Correct ZIP asset attached
- [ ] Staging update test passed
- [ ] No plugin identity issues found
- [ ] No updater regressions found

---

## Notes

- Never rely on GitHub source ZIPs for production updates.
- The updater should only use the attached release asset:
  - `kontentainment-wrapped.zip`
- Every plugin code change must include a version bump before release.
