<?php
/**
 * Created            16/02/2017 22:58
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\SerializationHelper;


/**
 * Class Person
 * A person or organisation
 * @package phpGPX\Models
 */
class Person implements Summarizable
{

	/**
	 * Name of person or organization.
	 * An original GPX 1.1 attribute.
	 * @var string
	 */
	public $name;

	/**
	 * E-mail address.
	 * An original GPX 1.1 attribute.
	 * @var Email|null
	 */
	public $email;

	/**
	 * Link to Web site or other external information about person.
	 * An original GPX 1.1 attribute.
	 * @var Link|null
	 */
	public $link;

	/**
	 * Person constructor.
	 */
	public function __construct()
	{
		$this->name = null;
		$this->email = null;
		$this->link = null;
	}


	/**
	 * Serialize object to array
	 * @return array
	 */
	function toArray()
	{
		return [
			'name' => (string) $this->name,
			'email' => SerializationHelper::serialize($this->email),
			'link' => SerializationHelper::serialize($this->link)
		];
	}
}