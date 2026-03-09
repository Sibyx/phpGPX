<?php

namespace phpGPX\Models;

use phpGPX\Helpers\DateTimeHelper;

/**
 * Information about the GPX file, author, and copyright restrictions.
 */
class Metadata implements \JsonSerializable
{
	public ?string $name = null;

	public ?string $description = null;

	public ?Person $author = null;

	public ?Copyright $copyright = null;

	/** @var Link[] */
	public array $links = [];

	public ?\DateTime $time = null;

	public ?string $keywords = null;

	public ?Bounds $bounds = null;

	public ?Extensions $extensions = null;

	public function jsonSerialize(): array
	{
		return array_filter([
			'name' => $this->name,
			'desc' => $this->description,
			'author' => $this->author,
			'copyright' => $this->copyright,
			'links' => !empty($this->links) ? $this->links : null,
			'time' => DateTimeHelper::formatDateTime($this->time),
			'keywords' => $this->keywords,
			'bounds' => $this->bounds,
			'extensions' => $this->extensions,
		], fn ($v) => $v !== null);
	}
}
