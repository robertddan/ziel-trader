<?php

require __DIR__.'/../../config/bootstrap.php';

echo '<pre>';
use App\Core;
use App\Core\Http;

$oCore = new Core();
$oReal = new Http();

?>