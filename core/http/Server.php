<?php


namespace App\Core\Http;

class Server {
	public $aQuery;
	
	function __construct() {
		$this->aQuery = $_GET;
		var_dump('-Router');
	}

	function configure() {
		if (!$this->run()) return false;
	}
	
	function run() {
		#router
		print '<pre>';
			$oDispatcher = new Router();
			$oDispatcher->invoking_callouts();
			$oDispatcher->forwards_response();
		print '</pre>';
		return true;
	}
	
	function invoking_callouts() {
		var_dump('--invoking_callouts');
		var_dump($this->aQuery);
		return true;
	}
}

?>