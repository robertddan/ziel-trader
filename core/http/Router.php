<?php


namespace App\Core\Http;

class Router {
	function __construct() {
		var_dump('Router');
	}
	
	function invoking_callouts() {
		var_dump('invoking_callouts');
		return true;
	}
}

?>