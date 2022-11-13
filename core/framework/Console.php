<?php


namespace App\Core\Framework;

class Console
{
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
		$sNamespaceClass = "App\\Suiteziel\\". ucfirst($aArgv[0]) ."\\". ucfirst($aArgv[1]) ."\\Command\\Command_". lcfirst($aArgv[2]);
		var_dump( $sNamespaceClass);
		$oClass = new $sNamespaceClass;
		call_user_func_array( array( $oClass, 'configure'), array($aArgv) );
  }

}

?>