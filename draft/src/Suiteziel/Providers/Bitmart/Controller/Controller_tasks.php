<?php

namespace App\Suiteziel\Providers\Bitmart\Controller;

use App\Suiteziel\Framework\Controller;
use ccxt;


class Controller_tasks extends Controller
{
  public $aSymbols = array();
  public $oExchange;

  public function __construct()
  {
    $sExchange = "\\ccxt\\bitmart";
    $this->oExchange = new $sExchange($this->api_keys());
    $this->oExchange->load_markets();
  }

  protected function api_keys()
  {
    return array(
      'apiKey' => '781f5debbbf2597e50821665a7aaa1fedf4f2151',
      'secret' => 'd635bd66d2c1d8b29504a58095beae1d9598e14f0bb77a600027b8e9b05fb3bd',
    );
  }

  public function run($bCloseTrades = true)
  {
    foreach ($this->oExchange->symbols as $sSymbols)
    {
      if(strpos($sSymbols, 'USDT') !== false) array_push($this->aSymbols, $sSymbols);
    }

    var_dump($this->aSymbols);
  }

}