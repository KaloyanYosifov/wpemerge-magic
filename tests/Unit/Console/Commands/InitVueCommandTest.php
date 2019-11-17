<?php

namespace Tests\Unit\Console\Commands;

use Tests\CommandTestCase;

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
}
