<?php

namespace phpGPX\Tests\Unit\Parsers;

use phpGPX\Models\Email;
use phpGPX\Parsers\EmailParser;
use PHPUnit\Framework\TestCase;

class EmailParserTest extends TestCase
{
	protected Email $email;
	protected \SimpleXMLElement $file;

	private const FIXTURES_DIR = __DIR__ . '/../../Fixtures/Parsers/Email';

	protected function setUp(): void
	{
		$this->email = new Email();
		$this->email->id = 'jakub.dubec';
		$this->email->domain = 'gmail.com';

		$this->file = simplexml_load_file(self::FIXTURES_DIR . '/email.xml');
	}

	public function testParse(): void
	{
		$email = EmailParser::parse($this->file->email);

		$this->assertEquals($this->email, $email);
		$this->assertNotEmpty($email);

		$this->assertEquals($this->email->id, $email->id);
		$this->assertEquals($this->email->domain, $email->domain);

		$this->assertEquals($this->email->jsonSerialize(), $email->jsonSerialize());
	}

	public function testToXML(): void
	{
		$document = new \DOMDocument('1.0', 'UTF-8');

		$root = $document->createElement('document');
		$root->appendChild(EmailParser::toXML($this->email, $document));

		$document->appendChild($root);

		$this->assertXmlStringEqualsXmlString($this->file->asXML(), $document->saveXML());
	}

	public function testToJSON(): void
	{
		$this->assertJsonStringEqualsJsonFile(
			self::FIXTURES_DIR . '/email.json',
			json_encode($this->email->jsonSerialize()),
		);
	}
}
