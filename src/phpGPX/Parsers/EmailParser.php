<?php
/**
 * Created            16/02/2017 23:02
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Models\Email;

/**
 * Class EmailParser
 * @package phpGPX\Parsers
 */
abstract class EmailParser
{
	private static string $tagName = 'email';

	/**
	 * @param \SimpleXMLElement $node
	 * @return Email
	 */
	public static function parse(\SimpleXMLElement $node): Email
	{
		$email = new Email();

		$email->id = isset($node['id']) ? (string) $node['id'] : null;
		$email->domain = isset($node['domain']) ? (string) $node['domain'] : null;

		return $email;
	}


	/**
	 * @param Email $email
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(Email $email, \DOMDocument &$document): \DOMElement
	{
		$node =  $document->createElement(self::$tagName);

		if ($email->id !== null && $email->id !== '') {
			$node->setAttribute('id', $email->id);
		}

		if ($email->domain !== null && $email->domain !== '') {
			$node->setAttribute('domain', $email->domain);
		}

		return $node;
	}
}
