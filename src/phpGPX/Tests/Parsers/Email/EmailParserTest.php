<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Tests\Parsers\Email;

use phpGPX\Models\Email;
use phpGPX\Parsers\EmailParser;
use PHPUnit\Framework\TestCase;

class EmailParserTest extends TestCase
{
	protected Email $email;
    protected \SimpleXMLElement $file;

	protected function setUp(): void
    {
		$this->email = new Email();
        $this->email->id = "jakub.dubec";
        $this->email->domain = "gmail.com";

        $this->file = simplexml_load_file(sprintf("%s/email.xml", __DIR__));
	}

    /**
     * @covers \phpGPX\Parsers\EmailParser
     * @covers \phpGPX\Models\Email
     * @return void
     */
    public function testParse()
	{
		$email = EmailParser::parse($this->file->email);

        $this->assertEquals($this->email, $email);
		$this->assertNotEmpty($email);

		$this->assertEquals($this->email->id, $email->id);
		$this->assertEquals($this->email->domain, $email->domain);

		$this->assertEquals($this->email->toArray(), $email->toArray());
	}


    /**
     * @covers \phpGPX\Parsers\EmailParser
     * @covers \phpGPX\Models\Email
     * @return void
     * @throws \DOMException
     */
    public function testToXML()
    {
        $document = new \DOMDocument("1.0", 'UTF-8');

        $root = $document->createElement("document");
        $root->appendChild(EmailParser::toXML($this->email, $document));

        $document->appendChild($root);

        $this->assertXmlStringEqualsXmlString($this->file->asXML(), $document->saveXML());
    }

    /**
     * @covers \phpGPX\Models\Email
     * @return void
     */
    public function testToJSON()
    {
        $this->assertJsonStringEqualsJsonFile(
            sprintf("%s/email.json", __DIR__), json_encode($this->email->toArray())
        );
    }
}
