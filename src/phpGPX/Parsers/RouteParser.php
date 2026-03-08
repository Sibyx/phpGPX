<?php
/**
 * Created            10/02/2017 15:44
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Models\Route;
use phpGPX\phpGPX;

/**
 * Class RouteParser
 * @package phpGPX\Parsers
 */
abstract class RouteParser extends AbstractParser
{
	public static string $tagName = 'rte';

	protected static function getAttributeMapper(): array
	{
		return [
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
			'number' => [
				'name' => 'number',
				'type' => 'integer'
			],
			'type' => [
				'name' => 'type',
				'type' => 'string'
			],
			'extensions' => [
				'name' => 'extensions',
				'type' => 'object',
				'parser' => ExtensionParser::class,
			],
			'rtept' => [
				'name' => 'points',
				'type' => 'array',
				'parser' => PointParser::class,
			],
		];
	}

	/**
	 * @param \SimpleXMLElement $nodes
	 * @return Route[]
	 */
	public static function parse(\SimpleXMLElement $nodes): array
	{
		$routes = [];

		foreach ($nodes as $node) {
			$route = new Route();

			self::mapAttributesFromXML($node, $route);

			foreach (self::getAttributeMapper() as $key => $attribute) {
				if (isset($attribute['parser'])) {
					$route->{$attribute['name']} = self::parseDelegated($node, $key, $attribute);
				}
			}

			if (phpGPX::$CALCULATE_STATS) {
				$route->recalculateStats();
			}

			$routes[] = $route;
		}

		return $routes;
	}

	/**
	 * @param Route $route
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(Route $route, \DOMDocument &$document): \DOMElement
	{
		$node = $document->createElement(self::$tagName);

		self::mapAttributesToXML($route, $document, $node);

		foreach (self::getAttributeMapper() as $key => $attribute) {
			if (isset($attribute['parser'])) {
				self::serializeDelegated($route->{$attribute['name']}, $attribute, $document, $node);
			}
		}

		return $node;
	}

}