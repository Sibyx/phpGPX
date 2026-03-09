<?php

namespace phpGPX\Parsers;

use phpGPX\Helpers\DateTimeHelper;
use phpGPX\Models\Point;
use phpGPX\Models\PointType;

abstract class PointParser extends AbstractParser
{
	protected static function getAttributeMapper(): array
	{
		return [
			'ele' => [
				'name' => 'elevation',
				'type' => 'float',
			],
			'time' => [
				'name' => 'time',
				'type' => 'datetime',
			],
			'magvar' => [
				'name' => 'magVar',
				'type' => 'float',
			],
			'geoidheight' => [
				'name' => 'geoidHeight',
				'type' => 'float',
			],
			'name' => [
				'name' => 'name',
				'type' => 'string',
			],
			'cmt' => [
				'name' => 'comment',
				'type' => 'string',
			],
			'desc' => [
				'name' => 'description',
				'type' => 'string',
			],
			'src' => [
				'name' => 'source',
				'type' => 'string',
			],
			'link' => [
				'name' => 'links',
				'type' => 'array',
				'parser' => LinkParser::class,
			],
			'sym' => [
				'name' => 'symbol',
				'type' => 'string',
			],
			'type' => [
				'name' => 'type',
				'type' => 'string',
			],
			'fix' => [
				'name' => 'fix',
				'type' => 'string',
			],
			'sat' => [
				'name' => 'satellitesNumber',
				'type' => 'integer',
			],
			'hdop' => [
				'name' => 'hdop',
				'type' => 'float',
			],
			'vdop' => [
				'name' => 'vdop',
				'type' => 'float',
			],
			'pdop' => [
				'name' => 'pdop',
				'type' => 'float',
			],
			'ageofdgpsdata' => [
				'name' => 'ageOfGpsData',
				'type' => 'float',
			],
			'dgpsid' => [
				'name' => 'dgpsid',
				'type' => 'integer',
			],
			'extensions' => [
				'name' => 'extensions',
				'type' => 'object',
				'parser' => ExtensionParser::class,
			],
		];
	}

	public static function parse(\SimpleXMLElement $node): ?Point
	{
		$pointType = PointType::tryFrom($node->getName());
		if ($pointType === null) {
			return null;
		}

		$point = new Point($pointType);

		$point->latitude = isset($node['lat']) ? ((float) $node['lat']) : null;
		$point->longitude = isset($node['lon']) ? ((float) $node['lon']) : null;

		self::mapAttributesFromXML($node, $point);

		$point->time = isset($node->time) ? DateTimeHelper::parseDateTime($node->time) : null;

		$mapper = self::getAttributeMapper();
		$point->links = self::parseDelegated($node, 'link', $mapper['link']);
		$point->extensions = self::parseDelegated($node, 'extensions', $mapper['extensions']);

		return $point;
	}

	public static function toXML(Point $point, \DOMDocument &$document): \DOMElement
	{
		$node = $document->createElement($point->getPointType()->value);

		if ($point->latitude !== null) {
			$node->setAttribute('lat', $point->latitude);
		}
		if ($point->longitude !== null) {
			$node->setAttribute('lon', $point->longitude);
		}

		self::mapAttributesToXML($point, $document, $node);

		if ($point->time !== null) {
			$child = $document->createElement('time', DateTimeHelper::formatDateTime($point->time));
			$node->appendChild($child);
		}

		$mapper = self::getAttributeMapper();
		self::serializeDelegated($point->links, $mapper['link'], $document, $node);
		self::serializeDelegated($point->extensions, $mapper['extensions'], $document, $node);

		return $node;
	}
}
