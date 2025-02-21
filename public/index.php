<?php

define('DS', DIRECTORY_SEPARATOR);
chdir(__DIR__ . DS . '..' . DS);
define('ROOT', getcwd() . DS);
define('CONFIG', ROOT . 'config' . DS);
define('VENDOR', ROOT . 'vendor' . DS);

#var_dump([__DIR__, getcwd(), ROOT, CONFIG, VENDOR]);

require(CONFIG . 'bootstrap.php');

/* will be replaced by http */
$aRouter = array();
$aRouter = parse_url('/'. $_SERVER["REQUEST_URI"]);

if (isset($aRouter['host'])) //[["host"]=>"favicon.ico"
switch ($aRouter['host']) {
    case 'favicon.ico':
        header('Content-Type: text/x-icon');
        print file_get_contents(ROOT .'public'. DS .'favicon.ico');
        exit();
        break;
}
/* end will be replaced by http */


echo '<pre>';

use Ziel\Framework\View;
new View();

echo '</pre>';
