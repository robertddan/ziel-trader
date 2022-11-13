<?php

require __DIR__.'/../../config/bootstrap.php';
print '<pre>';

use App\Core;
use App\Core\Http;

$oCore = new Core();
$oHttp = new Http();

var_dump($oHttp->invoking_callouts());
print '</pre>';

?>