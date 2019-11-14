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
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of the controller.');

        // options
        $this->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Type of the controller (web, admin, ajax).')
            ->addOption('silent', 's', InputOption::VALUE_NONE, 'Option if we should throw an error if controller already exists.')
            ->addOption('dir', 'd', InputOption::VALUE_REQUIRED, 'Set the name of the first directory name (default is app).')
            ->addOption('namespace', null, InputOption::VALUE_REQUIRED, 'Set the namespace the controller is going to use.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // assign input member to the interface we are recieving
        $this->input = $input;

        $name = $input->getArgument('name');
        $controllerFullPath = $this->getControllerPath() . $name . '.php';

        if (file_exists($controllerFullPath)) {
            // if we are on silent mode
            // return early
            if ($input->getOption('silent')) {
                return;
            }

            throw new RuntimeException('Controller with name "' . $name . '" already exist');

            return;
        }

        $type = $this->input->getOption('type') ?: 'web';
        // check if we have a home type of a controller
        // and use HomeController stub for it
        $controllerType = $type === 'home' ? 'HomeController' : 'Controller';
        $stubArguments = [
            'CONTROLLER_NAMESPACE' => $input->getOption('namespace') ?: 'App',
            'CONTROLLER_NAME' => $name,
            'CONTROLLER_TYPE' => $this->getControllerType($type),
        ];

        (new Filesystem())->dumpFile(
            $controllerFullPath,
            (new StubParser)->parseViaStub($controllerType, $stubArguments)
        );

        $output->writeln("Created a controller named $name");
    }

    protected function getControllerPath()
    {
        $type = $this->input->getOption('type') ?: 'web';
        $controllerType = $this->getControllerType($type);

        if (!$controllerType) {
            throw new RuntimeException('The "--type" option accepts three values (web, admin or ajax).');
        }

        $paths = [
            $this->input->getOption('dir') ?: 'app',
            'Controllers',
            $controllerType,
        ];

        return (new CreatePath)->create(Path::getCurrentWorkingDirectory(), $paths);
    }

    protected function getControllerTypes(): array
    {
        return [
            'web' => 'Web',
            'home' => 'Web',
            'admin' => 'Admin',
            'ajax' => 'Ajax',
        ];
    }

    /**
     * Get controller Type
     *
     * @param string $type
     * @return string|boolean
     */
    protected function getControllerType(string $type)
    {
        if (!array_key_exists($type, $this->getControllerTypes())) {
            return false;
        }

        return $this->getControllerTypes()[$type];
    }
}
