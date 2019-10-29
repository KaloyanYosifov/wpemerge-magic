<?php

namespace WPEmergeMagic\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Inspiration extends Command
{
    protected static $defaultName = 'inspiration';

    protected function configure()
    {
        $this->setDescription('An inspiration for the day.')
            ->setHelp('This command displays inspirational quotes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Believe in yourself! Never give up!');
    }
}
