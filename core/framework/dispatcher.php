<?php


namespace App\Core\Framework;

class Dispatcher {
	
	private $aQuery;
	
	function __construct() {
		$this->aQuery = $_GET;
		var_dump('-Router');
	}
	
	function dispatch() {
		var_dump('--invoking_callouts');
		var_dump($this->aQuery);
		return true;
	}
}

/*
TODO
- from backend panel form submit to dispatch the new version
*/
?>