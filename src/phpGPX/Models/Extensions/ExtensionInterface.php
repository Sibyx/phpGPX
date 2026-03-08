<?php

namespace phpGPX\Models\Extensions;

/**
 * Contract for GPX extension models.
 *
 * Each extension type (e.g., Garmin TrackPointExtension, Style extension)
 * implements this interface to declare its XML namespace, schema, and element
 * name. The {@see \phpGPX\Parsers\ExtensionRegistry} uses this metadata for
 * namespace-based dispatch during parsing and for schema registration during
 * XML serialization.
 *
 * The XML namespace **prefix** (e.g., `gpxtpx`) is a serialization concern —
 * it is configured in the registry, not on the model. During parsing, the
 * prefix is extracted from the source XML. During serialization, the registry
 * provides the configured prefix.
 *
 * ## Implementing a custom extension
 *
 * ```php
 * class MyExtension implements ExtensionInterface
 * {
 *     public static function getNamespace(): string { return 'http://example.com/ext/v1'; }
 *     public static function getSchemaLocation(): string { return 'http://example.com/ext/v1/schema.xsd'; }
 *     public static function getTagName(): string { return 'MyExtension'; }
 *
 *     // Extension-specific properties...
 *     public ?float $value;
 *
 *     public function jsonSerialize(): array { return array_filter(['value' => $this->value ?? null], fn($v) => $v !== null); }
 * }
 * ```
 */
interface ExtensionInterface extends \JsonSerializable
{
	/**
	 * XML namespace URI for this extension.
	 *
	 * Example: `http://www.garmin.com/xmlschemas/TrackPointExtension/v2`
	 */
	public static function getNamespace(): string;

	/**
	 * XSD schema location URL.
	 *
	 * Example: `http://www.garmin.com/xmlschemas/TrackPointExtensionv2.xsd`
	 */
	public static function getSchemaLocation(): string;

	/**
	 * Root XML element name within the `<extensions>` block.
	 *
	 * Example: `TrackPointExtension`
	 */
	public static function getTagName(): string;
}