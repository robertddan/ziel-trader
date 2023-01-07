<?php

/*
storage
*/

namespace App\Draft\Fullstack;

class Container {
	
	private $aQuery;
	
	function __construct() {
		$this->aQuery = $_GET;
		var_dump('-Container');
}

?>