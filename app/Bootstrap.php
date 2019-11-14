<?php

namespace WPEmergeMagic;

use WPEmergeMagic\Console\Console;

class Bootstrap
{
    public function __construct() {}

    /**
     * Handle all bootstrap logic
     * required for the library to work
     *
     * @return void
     */
    public function handle()
    {
        define('ROOT_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);
        define('APP_DIR', __DIR__);

        $this->register();
        $this->run();
    }

    /**
     * Handle all bootstrap logic
     * required for the library to work on test mode
     *
     * @return void
     */
    public function handleForTests()
    {
        Console::activateTestMode();

        $this->handle();
    }

    /**
     * All configuration required to be done
     * before executing
     *
     * @return void
     */
    protected function register()
    {
        Console::register();
    }

    /**
     * Executing code
     *
     * @return void
     */
    protected function run()
    {
        Console::run();
    }
}
