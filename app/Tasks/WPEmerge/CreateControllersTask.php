<?php

namespace WPEmergeMagic\Tasks\WPEmerge;

use WPEmergeMagic\Constants\AppConstants;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateControllersTask
{
    public function handle(InputInterface $input, OutputInterface $output, Command $command)
    {
        $output->writeln('Creating controllers...');

        $controllerNames = [
            'home' => 'HomeController',
            'admin' => 'AdminController',
            'ajax' => 'AjaxController',
        ];
        $makeControllerCommand = $command->getApplication()->find('make:controller');

        foreach ($controllerNames as $controllerType => $controllerName) {
            $arguments = new ArrayInput([
                'name' => $controllerName,
                '--type' => $controllerType,
                '--silent' => true,
                '--dir' => $input->getOption('dir') ?: AppConstants::DEFAULT_APP_DIRECTORY,
                '--namespace' => $input->getOption('namespace') ?: AppConstants::DEFAULT_APP_NAMESPACE,
            ]);

            $makeControllerCommand->run($arguments, $output);
        }
    }
}
