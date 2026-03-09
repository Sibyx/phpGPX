<?php

namespace phpGPX\Tests\Unit\Parsers;

use phpGPX\Models\Link;
use phpGPX\Parsers\LinkParser;
use PHPUnit\Framework\TestCase;

class LinkParserTest extends TestCase
{
	protected Link $link;
	protected \SimpleXMLElement $file;

	private const FIXTURES_DIR = __DIR__ . '/../../Fixtures/Parsers/Link';

	protected function setUp(): void
	{
		$this->link = new Link();
		$this->link->href = 'https://jakubdubec.me';
		$this->link->text = 'Portfolio';
		$this->link->type = 'text/html';

		$this->file = simplexml_load_file(self::FIXTURES_DIR . '/link.xml');
	}

	public function testParse(): void
	{
		$link = LinkParser::parse($this->file->link);

		$this->assertInstanceOf(Link::class, $link);
		$this->assertEquals($this->link->href, $link->href);
		$this->assertEquals($this->link->text, $link->text);
		$this->assertEquals($this->link->type, $link->type);

		$this->assertEquals($this->link->jsonSerialize(), $link->jsonSerialize());
	}

	public function testToXML(): void
	{
		$document = new \DOMDocument('1.0', 'UTF-8');

		$root = $document->createElement('document');
		$root->appendChild(LinkParser::toXML($this->link, $document));

		$document->appendChild($root);

		$this->assertXmlStringEqualsXmlString($this->file->asXML(), $document->saveXML());
	}

	public function testToJSON(): void
	{
		$this->assertJsonStringEqualsJsonFile(
			self::FIXTURES_DIR . '/link.json',
			json_encode($this->link->jsonSerialize()),
		);
	}
}
