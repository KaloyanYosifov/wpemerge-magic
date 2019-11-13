<?php

namespace WPEmergeMagic\Console\Commands;

use WPEmergeMagic\Parsers\StubParser;
use WPEmergeMagic\Support\CreatePath;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeTest extends Command
{
    protected static $defaultName = 'make:test';

    protected function configure()
    {
        $this->setDescription('Create a test.')
            ->setHelp('Creates a unit test in your tests folder.');

        // arguments
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of the controller.');

        // options
        $this->addOption('namespace', null, InputOption::VALUE_REQUIRED, 'Define namespace for the controller.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $testsPath = (new CreatePath())
            ->create(getcwd(), $this->getPathToTests($input, $output), false);

        (new Filesystem())->dumpFile(
            $testsPath,
            (new StubParser)->parseViaStub('UnitTest', [
                'TEST_NAME' => $input->getArgument('name'),
                'NAMESPACE' => $input->getOption('namespace') ?: '',
            ])
        );
    }

    protected function getPathToTests(InputInterface $input, OutputInterface $output): array
    {
        $path = [
            'tests',
            'unit',
        ];
        $namespace = $input->getOption('namespace');

        if (!$namespace) {
            return $path;
        }

        if (strpos($namespace, '\\') === false) {
            return array_merge($path, [$namespace]);
        }

        return array_merge($path, explode('\\', $namespace));
    }
}
