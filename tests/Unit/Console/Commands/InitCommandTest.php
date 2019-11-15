<?php

namespace Tests\Unit\Console\Commands;

use Tests\CommandTestCase;

class InitCommandTest extends CommandTestCase
{
    /** @test */
    public function it_creates_an_app_folder()
    {
        $this->runCommand('init');

        $this->assertTestFileExists(['app']);
    }

    /** @test */
    public function it_creates_routes()
    {
        $this->runCommand('init');

        $routesPath = [
            'app',
            'routes',
        ];

        $this->assertTestFileExists($routesPath);
        $this->assertTestFileExists(array_merge($routesPath, ['web.php']));
        $this->assertTestFileExists(array_merge($routesPath, ['admin.php']));
        $this->assertTestFileExists(array_merge($routesPath, ['ajax.php']));
    }

    /** @test */
    public function it_creates_controllers()
    {
        $this->runCommand('init');

        $controllersPath = [
            'app',
            'Controllers',
        ];

        $this->assertTestFileExists($controllersPath);
        $this->assertTestFileExists(array_merge($controllersPath, ['Web', 'HomeController.php']));
        $this->assertTestFileExists(array_merge($controllersPath, ['Admin', 'AdminController.php']));
        $this->assertTestFileExists(array_merge($controllersPath, ['Ajax', 'AjaxController.php']));
    }

    /** @test */
    public function it_creates_views_and_layouts()
    {
        $this->runCommand('init');

        $viewsPath = [
            'app',
            'views',
        ];

        $this->assertTestFileExists($viewsPath);
        $this->assertTestFileExists(array_merge($viewsPath, ['layouts', 'default.php']));
        $this->assertTestFileExists(array_merge($viewsPath, ['home', 'home.php']));
    }

    /** @test */
    public function it_creates_a_bootstrap_file()
    {
        $this->runCommand('init');

        $this->assertTestFileExists([
            'app',
            'bootstrap.php',
        ]);
    }
}
