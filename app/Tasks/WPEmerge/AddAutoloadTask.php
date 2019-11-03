<?php

namespace WPEmergeMagic\Tasks\WPEmerge;

use WPEmergeMagic\Support\CreatePath;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\RuntimeException;

class AddAutoloadTask
{
    public function handle(InputInterface $input, OutputInterface $output, Command $command)
    {
        $composerJsonFilePath = (new CreatePath)->create(getcwd(), [
            'composer.json',
        ], false);

        if (!\file_exists($composerJsonFilePath)) {
            throw new RuntimeException('No composer json file found! Have you deleted it when initializing?');
            return;
        }

        // get the json content from composer
        // and convert it to array
        $composerData = json_decode(\file_get_contents($composerJsonFilePath), true);

        if ($this->hasAppAutoloadBeenDefined($composerData)) {
            return;
        }

        $output->writeln('Adding autoload to composer...');

        $composerData = $this->addAutoloadNamespace($composerData, $input);

        file_put_contents(
            $composerJsonFilePath,
            json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    protected function hasAppAutoloadBeenDefined(array $composerData): bool
    {
        if (!\array_key_exists('autoload', $composerData)) {
            return false;
        }

        if (!\array_key_exists('psr-4', $composerData['autoload'])) {
            return false;
        }

        $psr4Autoloads = $composerData['autoload']['psr-4'];

        foreach ($psr4Autoloads as $psr4AutoloadNamespace => $psr4AutoloadFolder) {
            // if the namespace App is defined
            // return true
            if (preg_match('~App\\\\~', $psr4AutoloadNamespace)) {
                return true;
            }
        }

        return false;
    }

    protected function addAutoloadNamespace(array $composerData, InputInterface $input): array
    {
        if (!\array_key_exists('autoload', $composerData)) {
            $composerData['autoload'] = [];
        }

        if (!\array_key_exists('psr-4', $composerData['autoload'])) {
            $composerData['autoload']['psr-4'] = [];
        }

        $dir = $input->getOption('dir') ?: 'app';
        $composerData['autoload']['psr-4']['App\\'] = $dir . '/';

        return $composerData;
    }
}
