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
class Email implements \JsonSerializable
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
}
