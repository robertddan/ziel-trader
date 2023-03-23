<?php

/*
memory
*/

namespace App\Draft\Fullstack;

class Resource {
	
	private $aQuery;
	
	function __construct() {
		$this->aQuery = $_GET;
		var_dump('-Resource');
	}
	
	function configure() {
		var_dump('--invoking-configure');
		var_dump($this->aQuery);
		return true;
	}
	
}

?>