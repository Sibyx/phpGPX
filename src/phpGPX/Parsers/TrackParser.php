<?php
/**
 * Created            30/08/16 13:31
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;


use phpGPX\Helpers\StatsHelper;
use phpGPX\Helpers\Utils;
use phpGPX\Models\Extension;
use phpGPX\Models\Segment;
use phpGPX\Models\Collection;
use phpGPX\Models\Point;
use phpGPX\phpGPX;

abstract class TrackParser
{

	/**
	 * @param \SimpleXMLElement $nodes
	 * @return \phpGPX\Models\Collection[]
	 */
	public static function parse(\SimpleXMLElement $nodes)
	{
		$tracks = [];

		foreach ($nodes as $trk)
		{
			$tracks[] = self::parseNode($trk);
		}

		return $tracks;
	}

	/**
	 * @param \SimpleXMLElement $node
	 * @return Collection
	 */
	private static function parseNode(\SimpleXMLElement $node)
	{
		$track = new Collection(Collection::TRACK_COLLECTION);

		if (isset($node->src))
		{
			$track->source = (string) $node->src;
		}

		if (isset($node->link))
		{
			$track->url['href'] = (string) $node->link['href'];
			$track->url['text'] = (string) $node->link->text;
		}

		if (isset($node->type))
		{
			$track->type = (string) $node->type;
		}

		if (isset($node->trkseg))
		{
			foreach ($node->trkseg as $seg)
			{
				$track->segments[] = self::parseSegment($seg);
			}
		}

		StatsHelper::recalculateStats($track);

		return $track;
	}

	private static function parseSegment(\SimpleXMLElement $seg)
	{
		$segment = new Segment(Collection::TRACK_COLLECTION);

		foreach ($seg as $pt)
		{
			$point = new Point(Collection::TRACK_COLLECTION);

			$point->latitude = isset($pt['lat']) ? ((double) $pt['lat']) : null;
			$point->longitude = isset($pt['lon']) ? ((double) $pt['lon']) : null;
			$point->altitude = isset($pt->ele) ? ((double) $pt->ele) : null;
			$point->name = isset($pt->name) ? ((string) $pt->name) : null;

			if (isset($pt->time))
			{
				$point->timestamp = new \DateTime($pt->time);
			}

			if (isset($pt->extensions))
			{
				$point->extension = self::parseExtensions($pt->extensions);
			}

			$segment->points[] = $point;
		}

		if (phpGPX::$SORT_BY_TIMESTAMP)
		{
			usort($segment->points, array(Utils::class, 'comparePointsByTimestamp'));
		}

		return $segment;
	}

	/**
	 * @param \SimpleXMLElement $ext
	 * @return Extension
	 */
	private static function parseExtensions(\SimpleXMLElement $ext)
	{
		$extension = new Extension();
		$ns = $ext->getNamespaces(true);

		$trackPointExtension = $ext->children($ns['gpxtpx'])->TrackPointExtension;

		if (!empty($trackPointExtension))
		{
			$extension->heartRate = isset($trackPointExtension->hr) ? ((double) $trackPointExtension->hr) : null; //check
			$extension->avgTemperature = isset($trackPointExtension->atemp) ? ((double) $trackPointExtension->atemp) : null; //check
			$extension->cadence = isset($trackPointExtension->cad) ? ((double) $trackPointExtension->cad) : null; //check
//			$extension->course = isset($trackPointExtension->hr) ? ((double) $trackPointExtension->hr) : null;
//			$extension->speed = isset($trackPointExtension->hr) ? ((double) $trackPointExtension->hr) : null;
		}

		return $extension;
	}

}