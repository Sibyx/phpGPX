<?php

namespace phpGPX\Tests\Unit\Parsers;

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

	private const FIXTURES_DIR = __DIR__ . '/../../Fixtures/Parsers/Person';

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

		$this->file = simplexml_load_file(self::FIXTURES_DIR . '/person.xml');
	}

	public function testParse(): void
	{
		$person = PersonParser::parse($this->file->author);

		$this->assertNotEmpty($person);

		$this->assertEquals($this->person->name, $person->name);
		$this->assertEquals($this->person, $person);

		$this->assertEquals($this->person->email->id, $person->email->id);
		$this->assertEquals($this->person->email->domain, $person->email->domain);

		$this->assertEquals($this->person->links[0]->type, $person->links[0]->type);
		$this->assertEquals($this->person->links[0]->text, $person->links[0]->text);
		$this->assertEquals($this->person->links[0]->href, $person->links[0]->href);

		$this->assertEquals($this->person->jsonSerialize(), $person->jsonSerialize());
		$this->assertEquals($this->person->email->jsonSerialize(), $person->email->jsonSerialize());
		$this->assertEquals($this->person->links[0]->jsonSerialize(), $person->links[0]->jsonSerialize());
	}

	/**
	 * @url https://github.com/Sibyx/phpGPX/issues/48
	 */
	public function testEmptyLinks(): void
	{
		$gpx_file = new GpxFile();

		$gpx_file->metadata = new Metadata();
		$gpx_file->metadata->author = new Person();
		$gpx_file->metadata->author->name = "Arthur Dent";

		$this->assertNotNull($gpx_file->toXML()->saveXML());
	}

	public function testToXML(): void
	{
		$document = new \DOMDocument("1.0", 'UTF-8');

		$root = $document->createElement("document");
		$root->appendChild(PersonParser::toXML($this->person, $document));

		$document->appendChild($root);

		$this->assertXmlStringEqualsXmlString($this->file->asXML(), $document->saveXML());
	}

	public function testToJSON(): void
	{
		$this->assertJsonStringEqualsJsonFile(
			self::FIXTURES_DIR . '/person.json', json_encode($this->person->jsonSerialize())
		);
	}
}