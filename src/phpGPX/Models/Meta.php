<?php
/**
 * Created            13/09/16 10:22
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;


use phpGPX\Helpers\Utils;

class Meta implements Summarizable
{

	/**
	 * Date of GPX creation
	 * @var \DateTime
	 */
	public $time;

	/**
	 * @var array
	 */
	public $author;

	/**
	 * @var array
	 */
	public $link;

	/**
	 * Meta constructor.
	 */
	public function __construct()
	{
		$this->author = [
			'name' => null,
			'email' => null
		];

		$this->link = [
			'href' => null,
			'text' => null
		];

		$this->time = new \DateTime();
	}


	/**
	 * Serialize object to arra
	 * @return array
	 */
	function summary()
	{
		return [
			'time' => Utils::formatDateTime($this->time),
			'author' => $this->author,
			'link' => $this->link
		];
	}
}