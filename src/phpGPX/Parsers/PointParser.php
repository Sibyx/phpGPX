<?php
/**
 * Created            15/02/2017 18:14
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Helpers\DateTimeHelper;
use phpGPX\Models\Point;

abstract class PointParser extends AbstractParser
{
	protected static function getAttributeMapper(): array
	{
		return [
			'ele' => [
				'name' => 'elevation',
				'type' => 'float'
			],
			'time' => [
				'name' => 'time',
				'type' => 'datetime'
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
				'type' => 'array',
				'parser' => LinkParser::class,
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
				'type' => 'object',
				'parser' => ExtensionParser::class,
			]
		];
	}

	private static array $typeMapper = [
		'trkpt' => Point::TRACKPOINT,
		'wpt' => Point::WAYPOINT,
		'rtept' => Point::ROUTEPOINT
	];

	public static function parse(\SimpleXMLElement $node): ?Point
	{
		if (!array_key_exists($node->getName(), self::$typeMapper)) {
			return null;
		}

		$point = new Point(self::$typeMapper[$node->getName()]);

		$point->latitude = isset($node['lat']) ? ((float) $node['lat']) : null;
		$point->longitude = isset($node['lon']) ? ((float) $node['lon']) : null;

		self::mapAttributesFromXML($node, $point);

		// Datetime
		$point->time = isset($node->time) ? DateTimeHelper::parseDateTime($node->time) : null;

		// Delegated parsers
		$mapper = self::getAttributeMapper();
		$point->links = self::parseDelegated($node, 'link', $mapper['link']);
		$point->extensions = self::parseDelegated($node, 'extensions', $mapper['extensions']);

		return $point;
	}

	/**
	 * @param Point $point
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(Point $point, \DOMDocument &$document): \DOMElement
	{
		$node = $document->createElement(array_search($point->getPointType(), self::$typeMapper));

		if ($point->latitude !== null) {
			$node->setAttribute('lat', $point->latitude);
		}
		if ($point->longitude !== null) {
			$node->setAttribute('lon', $point->longitude);
		}

		self::mapAttributesToXML($point, $document, $node);

		// Datetime
		if ($point->time !== null) {
			$child = $document->createElement('time', DateTimeHelper::formatDateTime($point->time));
			$node->appendChild($child);
		}

		// Delegated parsers
		$mapper = self::getAttributeMapper();
		self::serializeDelegated($point->links, $mapper['link'], $document, $node);
		self::serializeDelegated($point->extensions, $mapper['extensions'], $document, $node);

		return $node;
	}

}