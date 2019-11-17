<?php

namespace WPEmergeMagic\Tasks\Vue;

use WPEmergeMagic\Support\Path;
use Symfony\Component\Finder\Finder;
use WPEmergeMagic\Parsers\StubParser;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitializeWebpackTask
{
    protected $webpackPath = '';

    public function handle(InputInterface $input, OutputInterface $output, Command $command)
    {
        $output->writeln('Initializing webpack...');

        $this->checkIfWeHaveAWebpackJsFile();
        $this->addVueLoader();
    }

    protected function checkIfWeHaveAWebpackJsFile()
    {
        $this->webpackPath = $this->findWebpackFile(Path::getCurrentWorkingDirectory());

        if (!$this->webpackPath) {
            $this->webpackPath = (new CreatePath)->create(Path::getCurrentWorkingDirectory(), [
                'config',
                'webpack.config.js',
            ]);

            (new Filesystem())
                ->dumpFile(
                    $this->webpackPath,
                    (new StubParser('webpack.mix.js'))
                );
        }
    }

    protected function findWebpackFile(string $startPath): string
    {
        $finder = new Finder();
        $finder->files()->in($startPath)->name('webpack.config.js')->exclude(['node_modules', 'vendor']);

        if (!$finder->hasResults()) {
            return '';
        }

        foreach ($finder as $file) {
            return $file->getRealPath();
        }
    }

    protected function addVueLoader() {}
}
