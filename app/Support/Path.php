<?php

namespace WPEmergeMagic\Support;

class Path
{
    public static function getCurrentWorkingDirectory()
    {
        return getenv('ENV') === 'testing' ? ROOT_TEST_DIR . DIRECTORY_SEPARATOR . 'test-files' : getcwd();
    }
}
