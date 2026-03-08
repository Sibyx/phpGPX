<?php
/**
 * Created            16/02/2017 22:59
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\GpxSerializable;

/**
 * Class Email
 * An email address. Broken into two parts (id and domain) to help prevent email harvesting.
 * @package phpGPX\Models
 */
class Email implements \JsonSerializable, GpxSerializable
{

	/**
	 * Id half of email address (jakub.dubec)
	 * @var string|null
	 */
	public ?string $id = null;

	/** Domain half of email address (gmail.com)
	 * @var string|null
	 */
	public ?string $domain = null;

	/**
	 * Email constructor.
	 */
	public function __construct()
	{
		$this->id = null;
		$this->domain = null;
	}


	public function jsonSerialize(): array
	{
		return array_filter([
			'id' => $this->id,
			'domain' => $this->domain,
		], fn($v) => $v !== null);
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
