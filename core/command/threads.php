<?php


namespace App\Core\Command;

class Threads {
	
	private $aQuery;
	
	function __construct() {
		$this->aQuery = $_GET;
		var_dump('-Threads');
	}
	
	function invoking_callouts() {
		var_dump('--invoking_callouts');
		var_dump($this->aQuery);
		return true;
	}
}

?>