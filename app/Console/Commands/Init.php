<?php

namespace WPEmergeMagic\Console\Commands;

use WPEmergeMagic\Parsers\StubParser;
use WPEmergeMagic\Support\CreatePath;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Init extends Command
{
    protected static $defaultName = 'init';

    protected function configure()
    {
        $this->setDescription('Init WPEmerge project.')
            ->setHelp('Initialize WPEmerge project.');

        // options
        $this->addOption('dirName', 'd', InputOption::VALUE_REQUIRED, 'Set the name of the first directory name (default is app)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileSystem = new Filesystem;

        $routes = [
            'web.php',
            'admin.php',
            'ajax.php',
        ];

        foreach ($routes as $route) {
            $routePath = [
                $input->getOption('dirName') ?: 'app',
                'routes',
                $route,
            ];

            $fileSystem->dumpFile(
                (new CreatePath)->create($_SERVER['PWD'], $routePath, false),
                (new StubParser)->parseViaStub('route')
            );
        }
    }

    protected function getExecutionDirectory()
    {
        return $_SERVER['PWD'] . DIRECTORY_SEPARATOR;
    }
}
