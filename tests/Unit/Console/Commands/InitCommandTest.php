<?php

namespace Tests\Unit\Console\Commands;

use Tests\CommandTestCase;

class InitCommandTest extends CommandTestCase
{
    /** @test */
    public function it_creates_an_app_folder()
    {
        $this->runCommand('init');

        $this->assertTrue(
            file_exists($this->getTestFilePath(['app']))
        );
    }
}
