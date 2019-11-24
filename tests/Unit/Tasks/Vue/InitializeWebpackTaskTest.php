<?php

namespace Tests\Unit\Tasks\Vue;

use Tests\TestCase;
use WPEmergeMagic\Parsers\StubParser;
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
    public function it_adds_vue_loader_to_webpack()
    {
        $webpackFilePath = [
            'config',
            'webpack.config.js',
        ];

        // generate a webpack config js file prematurely
        // so that the task doesn't create the default one
        $this->putContentsToTestFile(
            $webpackFilePath,
            (new StubParser)->parseViaStub('webpack-not-full.config.js')
        );

        $inputMock = $this->mock(InputInterface::class);
        $outputMock = $this->mock(OutputInterface::class);
        $commandMock = $this->mock(Command::class);

        $outputMock->shouldReceive('writeln');

        $initWebpackTask = new InitializeWebpackTask();
        $initWebpackTask->handle($inputMock, $outputMock, $commandMock);

        $contents = $this->getTestFilePathContents($webpackFilePath);

        $this->assertRegExp('~test: \/\\\.vue\$\/,~', $contents);
        $this->assertRegExp('~loader: \'vue-loader\'~', $contents);
        $this->assertRegExp('~new VueLoaderPlugin\(\)~', $contents);
        $this->assertRegExp('~\'vue$\': \'vue/dist/vue.runtime.esm.js\'~', $contents);
        $this->assertNotRegExp('~new VueLoaderPlugin\(\)\':~', $contents);
    }
}
