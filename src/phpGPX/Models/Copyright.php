<?php
/**
 * Created            16/02/2017 22:20
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\GpxSerializable;
use phpGPX\Helpers\SerializationHelper;

/**
 * Class Copyright
 * Information about the copyright holder and any license governing use of this file.
 * By linking to an appropriate license, you may place your data into the public domain or grant additional usage rights.
 * @package phpGPX\Models
 */
class Copyright implements \JsonSerializable, GpxSerializable
{

	/**
	 * Copyright holder (TopoSoft, Inc.)
	 * @var string|null
	 */
	public ?string $author;

	/**
	 * Year of copyright.
	 * @var string|null
	 */
	public ?string $year;

	/**
	 * Link to external file containing license text.
	 * @var string|null
	 */
	public ?string $license;

	/**
	 * Copyright constructor.
	 */
	public function __construct()
	{
		$this->author = null;
		$this->year = null;
		$this->license = null;
	}


	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'author' => $this->author,
			'year' => SerializationHelper::stringOrNull($this->year),
			'license' => SerializationHelper::stringOrNull($this->license)
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
