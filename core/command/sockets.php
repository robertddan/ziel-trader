<?php


namespace App\Core\Command;

class Sockets {
	
	private $aQuery;
	
	function __construct() {
		$this->aQuery = $_GET;
		var_dump('-Sockets');
	}
	
	function server() {
		var_dump('--invoking_callouts');
		var_dump($this->aQuery);
		return true;
	}
	
	function client() {
		var_dump('--invoking_callouts');
		var_dump($this->aQuery);
		return true;
	}
	
}

?>