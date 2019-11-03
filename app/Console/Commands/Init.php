<?php

namespace WPEmergeMagic\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use WPEmergeMagic\Tasks\WPEmerge\CreateViewTask;
use WPEmergeMagic\Tasks\WPEmerge\AddAutoloadTask;
use WPEmergeMagic\Tasks\WPEmerge\CreateRouteTask;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WPEmergeMagic\Tasks\WPEmerge\CreateBootstrapTask;
use WPEmergeMagic\Tasks\WPEmerge\InstallWPEmergeTask;
use WPEmergeMagic\Tasks\WPEmerge\CreateControllersTask;

class Init extends Command
{
    protected static $defaultName = 'init';

    /**
     * List of tasks
     *
     * @var array
     */
    protected $tasks = [
        CreateRouteTask::class,
        CreateControllersTask::class,
        CreateBootstrapTask::class,
        CreateViewTask::class,
        InstallWPEmergeTask::class,
        AddAutoloadTask::class,
    ];

    protected function configure()
    {
        $this->setDescription('Init WPEmerge project.')
            ->setHelp('Initialize WPEmerge project.');

        // options
        $this
            ->addOption('dir', 'd', InputOption::VALUE_REQUIRED, 'Set the name of the first directory name (default is app)')
            ->addOption('namespace', 'ns', InputOption::VALUE_REQUIRED, 'Set the namespace the app is going to use.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->tasks as $task) {
            (new $task)->handle($input, $output, $this);
        }
    }
}
