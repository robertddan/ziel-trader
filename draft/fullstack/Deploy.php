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

class Deploy {
	
	private $aQuery;
	
	function __construct() {
		$this->aQuery = $_GET;
		var_dump('-Deploy');
	}
	
	function configure() {
		var_dump('--invoking-configure');
		var_dump($this->aQuery);
		return true;
	}
	
}
?>