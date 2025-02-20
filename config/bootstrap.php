<?php

$loader = require __DIR__ .'/../vendor/autoload.php';

#var_dump($loader->addPsr4('App\\Suiteziel\\BitMart\\', __DIR__ .'/../vendor/bitmart-php-sdk-api/src/BitMart/')); # .'/../vendor/bitmart-php-sdk-api/src/BitMart'));

define('VAR_DIR', __DIR__ .'/../../var/');

var_dump(file_exists(VAR_DIR));