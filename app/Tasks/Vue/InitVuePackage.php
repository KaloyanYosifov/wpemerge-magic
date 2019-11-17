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
        $output->writeln('Installing Vue...');

        $this->checkIfWeHavePackageFile();

        if (App::isOnTestMode()) {
            return;
        }

        $installVueProcess = new Process([
            'npm',
            'install',
            'vue vue-loader',
        ]);

        $installVueProcess->run();

        // executes after the command finishes
        if (!$installVueProcess->isSuccessful()) {
            throw new TaskFailedException('Couldn\'t install Vue!');
        }
    }

    public function checkIfWeHavePackageFile()
    {
        $packageJsonFile = (new CreatePath)->create(Path::getCurrentWorkingDirectory(), [
            'package.json',
        ], false);

        if (App::isOnTestMode()) {
            (new Filesystem())->dumpFile($packageJsonFile, $this->getTestPackageJsonData());

            return;
        }

        if (!file_exists($packageJsonFile)) {
            $npmInitProcess = new Process([
                'npm',
                'init',
                '-y',
            ]);

            $npmInitProcess->run();

            // executes after the command finishes
            if (!$npmInitProcess->isSuccessful()) {
                throw new TaskFailedException('Couldn\'t init Composer!');
            }
        }
    }

    protected function getTestPackageJsonData(): string
    {
        $composerData = [
            'name' => 'wpemerge-magic',
            'version' => '1.0.0',
            'description' => '---',
            'main' => 'index.js',
            'directories' => [
                'test' => 'tests',
            ],
            'scripts' => [
                'test' => 'echo \'Error: no test specified\' && exit 1',
            ],
            'license' => 'ISC',
        ];

        return json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
