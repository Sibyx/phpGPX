<?php
/**
 * Created            26/08/16 14:22
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Model;


class Point
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

	/** @var  \DateTime */
	public $timestamp;

	/**
	 * Point constructor.
	 */
	public function __construct()
	{
		$this->extension = new Extension();
	}
}