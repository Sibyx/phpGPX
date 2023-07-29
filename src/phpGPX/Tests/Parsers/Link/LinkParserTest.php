<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Tests\Parsers\Link;

use phpGPX\Models\Link;
use phpGPX\Parsers\LinkParser;
use PHPUnit\Framework\TestCase;

class LinkParserTest extends TestCase
{
	protected Link $link;
    protected \SimpleXMLElement $file;

	protected function setUp(): void
    {
		$this->link = new Link();
        $this->link->href = "https://jakubdubec.me";
        $this->link->text = "Portfolio";
        $this->link->type = "text/html";

        $this->file = simplexml_load_file(sprintf("%s/link.xml", __DIR__));
	}

    /**
     * @covers \phpGPX\Parsers\LinkParser
     * @covers \phpGPX\Models\Link
     * @return void
     */
    public function testParse()
	{
		$links = LinkParser::parse($this->file->link);

        $this->assertNotEmpty($links);
        $this->assertEquals($this->link, $links[0]);

		$this->assertEquals($this->link->href, $links[0]->href);
		$this->assertEquals($this->link->text, $links[0]->text);
		$this->assertEquals($this->link->type, $links[0]->type);

		$this->assertEquals($this->link->toArray(), $links[0]->toArray());
	}


    /**
     * @covers \phpGPX\Parsers\LinkParser
     * @covers \phpGPX\Models\Link
     * @return void
     * @throws \DOMException
     */
    public function testToXML()
    {
        $document = new \DOMDocument("1.0", 'UTF-8');

        $root = $document->createElement("document");
        $root->appendChild(LinkParser::toXML($this->link, $document));

        $document->appendChild($root);

        $this->assertXmlStringEqualsXmlString($this->file->asXML(), $document->saveXML());
    }

    /**
     * @covers \phpGPX\Models\Link
     * @return void
     */
    public function testToJSON()
    {
        $this->assertJsonStringEqualsJsonFile(
            sprintf("%s/link.json", __DIR__), json_encode($this->link->toArray())
        );
    }
}
