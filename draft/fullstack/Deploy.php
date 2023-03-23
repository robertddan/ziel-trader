<?php
#deployment

/*

fullstack-deploy-core.thread.socket.server.ip
fullstack-deploy-core.thread.socket.client.route

alias suiteziel
1. web server
2. run server
3. run router
4. start listen requests/
5. on request init child for multi threading.

*/


namespace App\Draft\Fullstack;

use App\Draft\Fullstack\Container;
use App\Draft\Fullstack\Resource;
use App\Draft\Fullstack\Stack;


class Deploy {
	
	private $aQuery;
	
	function __construct() {
		$this->aQuery = $_GET;
		var_dump('-Deploy');
	}
	
	function configure() {
		var_dump('--invoking-configure');
		var_dump($this->aQuery);
		
		$oStack = new Stack();
		#$oResource = new Resource();
		#$oContainer = new Container();
		
		return true;
	}
}



?>


