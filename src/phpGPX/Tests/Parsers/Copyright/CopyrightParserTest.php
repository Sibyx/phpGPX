<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Tests\Parsers\Copyright;

use phpGPX\Models\Copyright;
use phpGPX\Parsers\CopyrightParser;
use PHPUnit\Framework\TestCase;

class CopyrightParserTest extends TestCase
{
	protected Copyright $copyright;
    protected \SimpleXMLElement $file;

	protected function setUp(): void
    {
		$this->copyright = new Copyright();
        $this->copyright->author = "Jakub Dubec";
        $this->copyright->license = "https://github.com/Sibyx/phpGPX/blob/master/LICENSE";
        $this->copyright->year = '2017';

        // Input file
        $this->file = simplexml_load_file(sprintf("%s/copyright.xml", __DIR__));
	}

    /**
     * @covers \phpGPX\Parsers\CopyrightParser
     * @covers \phpGPX\Helpers\SerializationHelper
     * @covers \phpGPX\Models\Copyright
     * @return void
     */
    public function testParse()
	{
		$copyright = CopyrightParser::parse($this->file->copyright);

		$this->assertEquals($this->copyright, $copyright);
        $this->assertNotEmpty($copyright);

		$this->assertEquals($this->copyright->author, $copyright->author);
		$this->assertEquals($this->copyright->license, $copyright->license);
		$this->assertEquals($this->copyright->year, $copyright->year);

		$this->assertEquals($this->copyright->toArray(), $copyright->toArray());
	}

    /**
     * @covers \phpGPX\Parsers\CopyrightParser
     * @covers \phpGPX\Models\Copyright
     * @return void
     * @throws \DOMException
     */
    public function testToXML()
    {
        $document = new \DOMDocument("1.0", 'UTF-8');

        $root = $document->createElement("document");
        $root->appendChild(CopyrightParser::toXML($this->copyright, $document));

        $document->appendChild($root);

        $this->assertXmlStringEqualsXmlString($this->file->asXML(), $document->saveXML());
    }

    /**
     * @covers \phpGPX\Models\Copyright
     * @covers \phpGPX\Helpers\SerializationHelper
     * @return void
     */
    public function testToJSON()
    {
        $this->assertJsonStringEqualsJsonFile(
            sprintf("%s/copyright.json", __DIR__), json_encode($this->copyright->toArray())
        );
    }
}
