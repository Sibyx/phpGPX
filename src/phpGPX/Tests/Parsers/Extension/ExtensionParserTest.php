<?php

namespace phpGPX\Tests\Parsers\Extension;

use phpGPX\Models\Extensions;
use phpGPX\Models\Extensions\TrackPointExtension;
use phpGPX\Parsers\ExtensionParser;
use PHPUnit\Framework\TestCase;

class ExtensionParserTest extends TestCase
{
	protected Extensions $extensions;
    protected \SimpleXMLElement $file;

	protected function setUp(): void
    {
        $trackpoint = new TrackPointExtension();
        $trackpoint->aTemp = (float) 14;
        $trackpoint->avgTemperature = (float) 14;
        $trackpoint->hr = (float) 152;
        $trackpoint->heartRate = (float) 152;

        $this->extensions = new Extensions();
        $this->extensions->trackPointExtension = $trackpoint;

        $this->file = simplexml_load_file(sprintf("%s/extension.xml", __DIR__));
	}

    /**
     * @covers \phpGPX\Parsers\ExtensionParser
     * @covers \phpGPX\Parsers\Extensions\TrackPointExtensionParser
     * @covers \phpGPX\Models\Extensions
     * @covers \phpGPX\Helpers\SerializationHelper
     * @covers \phpGPX\Models\Extensions\AbstractExtension
     * @covers \phpGPX\Models\Extensions\TrackPointExtension
     * @return void
     */
    public function testParse()
	{
		$extensions = ExtensionParser::parse($this->file->extensions);

        $this->assertEquals($this->extensions, $extensions);

        $this->assertEquals($this->extensions->unsupported, $extensions->unsupported);
		$this->assertEquals($this->extensions->trackPointExtension, $extensions->trackPointExtension);

		$this->assertEquals($this->extensions->toArray(), $extensions->toArray());
	}

    /**
     * @covers \phpGPX\Parsers\ExtensionParser
     * @covers \phpGPX\Parsers\Extensions\TrackPointExtensionParser
     * @covers \phpGPX\Models\Extensions
     * @covers \phpGPX\Models\Extensions\AbstractExtension
     * @covers \phpGPX\Models\Extensions\TrackPointExtension
     * @return void
     * @throws \DOMException
     */
    public function testToXML()
	{
		$document = new \DOMDocument("1.0", 'UTF-8');

		$root = $document->createElement("document");
		$root->appendChild(ExtensionParser::toXML($this->extensions, $document));

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

		$this->assertXmlStringEqualsXmlString($this->file->asXML(), $document->saveXML());
	}

    /**
     * @covers \phpGPX\Models\Extensions
     * @covers \phpGPX\Models\Extensions\AbstractExtension
     * @covers \phpGPX\Models\Extensions\TrackPointExtension
     * @covers \phpGPX\Helpers\SerializationHelper
     * @return void
     */
    public function testToJSON()
    {
        $this->assertJsonStringEqualsJsonFile(
            sprintf("%s/extension.json", __DIR__), json_encode($this->extensions->toArray())
        );
    }
}
