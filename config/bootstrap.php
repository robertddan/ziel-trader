<?php

require __DIR__ .'/../vendor/autoload.php';

!defined('ROOT') && define('ROOT', __DIR__ .'/../');
!defined('DRAFT') && define('DRAFT', ROOT .'/backend/');


# errors
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
# memory
ini_set('memory_limit', -1);
ini_set('ignore_user_abort', true);
ini_set('max_execution_time', 0);
# trader
ini_set('trader.real_precision', 8);
# xdebug
#ini_set('xdebug.max_nesting_level', 14000);
#ini_set('xdebug.var_display_max_depth', '10');
#ini_set('xdebug.var_display_max_children', '256');
#ini_set('xdebug.var_display_max_data', '1024');
# bc math scale
call_user_func('bcscale', 0);
# session
#if (session_status() !== 2) session_start();
#var_dump(session_save_path());

?>