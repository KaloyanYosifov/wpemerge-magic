<?php

namespace WPEmergeMagic\Tasks\Vue;

use WPEmergeMagic\Support\Path;
use WPEmergeMagic\Parsers\JsParser;
use Symfony\Component\Finder\Finder;
use WPEmergeMagic\Parsers\StubParser;
use WPEmergeMagic\Support\CreatePath;
use WPEmergeMagic\Support\FileReader;
use WPEmergeMagic\Composers\JsComposer;
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
            ], false);

            (new Filesystem())
                ->dumpFile(
                    $this->webpackPath,
                    (new StubParser())->parseViaStub('webpack.mix.js')
                );
        }
    }

    protected function findWebpackFile(string $startPath): string
    {
        if (!file_exists($startPath)) {
            return '';
        }

        $finder = new Finder();
        $finder->files()->in($startPath)->name('webpack.config.js')->exclude(['node_modules', 'vendor']);

        if (!$finder->hasResults()) {
            return '';
        }

        foreach ($finder as $file) {
            return $file->getRealPath();
        }
    }

    protected function addVueLoader()
    {
        $fileReader = new FileReader();
        $webpackContents = ["const VueLoaderPlugin = require('vue-loader/lib/plugin');"];
        $webpackExportsContent = [];
        $startRecievingExportContent = false;

        foreach ($fileReader->readLines($this->webpackPath) as $fileLine) {
            if ($this->stringStartsWith($fileLine, 'module.exports') && !$startRecievingExportContent) {
                $startRecievingExportContent = true;
                $webpackExportsContent[] = explode('=', $fileLine)[1];

                continue;
            }

            if ($startRecievingExportContent) {
                $webpackExportsContent[] = $fileLine;
            } else {
                $webpackContents[] = $fileLine;
            }
        }

        $webpackExportJsObject = (new JsParser())->parse(implode('', $webpackExportsContent));
        $webpackExportJsObject = $this->addVueLoaderToObject($webpackExportJsObject);
        $webpackContents[] = 'module.exports = ' . stripslashes((new JsComposer())->compose($webpackExportJsObject)) . ';';

        (new Filesystem())
            ->dumpFile($this->webpackPath, implode(PHP_EOL, $webpackContents));
    }

    protected function addVueLoaderToObject(array $webpackExportJsObject): array
    {
        if (!array_key_exists('module', $webpackExportJsObject)) {
            $webpackExportJsObject['module'] = [];
        }

        if (!array_key_exists('rules', $webpackExportJsObject['module'])) {
            $webpackExportJsObject['module']['rules'] = [];
        }

        $webpackExportJsObject['module']['rules'][] = [
            'test' => '/\.vue$/',
            'loader' => 'vue-loader',
        ];

        return $webpackExportJsObject;
    }

    protected function stringStartsWith(string $string, string $startString): bool
    {
        return substr($string, 0, strlen($startString)) === $startString;
    }
}
