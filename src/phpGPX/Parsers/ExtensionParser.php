<?php

namespace phpGPX\Parsers;

use phpGPX\Models\Extensions;

/**
 * Parses and serializes `<extensions>` blocks using the extension registry.
 *
 * During parsing, each namespace encountered in an `<extensions>` element is
 * looked up in the {@see ExtensionRegistry}. If a parser is registered for that
 * namespace, the child elements are delegated to it. Otherwise, they are stored
 * as unsupported key-value pairs.
 *
 * The registry is set by `phpGPX` before parsing and serialization via the
 * static `$registry` property.
 */
abstract class ExtensionParser
{
	public static string $tagName = 'extensions';

	/** @var array<string, array{namespace: string, xsd: string, name: string, prefix: string}> */
	public static array $usedNamespaces = [];

	/**
	 * The active extension registry. Set by phpGPX before parse/serialize operations.
	 */
	public static ?ExtensionRegistry $registry = null;

	/**
	 * Parse an `<extensions>` XML element into an Extensions container.
	 */
	public static function parse(\SimpleXMLElement $nodes): Extensions
	{
		$extensions = new Extensions();
		$nodeNamespaces = $nodes->getNamespaces(true);
		$registry = self::$registry;

		foreach ($nodeNamespaces as $prefix => $namespace) {
			$parserClass = $registry?->getParserClass($namespace);

			if ($parserClass !== null) {
				foreach ($nodes->children($namespace) as $child) {
					$ext = $parserClass::parse($child);
					$extensions->set($ext);
				}
			} else {
				foreach ($nodes->children($namespace) as $childKey => $value) {
					$extensions->unsupported[$prefix ? "$prefix:$childKey" : "$childKey"] = (string) $value;
				}
			}
		}

		return $extensions;
	}

	/**
	 * Serialize an Extensions container to a DOM element.
	 */
	public static function toXML(Extensions $extensions, \DOMDocument &$document): \DOMElement
	{
		$node = $document->createElement(self::$tagName);
		$registry = self::$registry;

		foreach ($extensions->all() as $ext) {
			$namespace = $ext::getNamespace();
			$parserClass = $registry?->getParserClass($namespace);

			if ($parserClass !== null) {
				$prefix = $registry->getPrefix($namespace) ?? 'ext';
				$child = $parserClass::toXML($ext, $document, $prefix);
				$node->appendChild($child);

				// Register namespace for schema location output in GpxFile::toXML()
				self::$usedNamespaces[$ext::getTagName()] = [
					'namespace' => $namespace,
					'xsd' => $ext::getSchemaLocation(),
					'name' => $ext::getTagName(),
					'prefix' => $prefix,
				];
			}
		}

		if (!empty($extensions->unsupported)) {
			foreach ($extensions->unsupported as $key => $value) {
				$child = $document->createElement($key, $value);
				$node->appendChild($child);
			}
		}

		return $node;
	}
}