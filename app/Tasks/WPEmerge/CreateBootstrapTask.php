<?php

namespace WPEmergeMagic\Tasks\WPEmerge;

use WPEmergeMagic\Support\Path;
use WPEmergeMagic\Parsers\StubParser;
use WPEmergeMagic\Support\CreatePath;
use WPEmergeMagic\Support\FileReader;
use WPEmergeMagic\Constants\AppConstants;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class CreateBootstrapTask
{
    public function handle(InputInterface $input, OutputInterface $output, Command $command)
    {
        $this->appDirectory = preg_replace('~\/~', '', $input->getOption('dir') ?: AppConstants::DEFAULT_APP_DIRECTORY);
        $bootstrapFile = (new CreatePath)
            ->create(Path::getCurrentWorkingDirectory(), [
                $this->appDirectory,
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

        $this->addBootstrapToFunctionsFile();
    }

    public function addBootstrapToFunctionsFile()
    {
        $functionsFilePath = (new CreatePath())->create(Path::getCurrentWorkingDirectory(), [
            'functions.php',
        ], false);

        if (!$functionsFilePath) {
            throw new FileNotFoundException('The functions.php file couldn\'t be found! Are you on a wordpress theme?');
        }

        $fileReader = new FileReader();
        $addedBootstrapRequire = false;
        $fileLines = '';

        foreach ($fileReader->readLines($functionsFilePath) as $fileLine) {
            if (preg_match('~<\?(=|php)~', $fileLine) && !$addedBootstrapRequire) {
                $fileLine .= $this->addBootstrapRequire();
                $addedBootstrapRequire = true;
            }

            $fileLines .= $fileLine;
        }

        (new Filesystem)->dumpFile(
            $functionsFilePath,
            $fileLines
        );
    }

    protected function addBootstrapRequire(): string
    {
        return PHP_EOL . 'require_once __DIR__ . DIRECTORY_SEPARATOR . ' . $this->appDirectory . ' . DIRECTORY_SEPARATOR . bootstrap.php;';
    }
}
