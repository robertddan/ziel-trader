<?php

namespace App\Suiteziel\Sockets\Server\Controller;

use App\Suiteziel\Framework\Controller;
use App\Suiteziel\Sockets\Server\Controller\Controller_udp_server;
use App\Suiteziel\Sockets\Server\Controller\Controller_udp_client;

class Controller_tasks extends Controller
{

  public function __construct()
  {

  }

  public function run($aArgs)
  {
		
		if ($aArgs[3] == 'server') {
			$udp_server = new Controller_udp_server();
			print $udp_server->udp_server();
		}
		else if ($aArgs[3] == 'client') {
			$udp_client = new Controller_udp_client();
			print $udp_client->udp_client();
		}


  }

}