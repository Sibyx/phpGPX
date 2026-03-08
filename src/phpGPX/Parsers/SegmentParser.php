<?php
/**
 * Created            17/02/2017 19:29
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Models\Segment;
use phpGPX\phpGPX;

/**
 * Class SegmentParser
 * @package phpGPX\Parsers
 */
abstract class SegmentParser
{
	public static string $tagName = 'trkseg';

	/**
	 * Parse a single track segment node.
	 *
	 * @param \SimpleXMLElement $node
	 * @return Segment|null
	 */
	public static function parse(\SimpleXMLElement $node): ?Segment
	{
		if (!$node->count()) {
			return null;
		}

		$segment = new Segment();

		if (isset($node->trkpt)) {
			foreach ($node->trkpt as $point) {
				$parsed = PointParser::parse($point);
				if ($parsed !== null) {
					$segment->points[] = $parsed;
				}
			}
		}

		$segment->extensions = isset($node->extensions) ? ExtensionParser::parse($node->extensions) : null;

		if (phpGPX::$CALCULATE_STATS) {
			$segment->recalculateStats();
		}

		return $segment;
	}

	/**
	 * @param Segment $segment
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(Segment $segment, \DOMDocument &$document): \DOMElement
	{
		$node = $document->createElement(self::$tagName);

		foreach ($segment->points as $point) {
			$node->appendChild(PointParser::toXML($point, $document));
		}

		if (!empty($segment->extensions)) {
			$node->appendChild(ExtensionParser::toXML($segment->extensions, $document));
		}

		return $node;
	}
}