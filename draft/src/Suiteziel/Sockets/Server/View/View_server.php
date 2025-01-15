<?php

namespace App\Suiteziel\Sockets\Server\View;


use App\Suiteziel\Framework\View;
#
//use App\Suiteziel\Sockets\Controller\Controller_wallet;
use App\Suiteziel\Sockets\Server\Controller\Controller_tasks;
use App\Suiteziel\Sockets\Server\Controller\Controller_udp_server;
#
use Twig\Loader\ArrayLoader;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
#


class View_server extends View
{

  public function __construct($aUri = false)
  {
    $this->aUri = $aUri;
    if (!$this->set_variables()) exit('set_variables');
  }

  public function set_variables()
  {
    # template
    $this->oTwig = new Environment(new FilesystemLoader(dirname(__DIR__) .'/Template/'), ['debug' => true]);
    $this->oTwig->addExtension(new \Twig\Extension\DebugExtension());
    
    # setup
    #$this->oCache = new Controller_cache('view');
    $this->oTasks = new Controller_tasks();
    
    #$this->oTrade = new Model_trade();
    #$this->oApi = new Model_api();
    return true;
  }

  public function index()
	{
/*
1. starts socket server 
2. 
*/
/*
$socket = stream_socket_client('tcp://127.0.0.1:1037');
while (!feof($socket)) {
	echo fread($socket, 100);
}
fclose($socket);
*/
		
		
		//$this->oTasks->run();
    print $this->oTwig->render('index.html.twig');
  }
	
	

}


?>