<?php

namespace Tests\Unit\Tasks\Vue;

use Tests\TestCase;
use Symfony\Component\Console\Command\Command;
use WPEmergeMagic\Tasks\Vue\InitializeWebpackTask;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitializeWebpackTaskTest extends TestCase
{
    /** @test */
    public function it_creates_a_webpack_file_if_it_doesnt_find_it()
    {
        $webpackFilePath = [
            'config',
            'webpack.config.js',
        ];

        $inputMock = $this->mock(InputInterface::class);
        $outputMock = $this->mock(OutputInterface::class);
        $commandMock = $this->mock(Command::class);

        $outputMock->shouldReceive('writeln');

        $this->assertTestDoesntFileExists($webpackFilePath);

        $initWebpackTask = new InitializeWebpackTask();
        $initWebpackTask->handle($inputMock, $outputMock, $commandMock);

        $this->assertTestFileExists($webpackFilePath);
    }

    /** @test */
    public function it_adds_vue_loader_to_webpack() {}
}
