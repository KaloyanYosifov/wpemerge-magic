<?php

namespace Tests\Unit\Console\Commands;

use Tests\CommandTestCase;
use Symfony\Component\Filesystem\Filesystem;

class InitVueCommandTest extends CommandTestCase
{
    /** @test */
    public function it_has_a_package_json_file()
    {
        $this->runCommand('init-vue');

        $this->assertTestFileExists([
            'package.json',
        ]);
    }

    /** @test */
    public function it_has_vue_in_dependencies()
    {
        $this->runCommand('init-vue');

        $packageJsonContents = json_decode($this->getTestFilePathContents([
            'package.json',
        ]), true);

        $this->assertArrayHasKey('vue', $packageJsonContents['dependencies']);
    }

    /** @test */
    public function it_has_dev_dependencies()
    {
        $this->runCommand('init-vue');

        $packageJsonContents = json_decode($this->getTestFilePathContents([
            'package.json',
        ]), true);
        $devDependencies = $packageJsonContents['devDependencies'];

        $this->assertArrayHasKey('webpack', $devDependencies);
        $this->assertArrayHasKey('webpack-cli', $devDependencies);
        $this->assertArrayHasKey('vue-loader', $devDependencies);
    }

    /** @test */
    public function it_installs_only_dev_dependencies_that_do_not_exist()
    {
        $packageJsonStructure = [
            'devDependencies' => [
                'webpack-cli' => '*',
            ],
        ];

        (new Filesystem())->dumpFile($this->getTestFilePath(['package.json']), json_encode($packageJsonStructure, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $packageJsonContents = json_decode($this->getTestFilePathContents([
            'package.json',
        ]), true);
        $devDependencies = $packageJsonContents['devDependencies'];

        $this->assertArrayHasKey('webpack-cli', $devDependencies);
        $this->assertArrayNotHasKey('webpack', $devDependencies);
        $this->assertArrayNotHasKey('vue-loader', $devDependencies);

        $this->runCommand('init-vue');

        $packageJsonContents = json_decode($this->getTestFilePathContents([
            'package.json',
        ]), true);
        $devDependencies = $packageJsonContents['devDependencies'];

        $this->assertArrayHasKey('webpack', $devDependencies);
        $this->assertArrayHasKey('webpack-cli', $devDependencies);
        $this->assertArrayHasKey('vue-loader', $devDependencies);
    }
}
