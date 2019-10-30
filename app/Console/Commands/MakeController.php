<?php

namespace WPEmergeMagic\Console\Commands;

use WPEmergeMagic\Parsers\StubParser;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeController extends Command
{
    protected static $defaultName = 'make:controller';

    protected function configure()
    {
        $this->setDescription('Create a controller.')
            ->setHelp('Creates a controller in your root path.');

        // arguments
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of the controller');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $controllerFullPath = $this->getControllerPath() . $name . '.php';

        $stubArguments = [
            'CONTROLLER_NAME' => $name,
        ];

        (new Filesystem)->dumpFile(
            $controllerFullPath,
            (new StubParser)->parseViaStub('Controller', $stubArguments)
        );

        $output->writeln("Create a controller named $name");
    }

    protected function getControllerPath()
    {
        return $_SERVER['PWD'] . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Web' . DIRECTORY_SEPARATOR;
    }
}
