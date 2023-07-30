<?php
/**
 * Created            16/02/2017 22:09
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Models\Bounds;

/**
 * Class BoundsParser
 * @package phpGPX\Parsers
 */
abstract class BoundsParser
{
	private static $tagName = 'bounds';

	/**
	 * Parse data from XML.
	 * @param \SimpleXMLElement $node
	 * @return Bounds|null
	 */
	public static function parse(\SimpleXMLElement $node)
	{
		if ($node->getName() != self::$tagName) {
			return null;
		}

        return new Bounds(
            (float) $node['minlat'],
            (float) $node['minlon'],
            (float) $node['maxlat'],
            (float) $node['maxlon']
        );
	}

	/**
	 * Create XML representation.
	 * @param Bounds $bounds
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(Bounds $bounds, \DOMDocument &$document)
	{
		$node =  $document->createElement(self::$tagName);

		if (!is_null($bounds->minLatitude)) {
			$node->setAttribute('minlat', $bounds->minLatitude);
		}

		if (!is_null($bounds->minLongitude)) {
			$node->setAttribute('minlon', $bounds->minLongitude);
		}

		if (!is_null($bounds->maxLatitude)) {
			$node->setAttribute('maxlat', $bounds->maxLatitude);
		}

		if (!is_null($bounds->maxLongitude)) {
			$node->setAttribute('maxlon', $bounds->maxLongitude);
		}

		return $node;
	}
}
