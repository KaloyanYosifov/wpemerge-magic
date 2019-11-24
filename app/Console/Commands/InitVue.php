<?php

namespace WPEmergeMagic\Console\Commands;

use Symfony\Component\Console\Command\Command;
use WPEmergeMagic\Tasks\Vue\InitializeWebpackTask;
use WPEmergeMagic\Tasks\Vue\InstallVuePackageTask;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitVue extends Command
{
    protected static $defaultName = 'init-vue';

    /**
     * List of tasks
     *
     * @var array
     */
    protected $tasks = [
        InstallVuePackageTask::class,
        InitializeWebpackTask::class,
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
