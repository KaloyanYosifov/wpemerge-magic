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
    protected $initliazeVue = true;

    public function handle(InputInterface $input, OutputInterface $output, Command $command)
    {
        $output->writeln('Initializing webpack...');

        $this->checkIfWeHaveAWebpackJsFile();

        $this->initliazeVue && $this->addVueLoader();
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
                    (new StubParser())->parseViaStub('webpack.config.js')
                );

            $this->initliazeVue = false;
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
            $webpackExportJsObject['module'] = [
                'rules' => [],
            ];
        }

        if (!array_key_exists('plugins', $webpackExportJsObject)) {
            $webpackExportJsObject['plugins'] = [];
        }

        if (!array_key_exists('resolve', $webpackExportJsObject)) {
            $webpackExportJsObject['resolve'] = [
                'alias' => [],
            ];
        }

        $webpackExportJsObject['module']['rules'][] = [
            'test' => '/\.vue$/',
            'loader' => 'vue-loader',
        ];

        $webpackExportJsObject['plugins'][] = 'new VueLoaderPlugin()';

        $webpackExportJsObject['resolve']['alias'] = [
            'vue$' => 'vue/dist/vue.runtime.esm.js',
        ];

        return $webpackExportJsObject;
    }

    protected function stringStartsWith(string $string, string $startString): bool
    {
        return substr($string, 0, strlen($startString)) === $startString;
    }
}
