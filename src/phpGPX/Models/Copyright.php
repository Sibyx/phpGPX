<?php

namespace phpGPX\Models;

/**
 * Information about the copyright holder and any license governing use of this file.
 */
class Copyright implements \JsonSerializable
{
	public function __construct(
		public ?string $author = null,
		public ?string $year = null,
		public ?string $license = null,
	) {
	}

	public function jsonSerialize(): array
	{
		return array_filter([
			'author' => $this->author,
			'year' => $this->year,
			'license' => $this->license,
		], fn ($v) => $v !== null);
	}
}
