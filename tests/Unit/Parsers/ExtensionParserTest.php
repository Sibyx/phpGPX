<?php

namespace phpGPX\Tests\Unit\Parsers;

use phpGPX\Models\Extensions;
use phpGPX\Models\Extensions\TrackPointExtension;
use phpGPX\Parsers\ExtensionParser;
use phpGPX\Parsers\ExtensionRegistry;
use PHPUnit\Framework\TestCase;

class ExtensionParserTest extends TestCase
{
	protected Extensions $extensions;
	protected \SimpleXMLElement $file;

	private const FIXTURES_DIR = __DIR__ . '/../../Fixtures/Parsers/Extension';

	protected function setUp(): void
	{
		$trackpoint = new TrackPointExtension();
		$trackpoint->aTemp = 14.0;
		$trackpoint->hr = 152.0;

		$this->extensions = new Extensions();
		$this->extensions->set($trackpoint);

		$this->file = simplexml_load_file(self::FIXTURES_DIR . '/extension.xml');

		// Configure the registry for parsing
		ExtensionParser::$registry = ExtensionRegistry::default();
	}

	public function testParse(): void
	{
		$extensions = ExtensionParser::parse($this->file->extensions);

		$this->assertEquals($this->extensions->unsupported, $extensions->unsupported);

		$parsed = $extensions->get(TrackPointExtension::class);
		$expected = $this->extensions->get(TrackPointExtension::class);
		$this->assertNotNull($parsed);
		$this->assertEquals($expected->jsonSerialize(), $parsed->jsonSerialize());

		$this->assertJsonStringEqualsJsonString(
			json_encode($this->extensions),
			json_encode($extensions),
		);
	}

	public function testToXML(): void
	{
		$document = new \DOMDocument('1.0', 'UTF-8');

		$root = $document->createElement('document');
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

	public function testToJSON(): void
	{
		$this->assertJsonStringEqualsJsonFile(
			self::FIXTURES_DIR . '/extension.json',
			json_encode($this->extensions->jsonSerialize()),
		);
	}
}
