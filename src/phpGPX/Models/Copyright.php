<?php
/**
 * Created            16/02/2017 22:20
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

/**
 * Class Copyright
 * Information about the copyright holder and any license governing use of this file.
 * By linking to an appropriate license, you may place your data into the public domain or grant additional usage rights.
 * @package phpGPX\Models
 */
class Copyright implements \JsonSerializable
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


	public function jsonSerialize(): array
	{
		return array_filter([
			'author' => $this->author,
			'year' => $this->year,
			'license' => $this->license,
		], fn($v) => $v !== null);
	}
}
