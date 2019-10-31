<?php

namespace WPEmergeMagic\Exceptions;

class TaskFailedException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
