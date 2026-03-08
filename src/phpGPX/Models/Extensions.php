<?php

namespace phpGPX\Models;

use phpGPX\Models\Extensions\ExtensionInterface;

/**
 * Container for GPX extensions on a Point, Track, Route, or GpxFile.
 *
 * Holds registered (typed) extensions keyed by class name, plus an array
 * of unsupported extension data preserved as raw key-value strings for
 * round-trip fidelity.
 *
 * ## Accessing extensions
 *
 * ```php
 * use phpGPX\Models\Extensions\TrackPointExtension;
 *
 * $ext = $point->extensions?->get(TrackPointExtension::class);
 * if ($ext !== null) {
 *     echo $ext->hr; // heart rate
 * }
 * ```
 */
class Extensions implements \JsonSerializable
{
	/** @var array<class-string<ExtensionInterface>, ExtensionInterface> */
	private array $items = [];

	/**
	 * Unsupported extensions preserved as key-value pairs.
	 * Keys are prefixed element names (e.g., "ns:ElementName"), values are string content.
	 * @var array<string, string>
	 */
	public array $unsupported = [];

	/**
	 * Store a typed extension.
	 */
	public function set(ExtensionInterface $extension): void
	{
		$this->items[get_class($extension)] = $extension;
	}

	/**
	 * Retrieve a typed extension by class name.
	 *
	 * @template T of ExtensionInterface
	 * @param class-string<T> $class
	 * @return T|null
	 */
	public function get(string $class): ?ExtensionInterface
	{
		return $this->items[$class] ?? null;
	}

	/**
	 * Check if a typed extension is present.
	 *
	 * @param class-string<ExtensionInterface> $class
	 */
	public function has(string $class): bool
	{
		return isset($this->items[$class]);
	}

	/**
	 * Get all typed extensions.
	 *
	 * @return array<class-string<ExtensionInterface>, ExtensionInterface>
	 */
	public function all(): array
	{
		return $this->items;
	}

	/**
	 * Check if this container has any data (typed or unsupported).
	 */
	public function isEmpty(): bool
	{
		return empty($this->items) && empty($this->unsupported);
	}

	public function jsonSerialize(): mixed
	{
		$result = [];

		foreach ($this->items as $ext) {
			$key = lcfirst($ext::getTagName());
			$result[$key] = $ext;
		}

		if (!empty($this->unsupported)) {
			$result['unsupported'] = $this->unsupported;
		}

		return !empty($result) ? $result : new \stdClass();
	}
}