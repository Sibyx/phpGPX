<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace UnitTests\phpGPX\Parsers;

use phpGPX\Models\GpxFile;
use phpGPX\Models\Metadata;
use phpGPX\Models\Person;
use phpGPX\Parsers\PersonParser;

class PersonParserTest extends AbstractParserTest
{
	protected $testModelClass = Person::class;
	protected $testParserClass = PersonParser::class;

	/**
	 * @var Person
	 */
	protected $testModelInstance;

	public static function createTestInstance()
	{
		$person = new Person();
		$person->name = "Jakub Dubec";
		$person->email = EmailParserTest::createTestInstance();
		$person->links[] = LinkParserTest::createTestInstance();
		$person->name = 'Jakub Dubec';

		return $person;
	}

	protected function setUp()
	{
		parent::setUp();

		$this->testModelInstance = self::createTestInstance();
	}

	public function testParse()
	{
		$person = PersonParser::parse($this->testXmlFile->author);

		$this->assertNotEmpty($person);

		// Primitive attributes
		$this->assertEquals($this->testModelInstance->name, $person->name);

		// Email
		$this->assertEquals($this->testModelInstance->email->id, $person->email->id);
		$this->assertEquals($this->testModelInstance->email->domain, $person->email->domain);

		// Link
		$this->assertEquals($this->testModelInstance->links[0]->type, $person->links[0]->type);
		$this->assertEquals($this->testModelInstance->links[0]->text, $person->links[0]->text);
		$this->assertEquals($this->testModelInstance->links[0]->href, $person->links[0]->href);

		// toArray functions
		$this->assertEquals($this->testModelInstance->toArray(), $person->toArray());
		$this->assertEquals($this->testModelInstance->email->toArray(), $person->email->toArray());
		$this->assertEquals($this->testModelInstance->links[0]->toArray(), $person->links[0]->toArray());
	}

	/**
	 * Returns output of ::toXML method of tested parser.
	 * @depends testParse
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	protected function convertToXML(\DOMDocument $document)
	{
		return PersonParser::toXML($this->testModelInstance, $document);
	}

	/**
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
}
