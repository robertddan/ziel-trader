<?php

function exception_handler (Throwable $exception) {
    print PHP_EOL. $exception->getMessage() .PHP_EOL.
    'On file: '.$exception->getFile() .PHP_EOL.
    'On line: '. $exception->getLine() .PHP_EOL.
    $exception->getTraceAsString() .PHP_EOL;
}

function throw_exception ($sException) {
    throw new Exception($sException);
}

set_exception_handler('exception_handler');

require(VENDOR . DS . "autoload.php");

#$loader = require __DIR__ .'/../vendor/autoload.php';
#define('VAR_DIR', __DIR__ .'/../../var/');
