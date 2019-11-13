<?php

namespace Tests\Units;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Tester\CommandTester;

class MakeControllerCommandTest extends TestCase
{
    protected $application = null;

    public function setUp() {}

    protected function testCommand(string $commandName, array $arguments): Output
    {
        $command = $this->application->find($commandName);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array_merge(
            ['command' => $command->getName()],
            $arguments
        ));

        return $commandTester->getDisplay();
    }
}
