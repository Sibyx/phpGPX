<?php
/**
 * Created            16/02/2017 22:59
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

/**
 * Class Email
 * An email address. Broken into two parts (id and domain) to help prevent email harvesting.
 * @package phpGPX\Models
 */
class Email implements Summarizable
{

	/**
	 * Id half of email address (jakub.dubec)
	 * @var string
	 */
	public $id;

	/** Domain half of email address (gmail.com)
	 * @var string
	 */
	public $domain;

	/**
	 * Email constructor.
	 */
	public function __construct()
	{
		$this->id = null;
		$this->domain = null;
	}


	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray()
	{
		return [
			'id' => (string) $this->id,
			'domain' => (string) $this->domain
		];
	}
}
