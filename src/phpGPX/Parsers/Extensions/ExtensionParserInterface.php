<?php

namespace phpGPX\Parsers\Extensions;

use phpGPX\Models\Extensions\ExtensionInterface;

/**
 * Contract for extension parsers.
 *
 * Each registered extension type has a corresponding parser that handles
 * XML ↔ model conversion. The {@see \phpGPX\Parsers\ExtensionRegistry}
 * dispatches to the correct parser based on namespace URI.
 *
 * ## Implementing a custom extension parser
 *
 * ```php
 * class MyExtensionParser extends AbstractParser implements ExtensionParserInterface
 * {
 *     public static function parse(\SimpleXMLElement $node): MyExtension { ... }
 *     public static function toXML(ExtensionInterface $extension, \DOMDocument &$document, string $prefix): \DOMElement { ... }
 * }
 * ```
 */
interface ExtensionParserInterface
{
	/**
	 * Parse an XML element into an extension model.
	 *
	 * @param \SimpleXMLElement $node The extension's root element (e.g., `<gpxtpx:TrackPointExtension>`)
	 * @return ExtensionInterface The populated extension model
	 */
	public static function parse(\SimpleXMLElement $node): ExtensionInterface;

	/**
	 * Serialize an extension model to a DOM element.
	 *
	 * The prefix is provided by the registry — extension parsers should use it
	 * for element names (e.g., `$prefix:TrackPointExtension`) rather than
	 * hardcoding a prefix.
	 *
	 * @param ExtensionInterface $extension The extension model to serialize
	 * @param \DOMDocument $document The parent DOM document
	 * @param string $prefix XML namespace prefix from the registry
	 * @return \DOMElement The serialized XML element
	 */
	public static function toXML(ExtensionInterface $extension, \DOMDocument &$document, string $prefix): \DOMElement;
}