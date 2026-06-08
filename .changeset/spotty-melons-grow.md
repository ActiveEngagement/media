---
"actengage-media": major
---

### Laravel 12 & 13 Support

Added `^12.0` and `^13.0` to the `laravel/framework` constraint alongside `^11.0`. The package now supports Laravel 11, 12, and 13 on PHP 8.3, 8.4, and 8.5.

### Bug Fixes

- `HasEvents::unsetEventDispatcher()` no longer throws a `TypeError`. The static `$dispatcher` property is now nullable (`?Dispatcher`), matching Laravel's own model implementation.
- Removed an unreachable `filterEventResults()`/`=== false` halt branch in `HasEvents::fireEvent()` that could never execute.

### Testing & Tooling

- Migrated the test suite from PHPUnit to Pest with 100% line coverage of `src/`, written in idiomatic Pest style with datasets.
- Added Larastan/PHPStan static analysis at the maximum level (no baseline or ignores), Rector, and Laravel Pint. The full `src/` and test suite are type-clean and fully typed (generics, array shapes, `@property` annotations).
- Added GitHub Actions CI running Pest (`--min=100` coverage gate) across a PHP 8.3/8.4/8.5 matrix, plus Pint, PHPStan, and Rector checks.
- Added a GitHub Actions release workflow using changesets for automated versioning.
- Added `laravel/boost` for development.
