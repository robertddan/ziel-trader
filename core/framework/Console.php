<?php


namespace App\Core\Framework;

class Console
{
	public $aArguments;

  public function call() :bool
  {
		print('_____________'. __NAMESPACE__. '__'. __FUNCTION__ .'_____________'. PHP_EOL);
		$aArgv = $_SERVER['argv'];
		if (empty($aArgv[1])) die('no argv given'. PHP_EOL);
		$this->aArguments = explode(':', $aArgv[1]);
		if (!$this->call_class()) die('call_class');
		return true;
  }

	/*
	* Command to run the server:
	* php /bin/suiteziel http:server:run
	*/
  public function call_class () :bool
  {
		#split argv
		$sNamespace = "App\\". ucfirst($this->aArguments[0]) ."\\". ucfirst($this->aArguments[1]) ."\\". ucfirst($this->aArguments[2]);
		var_dump($sNamespace);
		$oClass = new $sNamespace;
		call_user_func_array(array($oClass, 'configure'), array($this->aArguments));
		return true;
  }

}

?>