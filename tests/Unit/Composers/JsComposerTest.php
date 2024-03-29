<?php

namespace Tests\Unit\Composers;

use PHPUnit\Framework\TestCase;
use WPEmergeMagic\Composers\JsComposer;

class JsComposerTest extends TestCase
{
    /** @test */
    public function it_can_convert_json_data_to_js_object()
    {
        $jsComposer = new JsComposer();
        $jsObject = [
            'welcome' => [
                'testing' => 'how i am i',
            ],
            'welcome2' => [
                'greeting' => 'greets',
            ],
        ];
        $assertString = <<<EOD
        {
            welcome: {
                testing: 'how i am i'
            },
            welcome2: {
                greeting: 'greets'
            }
        }
        EOD;

        $this->assertEquals($jsComposer->compose($jsObject), $assertString);
    }

    /** @test */
    public function it_can_compose_js_functions_strings()
    {
        $jsComposer = new JsComposer();
        $jsObject = [
            'welcome' => [
                'testing' => 'path.rename(\'testing issue\', \'welcome to the hood\')',
                'testing2' => 'path.rename(\'testing issue\', \'welcome to my hood\')',
            ],
            'welcome2' => [
                'greeting' => 'greets',
            ],
        ];
        $assertString = <<<EOD
        {
            welcome: {
                testing: path.rename('testing issue', 'welcome to the hood'),
                testing2: path.rename('testing issue', 'welcome to my hood'),
            },
            welcome2: {
                greeting: 'greets'
            }
        }
        EOD;

        $this->assertEquals($jsComposer->compose($jsObject), $assertString);
    }

    /** @test */
    public function it_can_compose_regex()
    {
        $jsComposer = new JsComposer();
        $jsObject = [
            'welcome' => [
                'testing' => 'path.rename(\'testing issue\', \'welcome to the hood\')',
                'testing2' => 'path.rename(\'testing issue\', \'welcome to my hood\')',
            ],
            'welcome2' => [
                'greeting' => '/test/',
                'greeting3' => '/test2/ig',
            ],
        ];
        $assertString = <<<EOD
        {
            welcome: {
                testing: path.rename('testing issue', 'welcome to the hood'),
                testing2: path.rename('testing issue', 'welcome to my hood'),
            },
            welcome2: {
                greeting: /test/,
                greeting3: /test2/ig
            }
        }
        EOD;

        $this->assertEquals($jsComposer->compose($jsObject), $assertString);
    }

    /** @test */
    public function it_can_compose_from_classes()
    {
        $jsComposer = new JsComposer();
        $jsObject = [
            'welcome' => [
                'testing' => 'path.rename(\'testing issue\', \'welcome to the hood\')',
                'testing2' => 'path.rename(\'testing issue\', \'welcome to my hood\')',
            ],
            'welcome2' => [
                'new Greet()',
            ],
        ];
        $assertString = <<<EOD
        {
            welcome: {
                testing: path.rename('testing issue', 'welcome to the hood'),
                testing2: path.rename('testing issue', 'welcome to my hood'),
            },
            welcome2: [
                new Greet()
            ]
        }
        EOD;

        $this->assertEquals($jsComposer->compose($jsObject), $assertString);
    }

    /** @test */
    public function it_doesnt_remove_quotes_from_special_object_keys()
    {
        $jsComposer = new JsComposer();
        $jsObject = [
            'welcome' => [
                'test-and-test' => 'path.rename(\'testing issue\', \'welcome to the hood\')',
                'test$' => 'path.rename(\'testing issue\', \'welcome to my hood\')',
            ],
        ];
        $assertString = <<<EOD
        {
            welcome: {
                'test-and-test': path.rename('testing issue', 'welcome to the hood'),
                'test$': path.rename('testing issue', 'welcome to my hood'),
            }
        }
        EOD;

        $this->assertEquals($jsComposer->compose($jsObject), $assertString);
    }
}
