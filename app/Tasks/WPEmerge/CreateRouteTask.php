<?php

namespace WPEmergeMagic\Tasks\WPEmerge;

use WPEmergeMagic\Parsers\StubParser;
use WPEmergeMagic\Support\CreatePath;
use WPEmergeMagic\Constants\AppConstants;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateRouteTask
{
    public function handle(InputInterface $input, OutputInterface $output, Command $command)
    {
        $output->writeln('Creating routes...');

        $fileSystem = new Filesystem;

        $routes = [
            'HomeController' => 'web.php',
            'AdminController' => 'admin.php',
            'AjaxController' => 'ajax.php',
        ];

        foreach ($routes as $controllerName => $route) {
            $routeFullPath = (new CreatePath)->create(getcwd(), [
                $input->getOption('dir') ?: AppConstants::DEFAULT_APP_DIRECTORY,
                'routes',
                $route,
            ], false);

            // if we already have a route named like that skip it
            if (\file_exists($routeFullPath)) {
                continue;
            }

            $fileSystem->dumpFile(
                $routeFullPath,
                (new StubParser)->parseViaStub('route', [
                    'CONTROLLER_NAME' => $controllerName,
                ])
            );
        }
    }
}
