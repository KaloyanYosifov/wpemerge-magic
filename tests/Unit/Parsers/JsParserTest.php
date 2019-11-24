<?php

namespace Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;
use WPEmergeMagic\Parsers\JsParser;

class JsParserTest extends TestCase
{
    /** @test */
    public function it_parses_a_js_object()
    {
        $jsObject = <<<'EOD'
        {
            object1: {
                welcome: 'test'
            },
            array1: ['test', 'test2', { testing: ['test', 'test3'] }]
        }
        EOD;

        $data = (new JsParser())->parse($jsObject);

        $this->assertArrayHasKey('object1', $data);
        $this->assertArrayHasKey('welcome', $data['object1']);
        $this->assertSame('test', $data['object1']['welcome']);

        $this->assertArrayHasKey('array1', $data);
        $this->assertCount(3, $data['array1']);

        $arrayObjectText = ['test', 'test2'];

        foreach ($data['array1'] as $singleData) {
            if (!is_array($singleData)) {
                $this->assertTrue(in_array($singleData, $arrayObjectText));
                continue;
            }

            $this->assertArrayHasKey('testing', $singleData);
            $nesteArratDataText = ['test', 'test3'];

            foreach ($singleData['testing'] as $nestedArrayData) {
                $this->assertTrue(in_array($nestedArrayData, $nesteArratDataText));
            }
        }
    }
}
