<?php
/**
 * Created            15/02/2017 18:14
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Helpers\DateTimeHelper;
use phpGPX\Models\Point;

abstract class PointParser
{
	private static $attributeMapper = [
		'ele' => [
			'name' => 'elevation',
			'type' => 'float'
		],
		'time' => [
			'name' => 'time',
			'type' => 'object'
		],
		'magvar' => [
			'name' => 'magVar',
			'type' => 'float'
		],
		'geoidheight' => [
			'name' => 'geoidHeight',
			'type' => 'float'
		],
		'name' => [
			'name' => 'name',
			'type' => 'string'
		],
		'cmt' => [
			'name' => 'comment',
			'type' => 'string'
		],
		'desc' => [
			'name' => 'description',
			'type' => 'string'
		],
		'src' => [
			'name' => 'source',
			'type' => 'string'
		],
		'link' => [
			'name' => 'links',
			'type' => 'object'
		],
		'sym' => [
			'name' => 'symbol',
			'type' => 'string'
		],
		'type' => [
			'name' => 'type',
			'type' => 'string'
		],
		'fix' => [
			'name' => 'fix',
			'type' => 'string'
		],
		'sat' => [
			'name' => 'satellitesNumber',
			'type' => 'integer'
		],
		'hdop' => [
			'name' => 'hdop',
			'type' => 'float'
		],
		'vdop' => [
			'name' => 'vdop',
			'type' => 'float'
		],
		'pdop' => [
			'name' => 'pdop',
			'type' => 'float'
		],
		'ageofdgpsdata' => [
			'name' => 'ageOfGpsData',
			'type' => 'float'
		],
		'dgpsid' => [
			'name' => 'dgpsid',
			'type' => 'integer'
		],
		'extensions' => [
			'name' => 'extensions',
			'type' => 'object'
		]
	];

	private static $typeMapper = [
		'trkpt' => Point::TRACKPOINT,
		'wpt' => Point::WAYPOINT,
		'rtept' => Point::ROUTEPOINT
	];

	public static function parse(\SimpleXMLElement $node)
	{
		if (!array_key_exists($node->getName(), self::$typeMapper)) {
			return null;
		}

		$point = new Point(self::$typeMapper[$node->getName()]);

		$point->latitude = isset($node['lat']) ? ((float) $node['lat']) : null;
		$point->longitude = isset($node['lon']) ? ((float) $node['lon']) : null;

		foreach (self::$attributeMapper as $key => $attribute) {
			switch ($key) {
				case 'time':
					$point->time = isset($node->time) ? DateTimeHelper::parseDateTime($node->time) : null;
					break;
				case 'extensions':
					$point->extensions = isset($node->extensions) ? ExtensionParser::parse($node->extensions) : null;
					break;
				case 'link':
					$point->links = isset($node->link) ? LinkParser::parse($node->link) : [];
					break;
				default:
					if (!in_array($attribute['type'], ['object', 'array'])) {
						$point->{$attribute['name']} = isset($node->$key) ? $node->$key : null;
						if (!is_null($point->{$attribute['name']})) {
							settype($point->{$attribute['name']}, $attribute['type']);
						}
					}
					break;
			}
		}

		return $point;
	}

	/**
	 * @param Point $point
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(Point $point, \DOMDocument &$document)
	{
		$node = $document->createElement(array_search($point->getPointType(), self::$typeMapper));

		$node->setAttribute('lat', $point->latitude);
		$node->setAttribute('lon', $point->longitude);

		foreach (self::$attributeMapper as $key => $attribute) {
			if (!is_null($point->{$attribute['name']})) {
				switch ($key) {
					case 'link':
						$child = LinkParser::toXMLArray($point->links, $document);
						break;
					case 'time':
						$child = $document->createElement('time', DateTimeHelper::formatDateTime($point->time));
						break;
					case 'extensions':
						$child = ExtensionParser::toXML($point->extensions, $document);
						break;
					default:
						$child = $document->createElement($key);
						$elementText = $document->createTextNode((string) $point->{$attribute['name']});
						$child->appendChild($elementText);
						break;
						break;
				}

				if (is_array($child)) {
					foreach ($child as $item) {
						$node->appendChild($item);
					}
				} else {
					$node->appendChild($child);
				}
			}
		}

		return $node;
	}

	/**
	 * @param array $points
	 * @param \DOMDocument $document
	 * @return \DOMElement[]
	 */
	public static function toXMLArray(array $points, \DOMDocument &$document)
	{
		$result = [];

		foreach ($points as $point) {
			$result[] = self::toXML($point, $document);
		}

		return $result;
	}
}
