# JSON

A structured JSON representation that mirrors the GPX data model.

## Saving to file

```php
$file->save('output.json', phpGPX::JSON_FORMAT);
```

## Getting JSON as string

```php
$jsonString = $file->toJSON(false); // false = GPX structure format
```

## Structure

The JSON output follows the GPX structure:

```json
{
    "creator": "phpGPX/2.0.0-alpha.1",
    "metadata": {
        "name": "My Track",
        "time": "2024-01-15T07:00:00+00:00"
    },
    "tracks": [
        {
            "name": "Morning Run",
            "trkseg": [
                {
                    "points": [
                        {
                            "lat": 48.157,
                            "lon": 17.054,
                            "ele": 134.0,
                            "time": "2024-01-15T07:00:00+00:00"
                        }
                    ],
                    "stats": {
                        "distance": 1250.5,
                        "avgSpeed": 3.2
                    }
                }
            ],
            "stats": { }
        }
    ]
}
```

## DateTime format

Control the DateTime output format:

```php
phpGPX::$DATETIME_FORMAT = 'c';  // ISO 8601 (default)
phpGPX::$DATETIME_FORMAT = 'U';  // Unix timestamp
phpGPX::$DATETIME_FORMAT = 'Y-m-d H:i:s'; // Custom
```

## Timezone

```php
phpGPX::$DATETIME_TIMEZONE_OUTPUT = 'UTC';           // default
phpGPX::$DATETIME_TIMEZONE_OUTPUT = 'Europe/Prague';  // local time
```