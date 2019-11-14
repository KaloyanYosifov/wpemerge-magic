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

        $this->assertAppFileExists([
            'app',
            'Controllers',
            'Web',
            'TestController.php',
        ]);
    }

    /** @test */
    public function it_creates_an_admin_controller()
    {
        $this->tryToTestACommand('make:controller', [
            'name' => 'TestController',
            '--type' => 'admin',
        ]);

        $this->assertAppFileExists([
            'app',
            'Controllers',
            'Admin',
            'TestController.php',
        ]);
    }
}
