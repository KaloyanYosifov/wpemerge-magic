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

    /** @test */
    public function it_can_create_controller_in_another_folder_convention()
    {
        $this->runCommand('make:controller', [
            'name' => 'TestController',
            '--dir' => 'new-test-app',
        ]);

        $this->assertTestFileExists([
            'new-test-app',
            'Controllers',
            'Web',
            'TestController.php',
        ]);
    }

    /** @test */
    public function it_can_create_a_controller_with_a_different_namespace()
    {
        $this->runCommand('make:controller', [
            'name' => 'TestController',
            '--namespace' => 'RandomNamespaceFound',
        ]);

        $pathToController = [
            'app',
            'Controllers',
            'Web',
            'TestController.php',
        ];

        $this->assertTestFileExists($pathToController);

        $contentsOfController = file_get_contents($this->getTestFilePath($pathToController));

        $this->assertTrue(!!preg_match('~RandomNamespaceFound~', $contentsOfController));
    }

    /** @test */
    public function it_can_create_controller_with_all_the_options()
    {
        $this->runCommand('make:controller', [
            'name' => 'TestController',
            '--type' => 'admin',
            '--dir' => 'new-test-app',
            '--namespace' => 'RandomNamespaceFound',
        ]);

        $pathToController = [
            'new-test-app',
            'Controllers',
            'Admin',
            'TestController.php',
        ];

        $this->assertTestFileExists($pathToController);

        $contentsOfController = file_get_contents($this->getTestFilePath($pathToController));

        $this->assertTrue(!!preg_match('~RandomNamespaceFound~', $contentsOfController));
    }

    protected function assertControllerIsCreated(string $name, string $type = 'web')
    {
        $this->runCommand('make:controller', [
            'name' => $name,
            '--type' => $type,
        ]);

        $this->assertTestFileExists([
            'app',
            'Controllers',
            ucfirst($type),
            $name . '.php',
        ]);
    }
}
