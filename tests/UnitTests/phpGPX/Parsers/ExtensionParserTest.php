<?php

namespace UnitTests\phpGPX\Parsers;

use phpGPX\Models\Extensions;
use phpGPX\Models\Extensions\TrackPointExtension;
use phpGPX\Parsers\ExtensionParser;

class ExtensionParserTest extends AbstractParserTest
{
	protected $testModelClass = Extension::class;
	protected $testParserClass = ExtensionParser::class;

	/**
	 * @var Extension
	 */
	protected $testModelInstance;

	/**
	 * @return Extension
	 */
	public static function createTestInstance()
	{
		$trackpoint = new TrackPointExtension();
		$trackpoint->aTemp = (float) 14;
		$trackpoint->avgTemperature = (float) 14;
		$trackpoint->hr = (float) 152;
		$trackpoint->heartRate = (float) 152;

		$extensions = new Extensions();
		$extensions->trackPointExtension = $trackpoint;

		return $extensions;
	}

	protected function setUp()
	{
		parent::setUp();

		$this->testModelInstance = self::createTestInstance();
	}

	public function testParse()
	{
		$extensions = ExtensionParser::parse($this->testXmlFile->extensions);

		$this->assertEquals($this->testModelInstance->unsupported, $extensions->unsupported);
		$this->assertEquals($this->testModelInstance->trackPointExtension, $extensions->trackPointExtension);

		$this->assertEquals($this->testModelInstance->toArray(), $extensions->toArray());
	}


	/**
	 * Returns output of ::toXML method of tested parser.
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	protected function convertToXML(\DOMDocument $document)
	{
		return ExtensionParser::toXML($this->testModelInstance, $document);
	}

	public function testToXML()
	{
		$document = new \DOMDocument("1.0", 'UTF-8');

		$root = $document->createElement("document");
		$root->appendChild($this->convertToXML($document));

		$attributes = [
			'xmlns' => 'http://www.topografix.com/GPX/1/1',
			'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
			'xsi:schemaLocation' => 'http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd',
			'xmlns:gpxtpx' => 'http://www.garmin.com/xmlschemas/TrackPointExtension/v1',
			'xmlns:gpxx' => 'http://www.garmin.com/xmlschemas/GpxExtensions/v3',
		];

		foreach ($attributes as $key => $value) {
			$attribute = $document->createAttribute($key);
			$attribute->value = $value;
			$root->appendChild($attribute);
		}

		$document->appendChild($root);

		$this->assertXmlStringEqualsXmlString($this->testXmlFile->asXML(), $document->saveXML());
	}
}
