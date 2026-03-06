# XML (GPX)

The native GPX format, conforming to the [GPX 1.1 specification](http://www.topografix.com/GPX/1/1/).

## Saving to file

```php
$file->save('output.gpx', phpGPX::XML_FORMAT);
```

## Getting XML as string

```php
$document = $file->toXML(); // Returns \DOMDocument
$xmlString = $document->saveXML();
```

## Pretty printing

By default, XML output is formatted with indentation:

```php
phpGPX::$PRETTY_PRINT = true; // default
```

Set to `false` for compact output.

## Namespaces

Extension namespaces are included automatically when the file contains extensions. For example, a file with Garmin TrackPointExtension will include:

```xml
<gpx xmlns="http://www.topografix.com/GPX/1/1"
     xmlns:gpxtpx="http://www.garmin.com/xmlschemas/TrackPointExtension/v1"
     xmlns:gpxx="http://www.garmin.com/xmlschemas/GpxExtensions/v3"
     ...>
```

## Creator attribute

The `creator` attribute is set from `$gpxFile->creator`. If not set, it defaults to the phpGPX library signature:

```php
$gpxFile->creator = "My Application";
```