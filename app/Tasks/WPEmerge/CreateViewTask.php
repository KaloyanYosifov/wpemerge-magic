<?php

namespace WPEmergeMagic\Tasks\WPEmerge;

use WPEmergeMagic\Parsers\StubParser;
use WPEmergeMagic\Support\CreatePath;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateViewTask
{
    public function handle(InputInterface $input, OutputInterface $output, Command $command)
    {
        $this->createLayout($output);
        $this->createView($output);
    }

    public function createLayout(OutputInterface $output)
    {
        $layoutFile = (new CreatePath)->create(getcwd(), [
            'app',
            'views',
            'layouts',
            'default.php',
        ], false);

        if (\file_exists($layoutFile)) {
            return;
        }

        $output->writeln('Creating layout!');

        (new Filesystem)->dumpFile(
            $layoutFile,
            (new StubParser)->parseViaStub('layout')
        );
    }

    public function createView(OutputInterface $output)
    {
        $homeViewFile = (new CreatePath)->create(getcwd(), [
            'app',
            'views',
            'home',
            'home.php',
        ], false);

        if (\file_exists($homeViewFile)) {
            return;
        }

        $output->writeln('Creating home view!');

        (new Filesystem)->dumpFile(
            $homeViewFile,
            (new StubParser)->parseViaStub('home-view')
        );
    }
}
