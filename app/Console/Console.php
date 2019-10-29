<?php

namespace WPEmergeMagic\Console;

use Symfony\Component\Console\Application;

class Console
{
    /**
     * The application where all the commands will be stored
     *
     * @var [Symfony\Component\Console\Application]
     */
    protected $application = null;

    public function __construct()
    {
        $this->application = new Application();
    }

    public function register() {}

    public function run()
    {
        $this->application->run();
    }
}
