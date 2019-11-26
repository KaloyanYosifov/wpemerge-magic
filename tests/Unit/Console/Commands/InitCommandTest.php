<?php

namespace Tests\Unit\Console\Commands;

use Tests\CommandTestCase;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class InitCommandTest extends CommandTestCase
{
    /** @test */
    public function it_creates_an_app_folder()
    {
        // create functions.php
        //every time
        file_put_contents($this->getTestFilePath(['functions.php']), '<?php');

        $this->runCommand('init');
        $this->assertTestFileExists(['app']);
    }

    /** @test */
    public function it_creates_routes()
    {
        // create functions.php
        //every time
        file_put_contents($this->getTestFilePath(['functions.php']), '<?php');

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
        // create functions.php
        //every time
        file_put_contents($this->getTestFilePath(['functions.php']), '<?php');

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
        // create functions.php
        //every time
        file_put_contents($this->getTestFilePath(['functions.php']), '<?php');

        $this->runCommand('init');

        $viewsPath = [
            'app',
            'views',
        ];

        $this->assertTestFileExists(array_merge($viewsPath));
        $this->assertTestFileExists(array_merge($viewsPath, ['layouts']));
        $this->assertTestFileExists(array_merge($viewsPath, ['layouts', 'default.php']));
        $this->assertTestFileExists(array_merge($viewsPath, ['home', 'home.php']));
    }

    /** @test */
    public function it_creates_a_bootstrap_file()
    {
        // create functions.php
        //every time
        file_put_contents($this->getTestFilePath(['functions.php']), '<?php');

        $this->runCommand('init');

        $this->assertTestFileExists([
            'app',
            'bootstrap.php',
        ]);
    }

    /** @test */
    public function it_creates_a_composer_with_htmlburger_library_required()
    {
        // create functions.php
        //every time
        file_put_contents($this->getTestFilePath(['functions.php']), '<?php');

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
        // create functions.php
        //every time
        file_put_contents($this->getTestFilePath(['functions.php']), '<?php');

        $this->runCommand('init');

        $composerPath = ['composer.json'];

        $this->assertTestFileExists($composerPath);
        $this->assertAutoload('App\\', $this->getComposerData($composerPath));
    }

    /** @test */
    public function it_can_initialize_with_namespace()
    {
        // create functions.php
        //every time
        file_put_contents($this->getTestFilePath(['functions.php']), '<?php');

        $this->runCommand('init', [
            '--namespace' => 'TestNamespace',
        ]);

        $composerPath = ['composer.json'];

        $this->assertTestFileExists($composerPath);
        $this->assertAutoload('TestNamespace\\', $this->getComposerData($composerPath));
    }

    /** @test */
    public function it_can_initialize_with_a_different_folder()
    {
        // create functions.php
        //every time
        file_put_contents($this->getTestFilePath(['functions.php']), '<?php');

        $this->runCommand('init', [
            '--dir' => 'testing-dir',
        ]);
        $this->assertTestFileExists(['testing-dir']);
    }

    /** @test */
    public function it_adds_namespace_to_controllers()
    {
        // create functions.php
        //every time
        file_put_contents($this->getTestFilePath(['functions.php']), '<?php');

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

    /** @test */
    public function it_throws_an_error_if_it_cannot_find_functions_php()
    {
        try {
            $this->runCommand('init');
            $this->fail('An exception should have been thrown.');
        } catch (FileNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /** @test */
    public function it_requires_bootstrap_file_in_functions_php()
    {
        // create functions.php
        //every time
        file_put_contents($this->getTestFilePath(['functions.php']), '<?php');

        $requireBootstrapFileString = 'require_once __DIR__ \. DIRECTORY_SEPARATOR \. \'app\' \. DIRECTORY_SEPARATOR \. \'bootstrap.php\';';

        $this->assertNotRegExp('~' . $requireBootstrapFileString . '~', $this->getTestFilePathContents(['functions.php']));
        $this->runCommand('init');
        $this->assertRegExp('~' . $requireBootstrapFileString . '~', $this->getTestFilePathContents(['functions.php']));
    }

    /** @test */
    public function it_requires_bootstrap_file_in_functions_php_with_different_directory()
    {
        // create functions.php
        //every time
        file_put_contents($this->getTestFilePath(['functions.php']), '<?php');

        $requireBootstrapFileString = 'require_once __DIR__ \. DIRECTORY_SEPARATOR \. \'something\' \. DIRECTORY_SEPARATOR \. \'bootstrap.php\';';

        $this->assertNotRegExp('~' . $requireBootstrapFileString . '~', $this->getTestFilePathContents(['functions.php']));
        $this->runCommand('init', [
            '--dir' => 'something',
        ]);
        $this->assertRegExp('~' . $requireBootstrapFileString . '~', $this->getTestFilePathContents(['functions.php']));
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
