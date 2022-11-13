<?php


namespace App\Draft\Backend;

class Channel {
	
	private $aChannel;
	
	function __construct() {
		$this->aChannel = $_GET;
		var_dump('-Router');
	}
	
	function invoking_callouts() {
		var_dump('--invoking_callouts');
		var_dump($this->aChannel);
		return true;
	}
}

?>