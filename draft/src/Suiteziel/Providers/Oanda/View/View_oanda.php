<?php

namespace App\Suiteziel\Providers\Oanda\View;


use App\Suiteziel\Framework\View;
#
use App\Suiteziel\Providers\Oanda\Controller\Controller_settings;
use App\Suiteziel\Providers\Oanda\Controller\Controller_prices;
use App\Suiteziel\Providers\Oanda\Controller\Controller_priors;
use App\Suiteziel\Providers\Oanda\Controller\Controller_sticks;
use App\Suiteziel\Providers\Oanda\Controller\Controller_swings;
use App\Suiteziel\Providers\Oanda\Controller\Controller_cache;
use App\Suiteziel\Providers\Oanda\Controller\Controller_pivots;
use App\Suiteziel\Providers\Oanda\Controller\Controller_expo;
use App\Suiteziel\Providers\Oanda\Controller\Controller_sr;
use App\Suiteziel\Providers\Oanda\Controller\Controller_trends;

// use App\Suiteziel\Providers\Oanda\Model\Model_trade;
// use App\Suiteziel\Providers\Oanda\Model\Model_api;
#
use Twig\Loader\ArrayLoader;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
#

/*
To do, Sofort.: 
  - Trend liness starting Thursday - on Friday.
  - On weekends, the stream loop stops and opens late on Sundays.

  - read database market orders and resume. to continue bind databases.

  - S/R + TBP TST etc
*/


class View_oanda extends View
{
  public $aUri;
  public $aView;
  public $aaView;
  public $sPair;
  public $iScale;
  public $fRatio;
  public $fMinPrice;

  public function __construct($aUri = false)
  {
    $this->aUri = $aUri;
    if (!$this->set_variables()) exit('set_variables');
  }

  public function set_variables()
  {
    # variables class
    if (empty($this->aUri[3])) exit('No pair selected. Url example: /oanda/index/cad_jpy'); # TODO: translate class
    $this->sPair = strtoupper($this->aUri[3]);

    # template
    $this->oTwig = new Environment(new FilesystemLoader(dirname(__DIR__) .'/Template/'), ['debug' => true]);
    $this->oTwig->addExtension(new \Twig\Extension\DebugExtension());
    
    # setup
    #$this->oCache = new Controller_cache('view');
    $this->oSettings = new Controller_settings();
    $this->oPrices = new Controller_prices($this->aUri);
    $this->oSticks = new Controller_sticks();
    $this->oPriors = new Controller_priors();
    $this->oSwings = new Controller_swings();
    $this->oPivots = new Controller_pivots();
    $this->oExpo = new Controller_expo();
    $this->oSr = new Controller_sr();
    $this->oTrends = new Controller_trends();
    
    #$this->oTrade = new Model_trade();
    #$this->oApi = new Model_api();
    return true;
  }

  public function index()
	{
    $_start = microtime(true);
    $sp__ = $this->sPair;
    #$CachedString = $this->oCache->cm__->getItem($sp__);
    #var_dump($this->oCache->cm__->deleteItem($sp__));

    if (!$this->setup_view()) exit('setup_view');


    echo '<pre>';

#exit();

    $i = 0;
    foreach($this->aaView as $k__ => &$aPrice) 
    {
      # swing
      #if (!$this->oSwings->swings_view_client($aPrice, $this->aView, false)) exit('swings_view_client');
      #if (!$this->oTrends->trends_client($aPrice, $this->aView)) exit('trends_client');
      #if (!$this->oSr->sr_client($aPrice, $this->aView)) exit('sr_client');
      #if (!$this->oPivots->pivots_view_client($aPrice, $this->aView, false)) exit('pivots_view_client');
      #maa      
      #if (!$this->oExpo->expo_view_client($aPrice, $this->aView, false)) exit('expo_view_client');

#var_dump(array($k__, $aPrice));
#if ($i > 60) break;

      # Beggs
      if (!$this->set_chart_prices($aPrice, $this->aView)) exit('set_chart_prices');
      $i++;
    }

/*
2020-11-24T21:56:29.463010261Z
2020-11-24T22:01:37.085177038Z
2020-11-24T22:07:00.005980767Z
2020-11-24T22:11:18.485660266Z

*/

echo '</pre>';

    # debugs
    $_debug = array(
      #$_cache,
      $this->convert(memory_get_usage(true)), 
      microtime(true) - $_start
    );
    foreach ($_debug as $i => $v) print "[$i] $v  //  ";
    #

    return print $this->oTwig->render('index.html.twig', array(
      'prices' => $this->aaView,
      'pairs' => $this->oSettings->aPairs,
      #name
      'name' => $this->sPair .' - '. $this->oSettings->aPairs[$sp__]['iTimeChart'] .' min '. $this->oPrices->sFrom .' - '. $this->oPrices->sTo,
      #playground
      'playground' => 1,
      # chart_line
      'ask_price' => 0,
      'bid_price' => 0,
      #candlesticks
      'chart_sticks' => 1,
      #swings
      'chart_swings' => 1,
      'chart_hh_ll' => 1,
      #pivots
      'chart_pivots_standard' => 0,
      'chart_pivots_fibonacci' => 0,
      'chart_pivots_demark' => 0,
      'chart_fans' => 0, # only on buy sell points
      #expo_lines 
      'expo_lines' => 0, # buggy
      #note,
      'chart_note' => 1,
      #trends,
      'chart_trends' => 0,

      // #trader
      // 'chart_trader' => 1,
      // #note,
      // 'chart_market' => 0,
    ));

  }

