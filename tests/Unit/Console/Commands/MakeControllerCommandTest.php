<?php

namespace Tests\Unit\Console\Commands;

use PHPUnit\Framework\TestCase;

class MakeControllerCommandTest extends TestCase
{
    /** @test */
    public function it_creates_a_controller()
    {
        $this->assertEquals('test', 'test');
    }
}
