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
use Symfony\Component\Console\Exception\RuntimeException;

class MakeController extends Command
{
    protected static $defaultName = 'make:controller';

    protected $input = null;

    protected function configure()
    {
        $this->setDescription('Create a controller.')
            ->setHelp('Creates a controller in your root path.');

        // arguments
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of the controller');

        // options
        $this->addOption('type', null, InputOption::VALUE_REQUIRED, 'Type of the controller (web, admin, ajax)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // assign input member to the interface we are recieving
        $this->input = $input;

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
        $controllerTypes = [
            'web' => 'Web',
            'admin' => 'Admin',
            'ajax' => 'Ajax',
        ];

        $type = $this->input->getOption('type') ?: 'web';

        if (!array_key_exists($type, $controllerTypes)) {
            throw new RuntimeException('The "--type" option accepts three values (web, admin or ajax).');
        }

        $paths = [
            'app',
            'Controllers',
            $controllerTypes[$type],
        ];

        return (new CreatePath)->create(getcwd(), $paths);
    }
}
