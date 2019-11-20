<?php

namespace Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;

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

        $data = (new JsParser())->handle();
    }
}
