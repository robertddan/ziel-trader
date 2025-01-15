<?php

namespace App\Suiteziel\Ziel\Draft\Controller;

use App\Suiteziel\Framework\Controller;
use App\Suiteziel\Ziel\Draft\Controller\Controller_js;


class Controller_tasks extends Controller
{

  public function __construct()
  {
		$this->oJs = new Controller_js();
  }

  public function run()
  {
		return true;
  }

  public function api()
  {
		$aUri = array_filter(explode('/', $_SERVER['REQUEST_URI']));
		
		if (isset($_POST['wallet']))
		{
			$sJsonData = $_POST['wallet'];
			return $this->oJs->curl_request_wallet($aUri[4], $sJsonData);
		}
		elseif (isset($_POST['exchange']))
		{
			$sJsonData = $_POST['exchange'];
			return $this->oJs->curl_request_exchange($aUri[4], $sJsonData);
		}
  }
}