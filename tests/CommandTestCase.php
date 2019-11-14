<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use WPEmergeMagic\Console\Console;
use Symfony\Component\Console\Tester\CommandTester;

class CommandTestCase extends TestCase
{
    protected function tryToTestACommand(string $commandName, array $arguments = []): string
    {
        $command = Console::getApplication()->find($commandName);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array_merge(
            ['command' => $command->getName()],
            $arguments
        ));

        return $commandTester->getDisplay();
    }
}
