<?php

namespace phpGPX\Models;

/**
 * An email address. Broken into two parts (id and domain) to help prevent email harvesting.
 */
class Email implements \JsonSerializable
{
	public function __construct(
		public ?string $id = null,
		public ?string $domain = null,
	) {
	}

	public function jsonSerialize(): array
	{
		return array_filter([
			'id' => $this->id,
			'domain' => $this->domain,
		], fn ($v) => $v !== null);
	}
}
