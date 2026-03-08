<?php

namespace phpGPX\Models;

/**
 * Abstract base class for Track and Route.
 */
abstract class Collection implements \JsonSerializable
{
	/** GPS name of route / track. */
	public ?string $name = null;

	/** GPS comment for route. */
	public ?string $comment = null;

	/** Text description of route/track for user. */
	public ?string $description = null;

	/** Source of data. */
	public ?string $source = null;

	/** @var Link[] Links to external information. */
	public array $links = [];

	/** GPS route/track number. */
	public ?int $number = null;

	/** Type (classification) of route/track. */
	public ?string $type = null;

	/** GPX extensions. */
	public ?Extensions $extensions = null;

	/** Calculated statistics. */
	public ?Stats $stats = null;

	/** @return Point[] */
	abstract public function getPoints(): array;
}