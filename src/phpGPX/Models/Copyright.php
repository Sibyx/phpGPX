<?php
/**
 * Created            16/02/2017 22:20
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\SerializationHelper;

/**
 * Class Copyright
 * Information about the copyright holder and any license governing use of this file.
 * By linking to an appropriate license, you may place your data into the public domain or grant additional usage rights.
 * @package phpGPX\Models
 */
class Copyright implements Summarizable
{

	/**
	 * Copyright holder (TopoSoft, Inc.)
	 * @var string
	 */
	public $author;

	/**
	 * Year of copyright.
	 * @var string
	 */
	public $year;

	/**
	 * Link to external file containing license text.
	 * @var string
	 */
	public $license;

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
	public function toArray()
	{
		return [
			'author' => $this->author,
			'year' => SerializationHelper::stringOrNull($this->year),
			'license' => SerializationHelper::stringOrNull($this->license)
		];
	}
}
