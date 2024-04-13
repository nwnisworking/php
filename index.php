<?php
require_once 'lib/Autoload.php';
use SQL\Driver;
use SQL\Util\Condition;

define('DS', DIRECTORY_SEPARATOR);

Autoload::load(__DIR__.DS.'lib');
Autoload::init();