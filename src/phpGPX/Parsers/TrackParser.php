<?php
/**
 * Created            30/08/16 13:31
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Models\Track;

/**
 * Class TrackParser
 * @package phpGPX\Parsers
 */
abstract class TrackParser extends AbstractParser
{
	public static string $tagName = 'trk';

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
			'trkseg' => [
				'name' => 'segments',
				'type' => 'array',
				'parser' => SegmentParser::class,
			],
		];
	}

	/**
	 * @param \SimpleXMLElement $nodes
	 * @return Track[]
	 */
	public static function parse(\SimpleXMLElement $nodes): array
	{
		$tracks = [];

		foreach ($nodes as $node) {
			$track = new Track();

			self::mapAttributesFromXML($node, $track);

			foreach (self::getAttributeMapper() as $key => $attribute) {
				if (isset($attribute['parser'])) {
					$track->{$attribute['name']} = self::parseDelegated($node, $key, $attribute);
				}
			}

			$tracks[] = $track;
		}

		return $tracks;
	}

	/**
	 * @param Track $track
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(Track $track, \DOMDocument &$document): \DOMElement
	{
		$node = $document->createElement(self::$tagName);

		self::mapAttributesToXML($track, $document, $node);

		foreach (self::getAttributeMapper() as $key => $attribute) {
			if (isset($attribute['parser'])) {
				self::serializeDelegated($track->{$attribute['name']}, $attribute, $document, $node);
			}
		}

		return $node;
	}

}