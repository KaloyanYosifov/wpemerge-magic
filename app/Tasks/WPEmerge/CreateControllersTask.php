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
        $commandInstance = $command->getApplication()->find('make:controller');
        $arguments = new ArrayInput([
            'name' => 'HomeController',
        ]);

        $commandInstance->run($arguments, $output);
    }
}
