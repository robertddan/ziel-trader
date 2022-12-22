<?php


namespace App\Core\Proposal;

class Pivots {
	
	private $aQuery;
	
	function __construct() {
		$this->aQuery = $_GET;
		var_dump('-Router');
	}
	
	function invoking_callouts() {
		var_dump('--invoking_callouts');
		var_dump($this->aQuery);
		return true;
	}
}

?>