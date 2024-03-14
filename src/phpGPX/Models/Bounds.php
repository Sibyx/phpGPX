<?php
/**
 * Created            16/02/2017 22:03
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\GpxSerializable;

class Bounds implements \JsonSerializable, GpxSerializable
{
    public const TAG_NAME = 'bounds';

	/**
	 * Minimal latitude in file.
	 * @var float|null
	 */
	public ?float $minLatitude;

	/**
	 * Minimal longitude in file.
	 * @var float|null
	 */
	public ?float $minLongitude;

	/**
	 * Maximal latitude in file.
	 * @var float|null
	 */
	public ?float $maxLatitude;

	/**
	 * Maximal longitude in file.
	 * @var float|null
	 */
	public ?float $maxLongitude;

    /**
     * @param float|null $minLatitude
     * @param float|null $minLongitude
     * @param float|null $maxLatitude
     * @param float|null $maxLongitude
     */
    public function __construct(?float $minLatitude, ?float $minLongitude, ?float $maxLatitude, ?float $maxLongitude)
    {
        $this->minLatitude = $minLatitude;
        $this->minLongitude = $minLongitude;
        $this->maxLatitude = $maxLatitude;
        $this->maxLongitude = $maxLongitude;
    }

    /**
     * GeoJSON serializer
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [$this->minLongitude, $this->minLatitude, $this->maxLongitude, $this->maxLatitude];
    }

    public static function parse(\SimpleXMLElement $node): ?Bounds
    {
        if ($node->getName() != self::TAG_NAME) {
            return null;
        }

        return new Bounds(
            (float) $node['minlat'],
            (float) $node['minlon'],
            (float) $node['maxlat'],
            (float) $node['maxlon']
        );
    }
}
