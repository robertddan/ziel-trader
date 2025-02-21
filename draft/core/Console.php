<?php

namespace Ziel\Framework;

class Console
{
  public function __construct()
  {

    # memory
    ini_set('error_reporting', E_ALL);
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
    if (session_status() !== 2) session_start();
    #var_dump(session_save_path());
    
  }

  public function call () :int
  {
    var_dump('call _______________________________________________________________');
    $aArgvParam = $_SERVER['argv'];
    var_dump($aArgvParam);

    $aArgv = explode(':', $aArgvParam[1]);
    $this->call_class($aArgv);
    return 0;
  }

	/*
	 * Command to run the server:
	 * php ./bin/suiteziel http:server:run
	 */
  public function call_class ($aArgv)
  {
		
    $sNamespaceClass = "App\\Suiteziel\\"
        . ucfirst($aArgv[0]) ."\\"
        . ucfirst($aArgv[1]) ."\\Command\\Command_"
        . lcfirst($aArgv[2]);
		
		var_dump( $sNamespaceClass);
    $oClass = new $sNamespaceClass;
    call_user_func_array( array( $oClass, 'configure'), array($aArgv) );
  }

}

