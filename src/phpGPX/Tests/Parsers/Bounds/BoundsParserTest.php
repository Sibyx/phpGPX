<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Tests\Parsers\Bounds;

use phpGPX\Models\Bounds;
use phpGPX\Parsers\BoundsParser;
use PHPUnit\Framework\TestCase;

class BoundsParserTest extends TestCase
{
    protected Bounds $bounds;
    protected \SimpleXMLElement $file;

	protected function setUp(): void
    {
        // Example object
		$this->bounds = new Bounds();
        $this->bounds->maxLatitude = 49.090543;
        $this->bounds->maxLongitude = 18.886939;
        $this->bounds->minLatitude = 49.072489;
        $this->bounds->minLongitude = 18.814543;

        // Input file
        $this->file = simplexml_load_file(sprintf("%s/bounds.xml", __DIR__));
	}

    /**
     * @covers \phpGPX
     * @codeCoverageIgnore
     * @return void
     */
    public function testParse()
	{
		$bounds = BoundsParser::parse($this->file->bounds);

        $this->assertEquals($bounds, $this->bounds);
		$this->assertNotEmpty($bounds);

		$this->assertEquals($this->bounds->maxLatitude, $bounds->maxLatitude);
		$this->assertEquals($this->bounds->maxLongitude, $bounds->maxLongitude);
		$this->assertEquals($this->bounds->minLatitude, $bounds->minLatitude);
		$this->assertEquals($this->bounds->minLongitude, $bounds->minLongitude);

		$this->assertEquals($this->bounds->toArray(), $bounds->toArray());
	}

    /**
     * @covers \phpGPX\Parsers\BoundsParser
     * @covers \phpGPX\Models\Bounds
     * @return void
     * @throws \DOMException
     */
    public function testToXML()
    {
        $document = new \DOMDocument("1.0", 'UTF-8');

        $root = $document->createElement("document");
        $root->appendChild(BoundsParser::toXML($this->bounds, $document));

        $document->appendChild($root);

        $this->assertXmlStringEqualsXmlString($this->file->asXML(), $document->saveXML());
    }

    /**
     * @covers \phpGPX\Models\Bounds
     * @return void
     */
    public function testToJSON()
    {
        $this->assertJsonStringEqualsJsonFile(
            sprintf("%s/bounds.json", __DIR__), json_encode($this->bounds->toArray())
        );
    }
}
