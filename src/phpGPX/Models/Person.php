<?php

namespace phpGPX\Models;

/**
 * A person or organisation.
 */
class Person implements \JsonSerializable
{
	public function __construct(
		public ?string $name = null,
		public ?Email $email = null,
		/** @var Link[]|null */
		public ?array $links = null,
	) {}

	public function jsonSerialize(): array
	{
		return array_filter([
			'name' => $this->name,
			'email' => $this->email,
			'links' => !empty($this->links) ? $this->links : null,
		], fn($v) => $v !== null);
	}
}