<?php

namespace App\Suiteziel\Providers\Bitvavo\Controller;

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
      'apiKey' => '1ecccf9eb07d4cf40670c6fb8cab268305d0da624507301fe366b2f480682dc2',
      'secret' => '2f192c71e617816043b70e694d3dd0535cc055015f11039e9125febfed69d3352b00fcf43c3d57e19e3f4e9c3ab48013a59a4665af75020cd27dd8a1b63fdf52',
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