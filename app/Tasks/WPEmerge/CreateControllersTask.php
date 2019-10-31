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
        $controllerNames = [
            'HomeController',
            'AdminController',
            'AjaxController',
        ];

        foreach ($controllerNames as $controllerName) {
            $arguments = new ArrayInput([
                'name' => $controllerName,
            ]);

            $command->run($arguments, $output);
        }
    }
}
