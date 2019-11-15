<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use WPEmergeMagic\Console\Console;
use WPEmergeMagic\Support\CreatePath;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Tester\CommandTester;

class CommandTestCase extends TestCase
{
    protected function runCommand(string $commandName, array $arguments = []): string
    {
        $command = Console::getApplication()->find($commandName);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array_merge(
            ['command' => $command->getName()],
            $arguments
        ));

        return $commandTester->getDisplay();
    }

    protected function assertTestFileExists(array $path)
    {
        $this->assertTrue(file_exists($this->getTestFilePath($path)));
    }

    protected function getTestFilePath(array $path)
    {
        return (new CreatePath())
            ->create(
                ROOT_TEST_DIR,
                array_merge(['test-files'], $path),
                false
            );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        (new Filesystem())
            ->remove(
                (new CreatePath())->create(ROOT_TEST_DIR, [
                    'test-files',
                ], false)
            );
    }
}
