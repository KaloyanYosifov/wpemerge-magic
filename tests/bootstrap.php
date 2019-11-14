<?php

define('ROOT_TEST_DIR', __DIR__);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
(new \WPEmergeMagic\Bootstrap())->handleForTests();
