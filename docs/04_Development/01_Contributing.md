# Contributing

## Repository structure

- `src/phpGPX/` - Library source code
  - `Models/` - Data models (GpxFile, Track, Segment, Point, Stats, etc.)
  - `Parsers/` - XML parsing and serialization
  - `Helpers/` - Utility classes (GeoHelper, DateTimeHelper, distance/elevation calculators)
- `tests/` - Test suite
  - `Unit/` - Unit tests for individual components
  - `Integration/` - Full file load/save round-trip tests
  - `fixtures/` - GPX test fixture files
- `docs/` - Documentation (Daux.io)

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

The project uses PSR-2 with **tab indentation** (configured in `.php-cs-fixer.php`).