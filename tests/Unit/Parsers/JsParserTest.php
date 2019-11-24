<?php

namespace Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;
use WPEmergeMagic\Parsers\JsParser;

class JsParserTest extends TestCase
{
    /** @test */
    public function it_parses_a_js_object()
    {
        $jsObject = <<<EOD
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

    /** @test */
    public function it_parses_js_object_with_functions()
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

        $this->assertArrayHasKey('object1', $data);
        $this->assertArrayHasKey('welcome', $data['object1']);

        $this->assertSame("classObject.parsingText('testing')", $data['object1']['welcome']);
        $this->assertSame("classObject.parsingText('testing', 'test123', 'testing the one two three')", $data['object1']['complex']);
        $this->assertSame("classObject.parsingText('testing', ['array2', 'arra3', ' asd' ])", $data['object1']['complex2']);
        $this->assertSame("classObject.parsingText('testing', __dirname('testing_sunshine'))", $data['object1']['complex3']);
    }

    /** @test */
    public function it_removes_trailing_comma_in_objects_so_the_json_can_parse()
    {
        $jsObject = <<<EOD
        {
            object1: {
                welcome: 'test',
                welcome: 'test2',
            },
            array1: ['test', 'test2', { testing: ['test', 'test3'] }]
        }
        EOD;

        $data = (new JsParser())->parse($jsObject);

        $this->assertNotNull($data);
        $this->assertArrayHasKey('object1', $data);
        $this->assertArrayHasKey('welcome', $data['object1']);
    }

    /** @test */
    public function it_replaces_double_quotes_with_single_quotes_so_we_can_parse()
    {
        $jsObject = <<<EOD
        {
            object1: {
                "welcome": 'test'
            },
        }
        EOD;

        $data = (new JsParser())->parse($jsObject);

        $this->assertNotNull($data);
        $this->assertArrayHasKey('object1', $data);
        $this->assertArrayHasKey('welcome', $data['object1']);
    }

    /** @test */
    public function it_adds_double_quotes_to_new_class_objects()
    {
        $jsObject = <<<EOD
        {
            object1: [
                new Object(),
                classObject.parsingText('testing'),
                new Object2(),
            ],
        }
        EOD;

        $data = (new JsParser())->parse($jsObject);

        $this->assertNotNull($data);
        $this->assertArrayHasKey('object1', $data);
        $this->assertEquals('new Object()', $data['object1'][0]);
        $this->assertEquals('classObject.parsingText(\'testing\')', $data['object1'][1]);
        $this->assertEquals('new Object2()', $data['object1'][2]);
    }

    /** @test */
    public function it_can_parse_real_js_object()
    {
        $jsObject = <<<EOD
        {
            entry: path.resolve(__dirname, '../', 'resources', 'js', 'index.js'),
            module: {
                rules: [
                    {
                        test: /\.vue$/,
                        use: 'vue-loader'
                    },
                ]
            },
            resolve: {
                alias: {
                    'vue$': 'vue/dist/vue.runtime.esm.js'
                },
                plugins: [
                    new VueLoaderPlugin()
                ]
            },
            output: {
                path: path.resolve(__dirname, '../', 'dist'),
                filename: 'bundle.js'
            }
        }
        EOD;

        $data = (new JsParser())->parse($jsObject);

        $this->assertNotNull($data);
    }
}
