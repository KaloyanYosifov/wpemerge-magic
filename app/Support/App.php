<?php

namespace WPEmergeMagic\Support;

class App
{
    public static function isOnTestMode()
    {
        return getenv('ENV') === 'testing';
    }
}
