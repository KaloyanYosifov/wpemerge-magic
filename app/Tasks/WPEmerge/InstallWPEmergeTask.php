<?php

namespace WPEmergeMagic\Tasks\WPEmerge;

use WPEmergeMagic\Support\App;
use WPEmergeMagic\Support\Path;
use WPEmergeMagic\Support\CreatePath;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
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

        if (App::isOnTestMode()) {
            return;
        }

        $installWPEmergeProcess = new Process([
            'composer',
            'require',
            'htmlburger/wpemerge',
        ]);

        $installWPEmergeProcess->setTimeout(600);
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

        if (App::isOnTestMode()) {
            (new Filesystem())->dumpFile($composerJsonFilePath, $this->getTestComposerData());

            return;
        }

        if (!\file_exists($composerJsonFilePath)) {
            $composerInitProcess = new Process([
                'composer',
                'init',
            ]);

            $composerInitProcess->setTimeout(600);
            $composerInitProcess->run();

            // executes after the command finishes
            if (!$composerInitProcess->isSuccessful()) {
                throw new TaskFailedException('Couldn\'t init Composer!');
            }
        }
    }

    protected function getTestComposerData(): string
    {
        $composerData = [
            'name' => 'test/wpemerge-magic-tests',
            'version' => '0.0.1',
            'description' => 'Tests.',
            'type' => 'library',
            'license' => 'MIT',
            'homepage' => 'https://test.com',
            'authors' => [
                [
                    'name' => 'Test Testov',
                    'email' => 'test@example.com',
                ],
            ],
            'require' => [
                'htmlburger/wpemerge' => 'dev',
            ],
        ];

        return json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
