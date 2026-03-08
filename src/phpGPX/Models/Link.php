<?php

namespace phpGPX\Models;

/**
 * A link to an external resource (Web page, digital photo, video clip, etc) with additional information.
 * @see http://www.topografix.com/GPX/1/1/#type_linkType
 */
class Link implements \JsonSerializable
{
	public function __construct(
		public ?string $href = null,
		public ?string $text = null,
		public ?string $type = null,
	) {}

	public function jsonSerialize(): array
	{
		return array_filter([
			'href' => $this->href,
			'text' => $this->text,
			'type' => $this->type,
		], fn($v) => $v !== null);
	}
}