<?php

namespace Tests\Unit\Console\Commands;

use Tests\CommandTestCase;

class MakeControllerCommandTest extends CommandTestCase
{
    /** @test */
    public function it_can_create_three_types_of_controllers()
    {
        $this->assertControllerIsCreated('TestController');
        $this->assertControllerIsCreated('TestController', 'admin');
        $this->assertControllerIsCreated('TestController', 'ajax');
    }

    protected function assertControllerIsCreated(string $name, string $type = 'web')
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
