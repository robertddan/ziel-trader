<?php


namespace App\Core;

class Http {
	function __construct() {
		var_dump('Http');
	}
	
	function invoking_callouts() {
		var_dump('invoking_callouts');
		return true;
	}
}

?>