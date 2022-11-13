<?php

require __DIR__.'/../../config/bootstrap.php';
print '<pre>';

use App\Core;
use App\Core\Http\Router;

$oCore = new Core();
$oHttp = new Router();

var_dump($oHttp->invoking_callouts());
print '</pre>';

?>