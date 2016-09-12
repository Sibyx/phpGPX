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

	/** @var  float */
	public $difference;

	/** @var  float */
	public $distance;

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

	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray()
	{
		return [
			'latitude' => $this->latitude,
			'longitude' => $this->longitude,
			'altitude' => $this->altitude,
			'difference' => $this->difference,
			'distance' => $this->distance,
			'name' => $this->name,
			'timestamp' => $this->timestamp->format("c"),
			'extension' => $this->extension->toArray()
		];
	}

	public function createNode()
	{

	}
}