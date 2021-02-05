<?php
/**
 * Created            16/02/2017 23:08
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Models\Person;

/**
 * Class PersonParser
 * @package phpGPX\Parsers
 */
abstract class PersonParser
{
	public static $tagName = 'author';

	/**
	 * @param \SimpleXMLElement $node
	 * @return Person
	 */
	public static function parse(\SimpleXMLElement $node)
	{
		$person = new Person();

		$person->name = isset($node->name) ? ((string) $node->name) : null;
		$person->email = isset($node->email) ? EmailParser::parse($node->email) : null;
		$person->links = isset($node->link) ? LinkParser::parse($node->link) : null;

		return $person;
	}

	public static function toXML(Person $person, \DOMDocument &$document)
	{
		$node =  $document->createElement(self::$tagName);

		if (!empty($person->name)) {
			$child = $document->createElement('name', $person->name);
			$node->appendChild($child);
		}

		if (!empty($person->email)) {
			$child = EmailParser::toXML($person->email, $document);
			$node->appendChild($child);
		}

		# TODO: is_iterable
		if (!is_null($person->links)) {
			foreach ($person->links as $link) {
				$child = LinkParser::toXML($link, $document);
				$node->appendChild($child);
			}
		}

		return $node;
	}
}
