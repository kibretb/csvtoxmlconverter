<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProducTest extends TestCase
{
    /**
     * Test that the CSV to XML conversion endpoint correctly generates and
     * serves an XML file for download with the expected content and filename
     */
    public function test_it_can_convert_and_download_xml_correctly(): void
    {
        $response = $this->get('/csv-to-xml-convertor');

        $expectedXml = file_get_contents(base_path('tests/ExpectedXml/expected-output.xml'));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/xml')
            ->assertHeader('Content-Disposition', 'attachment; filename="products.xml"');

        $this->assertXmlStringEqualsXmlString($expectedXml, $response->getContent());
    }
}
