<?php


namespace App\Core\API;

class Vendor {
	
	private $aQuery;
	
	function __construct() {
		$this->aQuery = $_GET;
		var_dump('-Router');
	}
	
	function oanda() {
		var_dump('--oanda');
		var_dump($this->aQuery);
		return true;
	}
	
	function binance() {
		var_dump('--binance');
		var_dump($this->aQuery);
		return true;
	}
}

?>