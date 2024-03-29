<?php

namespace WPEmergeMagic\Console\Commands;

use WPEmergeMagic\Support\Path;
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
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of the test.');

        // options
        $this->addOption('namespace', null, InputOption::VALUE_REQUIRED, 'Define namespace for the test.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $testsPath = (new CreatePath())
            ->create(Path::getCurrentWorkingDirectory(), $this->getPathToTests($input, $output), false);

        (new Filesystem())->dumpFile(
            $testsPath,
            (new StubParser)->parseViaStub('UnitTest', [
                'TEST_NAME' => $this->getNameWithoutExtension($input->getArgument('name')),
                'NAMESPACE' => $this->convertNamespace($input->getOption('namespace')),
            ])
        );
    }

    protected function getPathToTests(InputInterface $input, OutputInterface $output): array
    {
        $path = [
            'tests',
            'Unit',
        ];
        $namespace = $input->getOption('namespace');
        $name = $this->getNameWithExtension($input->getArgument('name'));

        if (!$namespace) {
            return array_merge($path, [$name]);
        }

        $namespacePath = [];

        if (strpos($namespace, '/') === false) {
            $path[] = $namespace;
        } else {
            $path = array_merge($path, explode('/', $namespace));
        }

        return array_merge($path, [$name]);
    }

    protected function getNameWithExtension(string $name): string
    {
        if (!preg_match('~\.php$~', $name)) {
            $name = $name . '.php';
        }

        return $name;
    }

    protected function getNameWithoutExtension(string $name): string
    {
        if (preg_match('~\.php$~', $name)) {
            $name = explode('.', $name)[0];
        }

        return $name;
    }

    protected function convertNamespace(?string $namespace): string
    {
        if (!$namespace) {
            return '';
        }

        return '\\' . str_replace('/', '\\', $namespace);
    }
}
