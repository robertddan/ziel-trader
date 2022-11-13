<?php

require __DIR__ .'/../vendor/autoload.php';

!defined('ROOT') && define('ROOT', __DIR__ .'/../');
!defined('DRAFT') && define('DRAFT', ROOT .'/backend/');

error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

?>