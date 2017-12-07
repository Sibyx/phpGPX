<?php
/**
 * Created            16/02/2017 22:03
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

class Bounds implements Summarizable
{

	/**
	 * Minimal latitude in file.
	 * @var float
	 */
	public $minLatitude;

	/**
	 * Minimal longitude in file.
	 * @var float
	 */
	public $minLongitude;

	/**
	 * Maximal latitude in file.
	 * @var float
	 */
	public $maxLatitude;

	/**
	 * Maximal longitude in file.
	 * @var float
	 */
	public $maxLongitude;

	/**
	 * Bounds constructor.
	 */
	public function __construct()
	{
		$this->minLatitude = null;
		$this->minLongitude = null;
		$this->maxLongitude = null;
		$this->maxLatitude = null;
	}


	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray()
	{
		return [
			'minlat' => $this->minLatitude,
			'minlon' => $this->minLongitude,
			'maxlat' => $this->maxLatitude,
			'maxlon' => $this->maxLongitude
		];
	}
}
