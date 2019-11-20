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

        $this->assertTestFileExists(['app', 'layouts']);
        $this->assertTestFileExists(['app', 'views']);
        $this->assertTestFileExists(['app', 'layouts', 'default.php']);
        $this->assertTestFileExists(['app', 'views', 'home', 'home.php']);
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

    /** @test */
    public function it_creates_a_composer_with_htmlburger_library_required()
    {
        $this->runCommand('init');

        $composerPath = ['composer.json'];

        $this->assertTestFileExists($composerPath);

        $composerData = $this->getComposerData($composerPath);

        $this->assertArrayHasKey('require', $composerData);
        $this->assertArrayHasKey('htmlburger/wpemerge', $composerData['require']);
    }

    /** @test */
    public function it_has_autoload()
    {
        $this->runCommand('init');

        $composerPath = ['composer.json'];

        $this->assertTestFileExists($composerPath);
        $this->assertAutoload('App\\\\', $this->getComposerData($composerPath));
    }

    /** @test */
    public function it_can_initialize_with_namespace()
    {
        $this->runCommand('init', [
            '--namespace' => 'TestNamespace',
        ]);

        $composerPath = ['composer.json'];

        $this->assertTestFileExists($composerPath);
        $this->assertAutoload('TestNamespace\\\\', $this->getComposerData($composerPath));
    }

    /** @test */
    public function it_can_initialize_with_a_different_folder()
    {
        $this->runCommand('init', [
            '--dir' => 'testing-dir',
        ]);
        $this->assertTestFileExists(['testing-dir']);
    }

    /** @test */
    public function it_adds_namespace_to_controllers()
    {
        $this->runCommand('init', [
            '--namespace' => 'TestingerNamespace',
        ]);

        $controllersPath = [
            'app',
            'Controllers',
        ];

        $this->assertContentMatch(
            'TestingerNamespace\\Controllers\\Web',
            file_get_contents($this->getTestFilePath(array_merge($controllersPath, ['Web', 'HomeController.php'])))
        );
        $this->assertContentMatch(
            'TestingerNamespace\\Controllers\\Admin',
            file_get_contents($this->getTestFilePath(array_merge($controllersPath, ['Admin', 'AdminController.php'])))
        );
        $this->assertContentMatch(
            'TestingerNamespace\\Controllers\\Ajax',
            file_get_contents($this->getTestFilePath(array_merge($controllersPath, ['Ajax', 'AjaxController.php'])))
        );
    }

    protected function assertContentMatch(string $regex, string $content): void
    {
        $matched = !!preg_match('~' . preg_quote($regex) . '~', $content);

        if (!$matched) {
            $this->fail("Regex \"$regex\" didn't match content.");
            return;
        }

        $this->assertTrue(true);
    }

    protected function assertAutoload(string $namespace, array $composerData)
    {
        $this->assertArrayHasKey('autoload', $composerData);
        $this->assertArrayHasKey('psr-4', $composerData['autoload']);
        $this->assertArrayHasKey($namespace, $composerData['autoload']['psr-4']);
    }

    protected function getComposerData(array $path): array
    {
        return json_decode(
            file_get_contents($this->getTestFilePath($path)),
            true
        );
    }
}
