<?php

namespace WPEmergeMagic\Console;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Application;

class Console
{
    // static
    protected static $instance = null;

    // properties
    protected $testMode = false;

    /**
     * The application where all the commands will be stored
     *
     * @var Symfony\Component\Console\Application
     */
    protected $application = null;

    protected function __construct()
    {
        $this->application = new Application();
    }

    protected function register()
    {
        $this->registerAllCommands();
    }

    protected function run()
    {
        !$this->testMode && $this->application->run();
    }

    protected function activateTestMode(): self
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

    protected function getApplication(): Application
    {
        return $this->application;
    }

    /** static */
    public static function __callStatic($method, $arguments)
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return call_user_func_array([static::$instance, $method], $arguments);
    }
}
