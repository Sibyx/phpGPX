<?php
/**
 * Created            15/02/2017 18:44
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Models\Link;

abstract class LinkParser
{
	private static $tagName = 'link';

	/**
	 * @param \SimpleXMLElement[] $nodes
	 * @return Link[]
	 */
	public static function parse($nodes = [])
	{
		$links = [];
		foreach ($nodes as $node) {
			$link = new Link();
			$link->href = isset($node['href']) ? (string) $node['href'] : null;
			$link->text = isset($node->text) ? (string) $node->text : null;
			$link->type = isset($node->type) ? (string) $node->type : null;

			$links[] = $link;
		}
		return $links;
	}

	/**
	 * @param Link[] $links
	 * @param \DOMDocument $document
	 * @return \DOMElement[]
	 */
	public static function toXMLArray(array $links, \DOMDocument &$document)
	{
		$result = [];

		foreach ($links as $link) {
			$result[] = self::toXML($link, $document);
		}

		return $result;
	}

	/**
	 * @param Link $link
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(Link $link, \DOMDocument &$document)
	{
		$node =  $document->createElement(self::$tagName);

		$node->setAttribute('href', $link->href);

		if (!empty($link->text)) {
			$child = $document->createElement('text', $link->text);
			$node->appendChild($child);
		}

		if (!empty($link->type)) {
			$child = $document->createElement('type', $link->type);
			$node->appendChild($child);
		}

		return $node;
	}
}
