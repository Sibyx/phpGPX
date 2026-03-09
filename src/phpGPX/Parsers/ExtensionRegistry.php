<?php

namespace phpGPX\Parsers;

use phpGPX\Parsers\Extensions\TrackPointExtensionParser;

/**
 * Registry mapping XML namespace URIs to extension parsers.
 *
 * The registry is the central dispatch mechanism for GPX extensions. During parsing,
 * {@see ExtensionParser} queries the registry to find the correct parser for each
 * namespace encountered in `<extensions>` blocks. During serialization, the registry
 * provides the namespace prefix for XML element names.
 *
 * ## Default registry
 *
 * The `default()` factory registers Garmin TrackPointExtension (both v1 and v2
 * namespaces) with the `gpxtpx` prefix.
 *
 * ## Custom extensions
 *
 * ```php
 * use phpGPX\Parsers\ExtensionRegistry;
 *
 * $registry = ExtensionRegistry::default()
 *     ->register('http://example.com/ext/v1', MyExtensionParser::class, 'myext');
 *
 * $gpx = new phpGPX(extensionRegistry: $registry);
 * ```
 *
 * Multiple namespaces can map to the same parser (e.g., v1 and v2 of the same extension).
 */
class ExtensionRegistry
{
	/** @var array<string, array{parserClass: class-string<Extensions\ExtensionParserInterface>, prefix: string}> */
	private array $entries = [];

	/**
	 * Register a parser for a namespace URI.
	 *
	 * @param string $namespace The XML namespace URI
	 * @param string $parserClass Fully qualified class name implementing ExtensionParserInterface
	 * @param string $prefix XML namespace prefix for serialization (e.g., 'gpxtpx')
	 * @return $this Fluent interface
	 */
	public function register(string $namespace, string $parserClass, string $prefix = 'ext'): self
	{
		$this->entries[$namespace] = ['parserClass' => $parserClass, 'prefix' => $prefix];
		return $this;
	}

	/**
	 * Get the parser class for a namespace, or null if not registered.
	 */
	public function getParserClass(string $namespace): ?string
	{
		return $this->entries[$namespace]['parserClass'] ?? null;
	}

	/**
	 * Get the XML prefix for a namespace, or null if not registered.
	 */
	public function getPrefix(string $namespace): ?string
	{
		return $this->entries[$namespace]['prefix'] ?? null;
	}

	/**
	 * Check if a namespace is registered.
	 */
	public function has(string $namespace): bool
	{
		return isset($this->entries[$namespace]);
	}

	/**
	 * Get all registered namespace → parser mappings.
	 *
	 * @return array<string, array{parserClass: string, prefix: string}>
	 */
	public function all(): array
	{
		return $this->entries;
	}

	/**
	 * Create a registry with the standard built-in extensions.
	 *
	 * Registers Garmin TrackPointExtension for both v1 and v2 namespaces
	 * with the `gpxtpx` prefix.
	 */
	public static function default(): self
	{
		return (new self())
			->register('http://www.garmin.com/xmlschemas/TrackPointExtension/v2', TrackPointExtensionParser::class, 'gpxtpx')
			->register('http://www.garmin.com/xmlschemas/TrackPointExtension/v1', TrackPointExtensionParser::class, 'gpxtpx');
	}
}