	public function prior_stickske()
	{
    if (!$this->oPriors->prior_sticks_client($this->aaView, $this->aView)) exit('prior_sticks_client');
    $this->aaView = $this->oPriors->aaView;
    return true;
  }

	private function setup_view()
	{
    $sp__ = $this->sPair;
    $this->aaView = $this->oPrices->aPrices[$sp__]; # $this->get_prices(true)[$this->sPair];
    if (empty($this->aaView)) exit('get_prices');

    if (!$this->set_prices_scale()) exit('set_prices_scale');
    if (!$this->set_ratio()) exit('set_ratio');

    $this->aView = array(
      'sPair' => $this->sPair,
      'iScale' => $this->iScale,
      'fMinPrice' => $this->fMinPrice,
      'fRatio' => $this->fRatio,
      'aPair' => $this->oSettings->aPairs[$sp__],
      'iTimeChart' => $this->oSettings->aPairs[$sp__]['iTimeChart']
    );

    if (!$this->prior_stickske()) exit('prior_stickske');
    
    return true;
  }

  public function set_chart_prices(&$aPrices, $aView = false)
  {
    if (isset($aView)) foreach ($aView as $k => $a) $this->{$k} = $a;
    # candlesticks
    if (isset($aPrices['open']))
    {
      $aPrices['chart_open'] = $this->set_ratio($aPrices['open']);
      $aPrices['chart_high'] = $this->set_ratio($aPrices['high']);
      $aPrices['chart_low'] = $this->set_ratio($aPrices['low']);
      $aPrices['chart_close'] = $this->set_ratio($aPrices['close']);  
    }



    # trends
    // if (isset($aPrices['trends']))
    // {
    //   foreach ($aPrices['trends'] as $k__ => &$bTrends)
    //   {
    //   }
    // }

    # trend_lines
    for ($i = 0; $i <= 7; $i++)
    {
      if (isset($aPrices['trend_line_'. $i])) 
      {
        $aPrices['trend_line_'. $i][0] = $this->set_ratio($aPrices['trend_line_'. $i][0]);
      }
    }

    # expo
    if (isset($aPrices['expo']))
    {
      foreach ($aPrices['expo'] as &$aFunction)
      {
        if ($aFunction[1] == 0) { $aFunction[1] = 0; continue; }

        $aPrices[$aFunction[0]] = $this->set_ratio($aFunction[1]);
        $aFunction[1] = $this->set_ratio($aFunction[1]);
      }
    } 

    # pivots
    if (isset($aPrices['pivots']))
    {
      foreach ($aPrices['pivots'] as &$aPivot)
      {

        foreach ($aPivot as &$fValue)
        {

          $fValue = $this->set_ratio($fValue);
        }
      }
    }

    # ask/bids
    if (isset($aPrices['closeoutAsk']))
    {
      $aPrices['chart_closeoutAsk'] = $this->set_ratio($aPrices['closeoutAsk']);
    }
    if (isset($aPrices['closeoutBid']))
    {
      $aPrices['chart_closeoutBid'] = $this->set_ratio($aPrices['closeoutBid']);
    }

    # notes
    if (!empty($aPrices['note']))
    {
      $aPrices['note'] = json_encode($aPrices['note']);
#var_dump($aPrices['note']);
#exit();
    }


    # swing_high/swing_low
    if (!empty($aPrices['swingl_prew']['y']))
    {
      $aPrices['swingl_prew']['y'] = $this->set_ratio($aPrices['swingl_prew']['y']);
    }
    if (!empty($aPrices['swingl_next']['y']))
    {
      $aPrices['swingl_next']['y'] = $this->set_ratio($aPrices['swingl_next']['y']);
    }

    # swing_high/swing_low
    if (!empty($aPrices['swings_high']))
    {
      $aPrices['swings_high'] = $this->set_ratio($aPrices['swings_high']);
    }
    if (!empty($aPrices['swings_low']))
    {
      $aPrices['swings_low'] = $this->set_ratio($aPrices['swings_low']);
    }
    
    
    # swing_higher_high/swing_lower_low
    if (!empty($aPrices['swing_higher_high']))
    {
      $aPrices['swing_higher_high'] = $this->set_ratio($aPrices['swing_higher_high']);
    }
    if (!empty($aPrices['swing_lower_low']))
    {
      $aPrices['swing_lower_low'] = $this->set_ratio($aPrices['swing_lower_low']);
    }

    # swing_high/swing_low
    if (isset($aPrices['swing_high']['y']['high']))
    {
      $aPrices['swing_high']['y']['high'] = $this->set_ratio($aPrices['swing_high']['y']['high']);
    }

    # swing_high/swing_low
    if (isset($aPrices['swing_low']['y']['low']))
    {
      $aPrices['swing_low']['y']['low'] = $this->set_ratio($aPrices['swing_low']['y']['low']);
    }

    if (isset($aPrices['chart_draw_swing_buy']))
    {
      if (!empty($aPrices['chart_draw_swing_buy']['x']))
      {
        $aPrices['chart_draw_swing_buy']['x'] = (intval($aPrices['swing_low']['x'] + 1)) + 2000 * cos(deg2rad(180 - 50));
      }
      if (!empty($aPrices['chart_draw_swing_buy']['y']))
      {
        $aPrices['chart_draw_swing_buy']['y'] = $this->set_ratio($aPrices['chart_draw_swing_buy']['y']) + 2000 * sin(deg2rad(180 - 50));
      }
    }

    if (isset($aPrices['chart_draw_swing_sell']))
    {
      if (!empty($aPrices['chart_draw_swing_sell']['x']))
      {
        $aPrices['chart_draw_swing_sell']['x'] = (intval($aPrices['swing_low']['x']) + 1) + 2000 * cos(deg2rad(180 + 50));
      }
      if (!empty($aPrices['chart_draw_swing_sell']['y']))
      {
        $aPrices['chart_draw_swing_sell']['y'] = $this->set_ratio($aPrices['chart_draw_swing_sell']['y']) + 2000 * sin(deg2rad(180 + 50));
      }
    }

    return true;
  }


