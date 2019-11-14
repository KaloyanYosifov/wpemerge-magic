<?php

namespace Tests\Unit\Console\Commands;

use Tests\CommandTestCase;

class MakeControllerCommandTest extends CommandTestCase
{
    /** @test */
    public function it_creates_a_controller()
    {
        $this->tryToTestACommand('make:controller', [
            'name' => 'TestController',
        ]);
    }
}
