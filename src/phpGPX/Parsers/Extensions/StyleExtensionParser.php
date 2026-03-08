<?php
/**
 * Created            27/02/26 23:09
 * @author            Peter Newman
 */

namespace phpGPX\Parsers\Extensions;

use phpGPX\Models\Extensions\StyleExtension;
use phpGPX\Parsers\ExtensionParser;

class StyleExtensionParser
{
	private static $attributeMapper = [
		'color' => [
			'name' => 'color',
			'type' => 'string'
		],
		'opacity' => [
			'name' => 'opacity',
			'type' => 'float'
		],
		'width' => [
			'name' => 'width',
			'type' => 'float'
		],
		'pattern' => [
			'name' => 'pattern',
			'type' => 'string'
		],
		'linecap' => [
			'name' => 'linecap',
			'type' => 'string'
		],
	];

	/**
	 * @param \SimpleXMLElement $node
	 * @return StyleExtension
	 */
	public static function parse($node)
	{
		$extension = new StyleExtension();

		foreach (self::$attributeMapper as $key => $attribute) {
			$extension->{$attribute['name']} = isset($node->$key) ? $node->$key : null;
			if (!is_null($extension->{$attribute['name']})) {
				settype($extension->{$attribute['name']}, $attribute['type']);
			}
		}

		return $extension;
	}

	/**
	 * @param StyleExtension $extension
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(StyleExtension $extension, \DOMDocument &$document)
	{
		$node = $document->createElement("gpxstyle:line");

		ExtensionParser::$usedNamespaces[StyleExtension::EXTENSION_NAME] = [
			'namespace' => StyleExtension::EXTENSION_NAMESPACE,
			'xsd' => StyleExtension::EXTENSION_NAMESPACE_XSD,
			'name' => StyleExtension::EXTENSION_NAME,
			'prefix' => StyleExtension::EXTENSION_NAMESPACE_PREFIX
		];

		foreach (self::$attributeMapper as $key => $attribute) {
			if (!is_null($extension->{$attribute['name']})) {
				$child = $document->createElement(
					sprintf("%s:%s", StyleExtension::EXTENSION_NAMESPACE_PREFIX, $key),
					$extension->{$attribute['name']}
				);
				$node->appendChild($child);
			}
		}

		return $node;
	}
}
