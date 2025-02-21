<?php

namespace Ziel\Framework;

class Controller 
{
  public function __construct()
  {
/*
    # memory
    ini_set('error_reporting', E_ALL);
    ini_set('memory_limit', -1);
    ini_set('ignore_user_abort', true);
    ini_set('max_execution_time', 0);
    # trader
    ini_set('trader.real_precision', 8);
    # xdebug
    ini_set('xdebug.max_nesting_level', 14000);
    ini_set('xdebug.var_display_max_depth', '10');
    ini_set('xdebug.var_display_max_children', '256');
    ini_set('xdebug.var_display_max_data', '1024');
    # bc math scale
    call_user_func('bcscale', 0);
    # session
    if (session_status() !== 2) session_start();

    $aPath = array_filter(explode('/', $_SERVER['REQUEST_URI']));

    $sNamespace = "App\Suiteziel\Providers\\". lcfirst($aPath[1]) ."\View\View_". lcfirst($aPath[1]);
    #App\Suiteziel\Providers\Oanda\View
    
    if (!class_exists($sNamespace)) return false;
    
    $oNamespace = new $sNamespace($aPath);
    
    return $oNamespace->{$aPath[2]}();
*/
  }

}

