<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use WPEmergeMagic\Console\Console;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Tester\CommandTester;

class CommandTestCase extends TestCase
{
    protected function tryToTestACommand(string $commandName, array $arguments): Output
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
