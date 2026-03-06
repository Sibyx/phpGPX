<?php

namespace phpGPX\Tests\Unit\Parsers;

use phpGPX\Models\Copyright;
use phpGPX\Parsers\CopyrightParser;
use PHPUnit\Framework\TestCase;

class CopyrightParserTest extends TestCase
{
	protected Copyright $copyright;
	protected \SimpleXMLElement $file;

	private const FIXTURES_DIR = __DIR__ . '/../../Fixtures/Parsers/Copyright';

	protected function setUp(): void
	{
		$this->copyright = new Copyright();
		$this->copyright->author = "Jakub Dubec";
		$this->copyright->license = "https://github.com/Sibyx/phpGPX/blob/master/LICENSE";
		$this->copyright->year = '2017';

		$this->file = simplexml_load_file(self::FIXTURES_DIR . '/copyright.xml');
	}

	public function testParse(): void
	{
		$copyright = CopyrightParser::parse($this->file->copyright);

		$this->assertEquals($this->copyright, $copyright);
		$this->assertNotEmpty($copyright);

		$this->assertEquals($this->copyright->author, $copyright->author);
		$this->assertEquals($this->copyright->license, $copyright->license);
		$this->assertEquals($this->copyright->year, $copyright->year);

		$this->assertEquals($this->copyright->toArray(), $copyright->toArray());
	}

	public function testToXML(): void
	{
		$document = new \DOMDocument("1.0", 'UTF-8');

		$root = $document->createElement("document");
		$root->appendChild(CopyrightParser::toXML($this->copyright, $document));

		$document->appendChild($root);

		$this->assertXmlStringEqualsXmlString($this->file->asXML(), $document->saveXML());
	}

	public function testToJSON(): void
	{
		$this->assertJsonStringEqualsJsonFile(
			self::FIXTURES_DIR . '/copyright.json', json_encode($this->copyright->toArray())
		);
	}
}