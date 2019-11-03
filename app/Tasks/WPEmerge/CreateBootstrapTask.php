<?php

namespace WPEmergeMagic\Tasks\WPEmerge;

use WPEmergeMagic\Parsers\StubParser;
use WPEmergeMagic\Support\CreatePath;
use WPEmergeMagic\Constants\AppConstants;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateBootstrapTask
{
    public function handle(InputInterface $input, OutputInterface $output, Command $command)
    {
        $bootstrapFile = (new CreatePath)
            ->create(\getcwd(), [
                $input->getOption('dir') ?: AppConstants::DEFAULT_APP_DIRECTORY,
                'bootstrap.php',
            ], false);

        // if the bootstrap file exists
        // dont do anything
        if (file_exists($bootstrapFile)) {
            return;
        }

        $output->writeln('Creating bootstrap file.');

        (new Filesystem)->dumpFile(
            $bootstrapFile,
            (new StubParser)->parseViaStub('bootstrap')
        );
    }
}
