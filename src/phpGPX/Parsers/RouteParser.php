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
abstract class RouteParser
{
	public static $tagName = 'rte';

	private static $attributeMapper = [
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
		'links' => [
			'name' => 'links',
			'type' => 'array'
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
			'type' => 'object'
		],
		'rtept' => [
			'name' => 'points',
			'type' => 'array'
		],
	];

	/**
	 * @param \SimpleXMLElement[] $nodes
	 * @return Route[]
	 */
	public static function parse($nodes)
	{
		$routes = [];

		foreach ($nodes as $node) {
			$route = new Route();

			foreach (self::$attributeMapper as $key => $attribute) {
				switch ($key) {
					case 'link':
						$route->links = isset($node->link) ? LinkParser::parse($node->link) : [];
						break;
					case 'extensions':
						$route->extensions = isset($node->extensions) ? ExtensionParser::parse($node->extensions) : null;
						break;
					case 'rtept':
						$route->points = [];

						if (isset($node->rtept)) {
							foreach ($node->rtept as $point) {
								$route->points[] = PointParser::parse($point);
							}
						}
						break;
					default:
						if (!in_array($attribute['type'], ['object', 'array'])) {
							$route->{$attribute['name']} = isset($node->$key) ? $node->$key : null;
							if (!is_null($route->{$attribute['name']})) {
								settype($route->{$attribute['name']}, $attribute['type']);
							}
						}
						break;
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
	public static function toXML(Route $route, \DOMDocument &$document)
	{
		$node = $document->createElement(self::$tagName);

		foreach (self::$attributeMapper as $key => $attribute) {
			if (!is_null($route->{$attribute['name']})) {
				switch ($key) {
					case 'links':
						$child = LinkParser::toXMLArray($route->links, $document);
						break;
					case 'extensions':
						$child = ExtensionParser::toXML($route->extensions, $document);
						break;
					case 'rtept':
						$child = PointParser::toXMLArray($route->points, $document);
						break;
					default:
						$child = $document->createElement($key);
						$elementText = $document->createTextNode((string) $route->{$attribute['name']});
						$child->appendChild($elementText);
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
	 * @param array $routes
	 * @param \DOMDocument $document
	 * @return \DOMElement[]
	 */
	public static function toXMLArray(array $routes, \DOMDocument &$document)
	{
		$result = [];

		foreach ($routes as $route) {
			$result[] = self::toXML($route, $document);
		}

		return $result;
	}
}
