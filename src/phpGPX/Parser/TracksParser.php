<?php
/**
 * Created            30/08/16 13:31
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parser;


use phpGPX\Model\Track;

abstract class TracksParser
{

	/**
	 * @param \DOMNode $gpx
	 * @return \phpGPX\Model\Track[]
	 */
	public static function parse(\DOMNode $gpx)
	{

		/** @var Track[] $tracks */
		$tracks = [];

		$children = $gpx->childNodes->length;

		for ($i = 0; $i < $children; $i++)
		{
			$trk = $gpx->childNodes->item($i);
			$tracks[] = self::parse_trk($trk);
		}

		return $tracks;
	}

	/**
	 * @param \DOMNode $trk
	 * @return Track
	 */
	private static function parse_trk(\DOMNode $trk)
	{
		$track = new Track();
		$nodes = $trk->childNodes->length;

		for ($i = 0; $i < $nodes; $i++)
		{
			$node = $trk->childNodes->item($i);

			switch ($node->nodeName)
			{
				case 'src':
					$track->source = $node->nodeValue;
					printf("src: %s", $track->source);
					break;
				case 'type':
					$track->type = $node->nodeValue;
					printf("type: %s", $track->type);
					break;
				case 'link':
					$track->url = $node->attributes->getNamedItem('href')->nodeValue;
					printf("link: %s", $track->url);
					break;
				case 'trkseg':


			}
		}

		return $track;
	}

	private static function parse_trkseg(\DOMNode $trkseg)
	{

	}

}