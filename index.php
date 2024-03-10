<?php

require_once 'lib/Autoload.php';

use SQL\Drivers\MySQL;

Autoload::load('lib');
Autoload::init();
Env::init('.env');
Session::init($_ENV);
