<?php

require __DIR__.'/../../config/bootstrap.php';
print '<pre>';

use App\Core\Http\Router;
$oHttp = new Router();

var_dump($oHttp->invoking_callouts());
print '</pre>';

?>