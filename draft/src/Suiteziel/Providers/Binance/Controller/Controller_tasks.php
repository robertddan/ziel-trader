<?php

namespace App\Suiteziel\Providers\Binance\Controller;

use App\Suiteziel\Framework\Controller;
use ccxt;


class Controller_tasks extends Controller
{
  public $aSymbols = array();
  public $oExchange;

  public function __construct()
  {
    $sExchange = "\\ccxt\\binance";
    $this->oExchange = new $sExchange($this->api_keys());
    $this->oExchange->load_markets();
  }

  protected function api_keys()
  {
    return array(
      'apiKey' => '',
      'secret' => '',
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