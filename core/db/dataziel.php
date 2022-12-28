<?php

/*

*/

namespace App\Core\db;

class Dataziel {
	
	public $sPath;
	public $sTable;
	
	function __construct($sPath, $sTable) {
		$this->sPath = $sPath;
		$this->sTable = $sTable;
		var_dump('-Sockets');
	}
	
	function invoking_callouts() {
		var_dump('--invoking_callouts');
		var_dump($this->aQuery);
		return true;
	}
}

?>