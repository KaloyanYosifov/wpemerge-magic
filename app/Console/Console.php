<?php

namespace WPEmergeMagic\Console;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Application;

class Console
{
    protected $testMode = false;

    /**
     * The application where all the commands will be stored
     *
     * @var Symfony\Component\Console\Application
     */
    protected $application = null;

    public function __construct()
    {
        $this->application = new Application();
    }

    public function register()
    {
        $this->registerAllCommands();
    }

    public function run()
    {
        !$this->testMode && $this->application->run();
    }

    public function activateTestMode(): self
    {
        $this->testMode = true;

        return $this;
    }

    protected function registerAllCommands()
    {
        $COMMANDS_PATH = APP_DIR . DIRECTORY_SEPARATOR . 'Console' . DIRECTORY_SEPARATOR . 'Commands';

        $commandsFinder = new Finder;
        $commandsFinder->files()->in($COMMANDS_PATH);

        if (!$commandsFinder->hasResults()) {
            throw new \RuntimeException('Couldn\'t load commands!');
        }

        foreach ($commandsFinder as $commandFile) {
            $commandClassName = explode('.', $commandFile->getRelativePathName())[0];
            $commandNamespaceClass = 'WPEmergeMagic\\Console\\Commands\\';
            $commandClass = $commandNamespaceClass . $commandClassName;

            $this->application->add(new $commandClass);
        }
    }
}
