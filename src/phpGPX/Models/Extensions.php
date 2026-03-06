<?php
/**
 * Created            15/02/2017 19:00
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\GpxSerializable;
use phpGPX\Helpers\SerializationHelper;
use phpGPX\Models\Extensions\TrackPointExtension;

/**
 * Class Extensions
 * TODO: http://www.garmin.com/xmlschemas/GpxExtensions/v3
 * @package phpGPX\Models
 */
class Extensions implements \JsonSerializable, GpxSerializable
{
	/**
	 * GPX Garmin TrackPointExtension v1
	 * @see 'http://www.garmin.com/xmlschemas/TrackPointExtension/v1'
	 * @var TrackPointExtension|null
	 */
	public ?TrackPointExtension $trackPointExtension;

	/**
	 * @var array
	 */
	public array $unsupported = [];

	/**
	 * Extensions constructor.
	 */
	public function __construct()
	{
		$this->trackPointExtension = null;
	}

	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray(): array
	{
		return [
				'trackpoint' => SerializationHelper::serialize($this->trackPointExtension),
				'unsupported' => $this->unsupported,
			];
	}

	/**
	 * Implements JsonSerializable interface
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return $this->toArray();
	}

	/**
	 * GPX serializer
	 * @param \SimpleXMLElement $node
	 * @return void
	 */
	public static function gpxSerialize(\SimpleXMLElement $node): void
	{
		// Implementation required by GpxSerializable interface
	}

	/**
	 * GPX deserializer
	 * @param \DOMDocument $document
	 * @return void
	 */
	public function gpxDeserialize(\DOMDocument &$document): void
	{
		// Implementation required by GpxSerializable interface
	}
}
