<?php

namespace WPEmergeMagic;

use WPEmergeMagic\Console\Console;

class Bootstrap
{
    /**
     * Console class
     *
     * @var [WPEmergeMagic\Console\Console]
     */
    protected $console = null;

    public function __construct()
    {
        $this->console = new Console;
    }

    /**
     * Handle all bootstrap logic
     * required for the library to work
     *
     * @return void
     */
    public function handle()
    {
        define('ROOT_DIR', __DIR__ . '..' . DIRECTORY_SEPARATOR);
        define('APP_DIR', __DIR__);

        $this->register();
        $this->run();
    }

    /**
     * All configuration required to be done
     * before executing
     *
     * @return void
     */
    protected function register()
    {
        $this->console->register();
    }

    /**
     * Executing code
     *
     * @return void
     */
    protected function run()
    {
        $this->console->run();
    }
}
