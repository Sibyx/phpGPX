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
	public float $minLatitude;

	/**
	 * Minimal longitude in file.
	 * @var float
	 */
	public float $minLongitude;

	/**
	 * Maximal latitude in file.
	 * @var float
	 */
	public float $maxLatitude;

	/**
	 * Maximal longitude in file.
	 * @var float
	 */
	public float $maxLongitude;

    /**
     * @param float $minLatitude
     * @param float $minLongitude
     * @param float $maxLatitude
     * @param float $maxLongitude
     */
    public function __construct(float $minLatitude, float $minLongitude, float $maxLatitude, float $maxLongitude)
    {
        $this->minLatitude = $minLatitude;
        $this->minLongitude = $minLongitude;
        $this->maxLatitude = $maxLatitude;
        $this->maxLongitude = $maxLongitude;
    }


    /**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray(): array
    {
		return [
			'minlat' => $this->minLatitude,
			'minlon' => $this->minLongitude,
			'maxlat' => $this->maxLatitude,
			'maxlon' => $this->maxLongitude
		];
	}
}
