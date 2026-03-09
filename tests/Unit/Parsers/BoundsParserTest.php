<?php

namespace phpGPX\Tests\Unit\Parsers;

use phpGPX\Models\Bounds;
use phpGPX\Parsers\BoundsParser;
use PHPUnit\Framework\TestCase;

class BoundsParserTest extends TestCase
{
	protected Bounds $bounds;
	protected \SimpleXMLElement $file;

	private const FIXTURES_DIR = __DIR__ . '/../../Fixtures/Parsers/Bounds';

	protected function setUp(): void
	{
		$this->bounds = new Bounds(
			49.072489,
			18.814543,
			49.090543,
			18.886939,
		);

		$this->file = simplexml_load_file(self::FIXTURES_DIR . '/bounds.xml');
	}

	public function testParse(): void
	{
		$bounds = BoundsParser::parse($this->file->bounds);

		$this->assertEquals($bounds, $this->bounds);
		$this->assertNotEmpty($bounds);

		$this->assertEquals($this->bounds->maxLatitude, $bounds->maxLatitude);
		$this->assertEquals($this->bounds->maxLongitude, $bounds->maxLongitude);
		$this->assertEquals($this->bounds->minLatitude, $bounds->minLatitude);
		$this->assertEquals($this->bounds->minLongitude, $bounds->minLongitude);

		$this->assertEquals($this->bounds->jsonSerialize(), $bounds->jsonSerialize());
	}

	public function testToXML(): void
	{
		$document = new \DOMDocument('1.0', 'UTF-8');

		$root = $document->createElement('document');
		$root->appendChild(BoundsParser::toXML($this->bounds, $document));

		$document->appendChild($root);

		$this->assertXmlStringEqualsXmlString($this->file->asXML(), $document->saveXML());
	}
}
