<?php
/**
 * Created            30/08/16 13:31
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Models\Track;
use phpGPX\phpGPX;

/**
 * Class TrackParser
 * @package phpGPX\Parsers
 */
abstract class TrackParser
{
	public static $tagName = 'trk';

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
		'link' => [
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
		'trkseg' => [
			'name' => 'segments',
			'type' => 'array'
		],
	];

	/**
	 * @param \SimpleXMLElement $nodes
	 * @return Track[]
	 */
	public static function parse(\SimpleXMLElement $nodes)
	{
		$tracks = [];

		foreach ($nodes as $node) {
			$track = new Track();

			foreach (self::$attributeMapper as $key => $attribute) {
				switch ($key) {
					case 'link':
						$track->links = isset($node->link) ? LinkParser::parse($node->link) : [];
						break;
					case 'extensions':
						$track->extensions = isset($node->extensions) ? ExtensionParser::parse($node->extensions) : null;
						break;
					case 'trkseg':
						$track->segments = isset($node->trkseg) ? SegmentParser::parse($node->trkseg) : [];
						break;
					default:
						if (!in_array($attribute['type'], ['object', 'array'])) {
							$track->{$attribute['name']} = isset($node->$key) ? $node->$key : null;
							if (!is_null($track->{$attribute['name']})) {
								settype($track->{$attribute['name']}, $attribute['type']);
							}
						}
						break;
				}
			}

			if (phpGPX::$CALCULATE_STATS) {
				$track->recalculateStats();
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
	public static function toXML(Track $track, \DOMDocument &$document)
	{
		$node = $document->createElement(self::$tagName);

		foreach (self::$attributeMapper as $key => $attribute) {
			if (!is_null($track->{$attribute['name']})) {
				switch ($key) {
					case 'link':
						$child = LinkParser::toXMLArray($track->links, $document);
						break;
					case 'extensions':
						$child = ExtensionParser::toXML($track->extensions, $document);
						break;
					case 'trkseg':
						$child = SegmentParser::toXMLArray($track->segments, $document);
						break;
					default:
						$child = $document->createElement($key);
						$elementText = $document->createTextNode((string) $track->{$attribute['name']});
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
	 * @param array $tracks
	 * @param \DOMDocument $document
	 * @return \DOMElement[]
	 */
	public static function toXMLArray(array $tracks, \DOMDocument &$document)
	{
		$result = [];

		foreach ($tracks as $track) {
			$result[] = self::toXML($track, $document);
		}

		return $result;
	}
}
