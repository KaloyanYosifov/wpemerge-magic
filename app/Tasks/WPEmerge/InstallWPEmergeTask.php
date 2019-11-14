<?php

namespace WPEmergeMagic\Tasks\WPEmerge;

use WPEmergeMagic\Support\Path;
use WPEmergeMagic\Support\CreatePath;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use WPEmergeMagic\Exceptions\TaskFailedException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallWPEmergeTask
{
    public function handle(InputInterface $input, OutputInterface $output, Command $command)
    {
        $output->writeln('Installing WPEmerge...');

        $this->checkIfWeHaveComposerFile();

        $installWPEmergeProcess = new Process([
            'composer',
            'require',
            'htmlburger/wpemerge',
        ]);

        $installWPEmergeProcess->run();

        // executes after the command finishes
        if (!$installWPEmergeProcess->isSuccessful()) {
            throw new TaskFailedException('Couldn\'t install WPEmerge!');
        }
    }

    public function checkIfWeHaveComposerFile()
    {
        $composerJsonFilePath = (new CreatePath)->create(Path::getCurrentWorkingDirectory(), [
            'composer.json',
        ], false);

        if (!\file_exists($composerJsonFilePath)) {
            $composerInitProcess = new Process([
                'composer',
                'init',
            ]);

            $composerInitProcess->run();

            // executes after the command finishes
            if (!$composerInitProcess->isSuccessful()) {
                throw new TaskFailedException('Couldn\'t init Composer!');
            }
        }
    }
}
