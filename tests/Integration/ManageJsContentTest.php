<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use WPEmergeMagic\Parsers\JsParser;
use WPEmergeMagic\Composers\JsComposer;

class ManageJsContentTest extends TestCase
{
    /** @test */
    public function a_js_string_object_can_be_parsed_and_modified()
    {
        $jsObject = <<<EOD
        {
            object1: {
                welcome: classObject.parsingText('testing'),
                complex: classObject.parsingText('testing', 'test123', 'testing the one two three'),
                complex2: classObject.parsingText('testing', ['array2', 'arra3', ' asd' ]),
                complex3: classObject.parsingText('testing', __dirname('testing_sunshine')),
            },
        }
        EOD;
        $data = (new JsParser())->parse($jsObject);

        $data['object2'] = [
            'testing' => 'new_implementation',
            'testing2' => 'it works',
        ];
        $assertString = <<<EOD
        {
            object1: {
                welcome: classObject.parsingText('testing'),
                complex: classObject.parsingText('testing', 'test123', 'testing the one two three'),
                complex2: classObject.parsingText('testing', ['array2', 'arra3', ' asd' ]),
                complex3: classObject.parsingText('testing', __dirname('testing_sunshine')),
            },
            object2: {
                testing: 'new_implementation',
                testing2: 'it works'
            }
        }
        EOD;

        $this->assertEquals((new JsComposer)->compose($data), $assertString);
    }
}
