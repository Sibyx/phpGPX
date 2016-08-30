<?php
/**
 * Created            26/08/16 16:46
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Model;


abstract class Point
{
	/** @var  float */
	public $latitude;

	/** @var  float */
	public $longitude;

	/** @var  float */
	public $altitude;

	/** @var  string */
	public $name;

	/** @var  Extension */
	public $extension;

	/**
	 * Point constructor.
	 */
	public function __construct()
	{
		$this->extension = new Extension();
	}
}