  private function set_ratio($sPrice = false)
  {
    bcscale($this->iScale);
    $sPrice = floatval(number_format($sPrice, $this->iScale, '.', ''));
    if ($sPrice === 0) return 1;

    if (!empty($sPrice)) {
      return bcmul(bcsub($sPrice, $this->fMinPrice, $this->iScale), $this->fRatio, $this->iScale);
    }

    $aaPrices = $this->aaView;
    $aColumns = array_map(function($k) use ($aaPrices) {					
      return array_column($aaPrices, $k);
    }, array('closeoutAsk','closeoutBid'));

    list($closeoutAsk, $closeoutBid) = $aColumns;
    $maxPrice = max($closeoutAsk);
    $minPrice = min($closeoutBid);
    
    $pSmallC = -1000;
    $pLargeD = 1000;

    $difPrice = bcsub($maxPrice, $minPrice);
    $difChart = bcsub($pLargeD, $pSmallC);
    #if ($difPrice == 0) $difPrice = $minPrice;
    $fRation = bcdiv($difChart, $difPrice);
    $this->fRatio = ($fRation != 0 ? $fRation: 0.2);
    $this->fMinPrice = $minPrice;
    bcscale(0); # reset
    return 1;
  }

  private function set_prices_scale()
  {
    if (empty($this->aaView)) return false; 
    $i = 0;
    foreach ($this->aaView as $aPrice)
    {
      $fPriceAsk = explode(".", $aPrice['closeoutAsk']);
      $fPriceBid = explode(".", $aPrice['closeoutBid']);
  
      if (count($fPriceAsk) > 1)
      {
        $iScaleAsk = strlen($fPriceAsk[1]); #zeros
        $iScaleBid = strlen($fPriceBid[1]); #zeros
      }
      else {
        $iScaleAsk = 0;
        $iScaleBid = 0;
      }

      $this->iScale = max(array($iScaleAsk, $iScaleBid));
      if ($i++ == 5) break;
    }

    return true;
  }

  public function convert($size)
  {
    // https://www.php.net/manual/en/function.memory-get-usage.php
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
  }

  public function __destruct()
  {
    #session_destroy();
    #$this->db->close();
  }
}


?>