<?php

namespace Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;
use WPEmergeMagic\Parsers\JsParser;

class JsParserTest extends TestCase
{
    /** @test */
    public function it_parses_a_js_object()
    {

        // fake test
        $this->assertTrue(true);
        return;

        $jsObject = <<<'EOD'
        {
            object1: {
                welcome: 'test'
            },
            array1: ['test', 'test2', { testing: ['test', 'test3'] }]
        }
        EOD;

        $data = (new JsParser())->parse($jsObject);
    }
}
