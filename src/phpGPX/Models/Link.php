<?php
/**
 * Created            14/02/2017 18:17
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\GpxSerializable;

/**
 * Class Link according to GPX 1.1 specification.
 * A link to an external resource (Web page, digital photo, video clip, etc) with additional information.
 * @see http://www.topografix.com/GPX/1/1/#type_linkType
 * @package phpGPX\Models
 */
class Link implements \JsonSerializable, GpxSerializable
{

	/**
	 * URL of hyperlink.
	 * @var string|null
	 */
	public ?string $href = null;

	/**
	 * Text of hyperlink.
	 * @var string|null
	 */
	public ?string $text;

	/**
	 * Mime type of content (image/jpeg)
	 * @var string|null
	 */
	public ?string $type;

	/**
	 * Link constructor.
	 */
	public function __construct()
	{
		$this->href = null;
		$this->text = null;
		$this->type = null;
	}


	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'href' => $this->href !== null ? (string) $this->href : null,
			'text' => $this->text,
			'type' => $this->type
		];
	}

	/**
	 * Implements JsonSerializable interface
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return $this->toArray();
	}

	/**
	 * GPX serializer
	 * @param \SimpleXMLElement $node
	 * @return void
	 */
	public static function gpxSerialize(\SimpleXMLElement $node): void
	{
		// Implementation required by GpxSerializable interface
	}

	/**
	 * GPX deserializer
	 * @param \DOMDocument $document
	 * @return void
	 */
	public function gpxDeserialize(\DOMDocument &$document): void
	{
		// Implementation required by GpxSerializable interface
	}
}
