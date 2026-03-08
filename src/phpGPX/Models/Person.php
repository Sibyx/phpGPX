<?php
/**
 * Created            16/02/2017 22:58
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

/**
 * Class Person
 * A person or organisation
 * @package phpGPX\Models
 */
class Person implements \JsonSerializable
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


	public function jsonSerialize(): array
	{
		return array_filter([
			'name' => $this->name,
			'email' => $this->email,
			'links' => !empty($this->links) ? $this->links : null,
		], fn($v) => $v !== null);
	}
}
