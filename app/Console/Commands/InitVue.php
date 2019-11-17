<?php

namespace WPEmergeMagic\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Init extends Command
{
    protected static $defaultName = 'init';

    /**
     * List of tasks
     *
     * @var array
     */
    protected $tasks = [

    ];

    protected function configure()
    {
        $this->setDescription('Init Vue in project.')
            ->setHelp('Initialize Vue in project.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->tasks as $task) {
            (new $task)->handle($input, $output, $this);
        }
    }
}
