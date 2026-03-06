<?php
/**
 * Created            16/02/2017 22:58
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\GpxSerializable;
use phpGPX\Helpers\SerializationHelper;

/**
 * Class Person
 * A person or organisation
 * @package phpGPX\Models
 */
class Person implements \JsonSerializable, GpxSerializable
{

	/**
	 * Name of person or organization.
	 * An original GPX 1.1 attribute.
	 * @var string|null
	 */
	public ?string $name;

	/**
	 * E-mail address.
	 * An original GPX 1.1 attribute.
	 * @var Email|null
	 */
	public ?Email $email;

	/**
	 * Link to Web site or other external information about person.
	 * An original GPX 1.1 attribute.
	 * @var Link[]|null
	 */
	public ?array $links;

	/**
	 * Person constructor.
	 */
	public function __construct()
	{
		$this->name = null;
		$this->email = null;
		$this->links = null;
	}


	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'name' => (string) $this->name,
			'email' => SerializationHelper::serialize($this->email),
			'links' => SerializationHelper::serialize($this->links)
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
