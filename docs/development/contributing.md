# Contributing

## Repository structure

- `src/phpGPX/` - Library source code
  - `Models/` - Data models (GpxFile, Track, Segment, Point, Stats, etc.)
  - `Parsers/` - XML parsing and serialization
  - `Helpers/` - Utility classes (GeoHelper, DateTimeHelper, distance/elevation calculators)
- `tests/` - Test suite
  - `Unit/` - Unit tests for individual components
  - `Integration/` - Full file load/save round-trip tests
  - `Fixtures/` - GPX and parser test fixture files
- `docs/` - Documentation (mkdocs-material)

## Branches

- `master` - Latest stable release
- `develop` - Work on the next major version (2.x)

## Setting up

```bash
git clone https://github.com/Sibyx/phpGPX.git
cd phpGPX
composer install
```

## Code style

The project follows **PSR-12** with **tab indentation**, enforced by [PHP CS Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) (configured in `.php-cs-fixer.php`).

```bash
# Check for style violations (dry run)
composer cs-fix -- --dry-run

# Auto-fix all files
composer cs-fix
```

Key rules beyond PSR-12:

- Short array syntax (`[]` not `array()`)
- No unused imports
- Alphabetically ordered imports
- Single quotes for strings
- Trailing commas in multiline arguments, arrays, and parameters