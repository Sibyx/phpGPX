<?php
/**
 * Created            12/09/16 14:48
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Serializers;


use phpGPX\Helpers\Utils;
use phpGPX\Models\Collection;
use phpGPX\Models\Extension;
use phpGPX\Models\Point;
use phpGPX\Models\Segment;

abstract class TrackXmlSerializer
{

	public static function serializeCollection(Collection $collection, \DOMDocument $domDocument)
	{
		$element = $domDocument->createElement("trk");

		if (!empty($collection->source))
		{
			$srcNode = $domDocument->createElement("src", $collection->source);
			$element->appendChild($srcNode);
		}

		if (!empty($collection->url))
		{
			$linkNode = $domDocument->createElement("link");
			$linkNode->setAttribute("href", $collection->url['href']);
			$linkTextNode = $domDocument->createElement("text", $collection->url['text']);
			$linkNode->appendChild($linkTextNode);

			$element->appendChild($linkNode);
		}

		if (!empty($collection->type))
		{
			$typeNode = $domDocument->createElement("type", $collection->type);
			$element->appendChild($typeNode);
		}

		foreach ($collection->segments as $segment)
		{
			$element->appendChild(self::serializeSegment($segment, $domDocument));
		}

		return $element;
	}

	private static function serializeSegment(Segment $segment, \DOMDocument $domDocument)
	{
		$element = $domDocument->createElement("trkseg");

		foreach ($segment->points as $point)
		{
			$element->appendChild(self::serializePoint($point, $domDocument));
		}

		return $element;
	}


	/**
	 * Convert Point object to valid GPX Track Point object.
	 * @param Point $point
	 * @return \DOMElement
	 */
	private static function serializePoint(Point $point, \DOMDocument $domDocument)
	{
		$element = $domDocument->createElement("trkpt");

		// Setting coordinates
		$element->setAttribute("lat", $point->latitude);
		$element->setAttribute("lon", $point->longitude);

		if ($point->timestamp instanceof \DateTime)
		{
			$timeNode = $domDocument->createElement("time", Utils::formatDateTime($point->timestamp, 'Y-m-d\TH:i:s\Z', 'UTC'));
			$element->appendChild($timeNode);
		}

		if (!empty($point->altitude))
		{
			$elevationNode = $domDocument->createElement("ele", $point->altitude);
			$element->appendChild($elevationNode);
		}

		if (!empty($point->name))
		{
			$nameNode = $domDocument->createElement("name", $point->name);
			$element->appendChild($nameNode);
		}

		$extensionNode = self::serializeExtension($point->extension, $domDocument);

		if ($extensionNode instanceof \DOMElement)
		{
			$element->appendChild($extensionNode);
		}

		return $element;
	}

	/**
	 * Convert Extension object to valid Garmin GPX extension object. Returns null if empty extension values.
	 * @param Extension $extension
	 * @return \DOMElement|null
	 */
	private static function serializeExtension(Extension $extension, \DOMDocument $domDocument)
	{
		$element = $domDocument->createElement("extensions");

		$TrackPointExtensionNode = $domDocument->createElement("gpxtpx:TrackPointExtension");

		// Average Temperature
		if (!empty($extension->avgTemperature))
		{
			$aTempNode = $domDocument->createElement("gpxtpx:atemp", $extension->avgTemperature);
			$TrackPointExtensionNode->appendChild($aTempNode);
		}

		if (!empty($extension->cadence))
		{
			$cadNode = $domDocument->createElement("gpxtpx:cad", $extension->cadence);
			$TrackPointExtensionNode->appendChild($cadNode);
		}

		if (!empty($extension->course))
		{
			$courseNode = $domDocument->createElement("gpxtpx:course", $extension->course);
			$TrackPointExtensionNode->appendChild($courseNode);
		}

		if (!empty($extension->heartRate))
		{
			$hrNode = $domDocument->createElement("gpxtpx:hr", $extension->heartRate);
			$TrackPointExtensionNode->appendChild($hrNode);
		}

		if (!empty($extension->speed))
		{
			$speedNode = $domDocument->createElement("gpxtpx:speed", $extension->speed);
			$TrackPointExtensionNode->appendChild($speedNode);
		}

		$element->appendChild($TrackPointExtensionNode);

		return $TrackPointExtensionNode->hasChildNodes() ? $element : null;
	}

}