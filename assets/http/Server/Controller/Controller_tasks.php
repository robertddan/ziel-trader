<?php

namespace App\Suiteziel\Http\Server\Controller;

use App\Suiteziel\Framework\Controller;


class Controller_tasks extends Controller
{

  public function __construct()
  {

  }

	/*
		pkill -9 php
	*/

  public function run()
  {
		$output = null;
		$retval = null;
    $sUrl = "0.0.0.0:80";
    $sPathDir = "./draft/www/public/index.php";
    var_dump("Hello World Http-Server!");
    exec("php -S $sUrl $sPathDir");

  }

	public function execz($sUrl){
		//screen -d -S sqlite -m /usr/local/Cellar/php/7.4.11/bin/php ~/Development/sticks/draft/bin/console app:oanda:sqlite
		return exec("screen -d -S ". $sUrl ." -m ping www.google.com");
	}
	
  public function tesks()
  {

		$a = array_map(array($this,'execz'), array(
			"first_command",
			"second_command"
		));
		
		var_dump($a);
/*
		$output = null;
		$retval = null;
		$sUrl = "0.0.0.0:80";
		$sPathDir = "./draft/www/public/index.php";
		var_dump("Hello World Http-Server!");
		exec("php -S $sUrl $sPathDir");


		$socket = stream_socket_server('tcp://0.0.0.0:1037');
		stream_set_timeout($socket, -1);
		while ($conn = stream_socket_accept($socket)) {
			var_dump($conn);
			fwrite($conn, "Hallo, hier spricht der Server.\n");
			fclose($conn);
		}
		fclose($socket);
*/
		
  }
}