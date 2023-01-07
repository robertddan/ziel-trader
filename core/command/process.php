<?php

/*

*/

namespace App\Core\Command;

class Process {
	
	private $aQuery;
	
	function __construct() {
		$this->aQuery = $_GET;
		var_dump('-Sockets');
	}
	
	function invoking_callouts() {
		var_dump('--invoking_callouts');
		var_dump($this->aQuery);
		return true;
	}
	
	function child_race () {
		var_dump('--invoking_callouts');
		var_dump($this->aQuery);
		return true;
	}
	
	function process_main() {
		var_dump('--invoking_callouts');
		var_dump($this->aQuery);
		return true;
	}
}

?>