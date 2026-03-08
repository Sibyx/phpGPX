<?php
/**
 * Created            16/02/2017 16:32
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers\Extensions;

use phpGPX\Models\Extensions\TrackPointExtension;
use phpGPX\Parsers\AbstractParser;
use phpGPX\Parsers\ExtensionParser;

class TrackPointExtensionParser extends AbstractParser
{
	protected static function getAttributeMapper(): array
	{
		return [
			'atemp' => [
				'name' => 'aTemp',
				'type' => 'float'
			],
			'wtemp' => [
				'name' => 'wTemp',
				'type' => 'float'
			],
			'depth' => [
				'name' => 'depth',
				'type' => 'float'
			],
			'hr' => [
				'name' => 'hr',
				'type' => 'float'
			],
			'cad' => [
				'name' => 'cad',
				'type' => 'float'
			],
			'speed' => [
				'name' => 'speed',
				'type' => 'float'
			],
			'course' => [
				'name' => 'course',
				'type' => 'int'
			],
			'bearing' => [
				'name' => 'bearing',
				'type' => 'int'
			]
		];
	}

	/**
	 * @param \SimpleXMLElement $node
	 * @return TrackPointExtension
	 */
	public static function parse(\SimpleXMLElement $node): TrackPointExtension
	{
		$extension = new TrackPointExtension();

		self::mapAttributesFromXML($node, $extension);

		return $extension;
	}

	/**
	 * @param TrackPointExtension $extension
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(TrackPointExtension $extension, \DOMDocument &$document): \DOMElement
	{
		$node = $document->createElement("gpxtpx:TrackPointExtension");

		ExtensionParser::$usedNamespaces[TrackPointExtension::EXTENSION_NAME] = [
			'namespace' => TrackPointExtension::EXTENSION_NAMESPACE,
			'xsd' => TrackPointExtension::EXTENSION_NAMESPACE_XSD,
			'name' => TrackPointExtension::EXTENSION_NAME,
			'prefix' => TrackPointExtension::EXTENSION_NAMESPACE_PREFIX
		];

		foreach (self::getAttributeMapper() as $key => $attribute) {
			if (isset($extension->{$attribute['name']})) {
				$child = $document->createElement(
					sprintf("%s:%s", TrackPointExtension::EXTENSION_NAMESPACE_PREFIX, $key),
					$extension->{$attribute['name']}
				);
				$node->appendChild($child);
			}
		}

		return $node;
	}
}