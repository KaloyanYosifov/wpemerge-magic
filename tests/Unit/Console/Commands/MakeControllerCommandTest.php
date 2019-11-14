<?php

namespace Tests\Unit\Console\Commands;

use Tests\CommandTestCase;

class MakeControllerCommandTest extends CommandTestCase
{
    /** @test */
    public function it_can_create_three_types_of_controllers()
    {
        $this->testCreatingAController('TestController');
        $this->testCreatingAController('TestController', 'admin');
        $this->testCreatingAController('TestController', 'ajax');
    }

    protected function testCreatingAController(string $name, string $type = 'web')
    {
        $this->runCommand('make:controller', [
            'name' => $name,
            '--type' => $type,
        ]);

        $this->assertAppFileExists([
            'app',
            'Controllers',
            ucfirst($type),
            $name . '.php',
        ]);
    }
}
