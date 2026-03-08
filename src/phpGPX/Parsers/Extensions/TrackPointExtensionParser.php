<?php
/**
 * Created            16/02/2017 16:32
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers\Extensions;

use phpGPX\Models\Extensions\ExtensionInterface;
use phpGPX\Models\Extensions\TrackPointExtension;
use phpGPX\Parsers\AbstractParser;

class TrackPointExtensionParser extends AbstractParser implements ExtensionParserInterface
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

	public static function parse(\SimpleXMLElement $node): ExtensionInterface
	{
		$extension = new TrackPointExtension();

		self::mapAttributesFromXML($node, $extension);

		return $extension;
	}

	public static function toXML(ExtensionInterface $extension, \DOMDocument &$document, string $prefix = 'gpxtpx'): \DOMElement
	{
		$node = $document->createElement(sprintf("%s:%s", $prefix, $extension::getTagName()));

		foreach (self::getAttributeMapper() as $key => $attribute) {
			if (isset($extension->{$attribute['name']})) {
				$child = $document->createElement(
					sprintf("%s:%s", $prefix, $key),
					$extension->{$attribute['name']}
				);
				$node->appendChild($child);
			}
		}

		return $node;
	}
}