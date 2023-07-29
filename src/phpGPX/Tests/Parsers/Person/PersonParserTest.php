<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Tests\Parsers\Person;

use phpGPX\Models\Email;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Link;
use phpGPX\Models\Metadata;
use phpGPX\Models\Person;
use phpGPX\Parsers\PersonParser;
use PHPUnit\Framework\TestCase;

class PersonParserTest extends TestCase
{
	protected Person $person;
    protected \SimpleXMLElement $file;

	protected function setUp(): void
    {
		$this->person = new Person();
        $this->person->name = "Jakub Dubec";

        $email = new Email();
        $email->id = "jakub.dubec";
        $email->domain = "gmail.com";
        $this->person->email = $email;

        $link = new Link();
        $link->href = "https://jakubdubec.me";
        $link->text = "Portfolio";
        $link->type = "text/html";
        $this->person->links[] = $link;

        $this->file = simplexml_load_file(sprintf("%s/person.xml", __DIR__));
	}

    /**
     * @covers \phpGPX\Models\Person
     * @covers \phpGPX\Models\Link
     * @covers \phpGPX\Models\Email
     * @covers \phpGPX\Parsers\EmailParser
     * @covers \phpGPX\Parsers\LinkParser
     * @covers \phpGPX\Parsers\PersonParser
     * @covers \phpGPX\Helpers\SerializationHelper
     * @return void
     */
    public function testParse()
	{
		$person = PersonParser::parse($this->file->author);

		$this->assertNotEmpty($person);

		// Primitive attributes
		$this->assertEquals($this->person->name, $person->name);
        $this->assertEquals($this->person, $person);

		// Email
		$this->assertEquals($this->person->email->id, $person->email->id);
		$this->assertEquals($this->person->email->domain, $person->email->domain);

		// Link
		$this->assertEquals($this->person->links[0]->type, $person->links[0]->type);
		$this->assertEquals($this->person->links[0]->text, $person->links[0]->text);
		$this->assertEquals($this->person->links[0]->href, $person->links[0]->href);

		// toArray functions
		$this->assertEquals($this->person->toArray(), $person->toArray());
		$this->assertEquals($this->person->email->toArray(), $person->email->toArray());
		$this->assertEquals($this->person->links[0]->toArray(), $person->links[0]->toArray());
	}

	/**
     * @coversNothing
	 * @url https://github.com/Sibyx/phpGPX/issues/48
	 */
	public function testEmptyLinks()
	{
		$gpx_file = new GpxFile();

		$gpx_file->metadata = new Metadata();
		$gpx_file->metadata->author = new Person();
		$gpx_file->metadata->author->name = "Arthur Dent";

		$this->assertNotNull($gpx_file->toXML()->saveXML());
	}


    /**
     * @covers \phpGPX\Models\Email
     * @covers \phpGPX\Models\Link
     * @covers \phpGPX\Models\Person
     * @covers \phpGPX\Parsers\EmailParser
     * @covers \phpGPX\Parsers\LinkParser
     * @covers \phpGPX\Parsers\PersonParser
     * @return void
     * @throws \DOMException
     */
    public function testToXML()
    {
        $document = new \DOMDocument("1.0", 'UTF-8');

        $root = $document->createElement("document");
        $root->appendChild(PersonParser::toXML($this->person, $document));

        $document->appendChild($root);

        $this->assertXmlStringEqualsXmlString($this->file->asXML(), $document->saveXML());
    }

    /**
     * @covers \phpGPX\Models\Person
     * @covers \phpGPX\Models\Email
     * @covers \phpGPX\Models\Link
     * @covers \phpGPX\Helpers\SerializationHelper
     * @return void
     */
    public function testToJSON()
    {
        $this->assertJsonStringEqualsJsonFile(
            sprintf("%s/person.json", __DIR__), json_encode($this->person->toArray())
        );
    }
}
