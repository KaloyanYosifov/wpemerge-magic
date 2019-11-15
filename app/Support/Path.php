<?php

namespace WPEmergeMagic\Support;

use WPEmergeMagic\Support\App;

class Path
{
    public static function getCurrentWorkingDirectory()
    {
        return App::isOnTestMode() ? ROOT_TEST_DIR . DIRECTORY_SEPARATOR . 'test-files' : getcwd();
    }
}
