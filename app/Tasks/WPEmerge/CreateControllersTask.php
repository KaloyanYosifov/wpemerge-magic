<?php

namespace WPEmergeMagic\Tasks\WPEmerge;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateControllersTask
{
    public function handle(InputInterface $input, OutputInterface $output, Command $command)
    {
        $this->createControllers($command->getApplication()->find('make:controller'), $output);
    }

    protected function createControllers(Command $command, OutputInterface $output)
    {
        $output->writeln('Creating controllers...');

        $controllerNames = [
            'home' => 'HomeController',
            'admin' => 'AdminController',
            'ajax' => 'AjaxController',
        ];

        foreach ($controllerNames as $controllerType => $controllerName) {
            $arguments = new ArrayInput([
                'name' => $controllerName,
                '--type' => $controllerType,
                '--silent' => true,
            ]);

            $command->run($arguments, $output);
        }
    }
}
