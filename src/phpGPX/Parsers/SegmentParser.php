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
	public static $tagName = 'trkseg';

	/**
	 * @param $nodes \SimpleXMLElement[]
	 * @return Segment[]
	 */
	public static function parse($nodes)
	{
		$segments = [];

		foreach ($nodes as $node) {
			$segment = new Segment();

			if (!$node->count()) {
				continue;
			}

			if (isset($node->trkpt)) {
				$segment->points = [];

				foreach ($node->trkpt as $point) {
					$segment->points[] = PointParser::parse($point);
				}
			}
			$segment->extensions = isset($node->extensions) ? ExtensionParser::parse($node->extensions) : null;

			if (phpGPX::$CALCULATE_STATS) {
				$segment->recalculateStats();
			}

			$segments[] = $segment;
		}

		return $segments;
	}

	/**
	 * @param Segment $segment
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(Segment $segment, \DOMDocument &$document)
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

	/**
	 * @param array $segments
	 * @param \DOMDocument $document
	 * @return \DOMElement[]
	 */
	public static function toXMLArray(array $segments, \DOMDocument $document)
	{
		$result = [];

		foreach ($segments as $segment) {
			$result[] = self::toXML($segment, $document);
		}

		return $result;
	}
}
