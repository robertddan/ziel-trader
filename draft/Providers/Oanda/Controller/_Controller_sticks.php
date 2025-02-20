<?php

namespace App\Suiteziel\Providers\Oanda\Controller;


use App\Suiteziel\Framework\Controller;

class Controller_sticks extends Controller
{

  #class
  public $sPair;
  public $aFunctions;
  public $aiTimeSave = array();
  public $abTimeSave = array();
  public $aaMids = array();
  public $aaSticks = array();
  public $iElementsNo = 1;
  #chart
  #public $iTimeChart = 60; # 1 5 15 30 60 
  # view note
  public $aaNotes = array();
  public $aitmpTimeSave = array();


	public function __construct()
	{
    #variales
    $this->variable_start_reset();
    #$this->variable_set_sticks_functions();
  }


  public function sticks_stream_client(&$aPrice, $aStream, $bAppendPatterns = true)
  {
    #variales
    foreach ($aStream as $k => $a) $this->{$k} = $a;
    $this->iTimeChart = $this->aPair['iTimeChart'];

    if(!$this->variable_set_scale($aPrice)) exit('variable_set_scale');
    #functions
    if(!$this->set_times_index($aPrice, true)) exit('set_times_index');
    if(!$this->set_chart_prices($aPrice, true)) exit('set_chart_prices');
    if(!$this->set_sticks_functions($aPrice, true, $bAppendPatterns)) exit('set_sticks_functions');

    return true;
  }


  public function sticks_view_client(&$aPrice, $aView, $bAppendPatterns = false)
  {
    #variales
    foreach ($aView as $k => $a) $this->{$k} = $a;
    #$this->iTimeChart = $this->aPair['iTimeChart'];

    if(!$this->variable_set_scale($aPrice)) exit('variable_set_scale');
    #functions
    if(!$this->set_times_index($aPrice, false)) exit('set_times_index');
    if(!$this->set_chart_prices($aPrice, false)) exit('set_chart_prices');
    #if(!$this->set_sticks_functions($aPrice, false, $bAppendPatterns)) exit('set_sticks_functions');

    return true;
  }
/**  */


  protected function set_times_index(&$aPrices, $bStream)
  {
    $sp__ = $this->sPair;
    if (!isset($this->aiTimeSave[$sp__])) $this->aiTimeSave[$sp__] = 0;
    if (!isset($this->abTimeSave[$sp__])) $this->abTimeSave[$sp__] = 0;
    if (!isset($this->aaSticks)) {$aPrices['note'] = array('reset' => true); $this->aaSticks = array();} # assume the note is empty
    if (!isset($this->aaMids[$sp__])) $this->aaMids[$sp__] = array();

    $aSplitTime = explode(".", str_replace('Z', '', $aPrices['time']));
    $iMicroSeconds = strtotime(str_replace('T', ' ', $aSplitTime[0]) .' '. substr($aSplitTime[1], 0, 3) .' Milliseconds ') . substr($aSplitTime[1], 0, 3) . substr($aSplitTime[1], 3, 9);

    if ($this->aiTimeSave[$sp__] === 0) 
    {
      $this->aiTimeSave[$sp__] = $iMicroSeconds;
      if (!isset($this->aaMids[$sp__][$this->aiTimeSave[$sp__]])) $this->aaMids[$sp__][$this->aiTimeSave[$sp__]] = array();
    }

    $aPrices = array_merge(
      $aPrices,
      array(
        'open' => 0,
        'high' => 0,
        'low' => 0,
        'close' => 0,
        'asks' => $aPrices['closeoutAsk'],
        'bids' => $aPrices['closeoutBid'],
        'mids' => bcdiv(bcadd($aPrices['closeoutAsk'], $aPrices['closeoutBid'], $this->iScale), 2, $this->iScale),
        'key' => $this->aiTimeSave[$sp__]
      )
    );

    array_unshift(
      $this->aaMids[$sp__][$this->aiTimeSave[$sp__]], 
      $aPrices
    );

    $iForwardTime = bcadd($this->aiTimeSave[$sp__], bcmul($this->iTimeChart, 60000000000, 0), 0); # bcmul($this->iTimeChart, 60)); #

    if (bccomp($iForwardTime, $iMicroSeconds, 0) <= 0)
    {
      $this->aiTimeSave[$sp__] = 0;
      $this->abTimeSave[$sp__] =  $this->abTimeSave[$sp__] + 1;
    }

    return true;
  }


  protected function set_chart_prices(&$aPrice, $bStream)
  {
    # to reduce loading time !important
    # aaMids is full array when enters loop, or halt empty?

    $sp__ = $this->sPair;
    if ($this->abTimeSave[$sp__] <= 0) return true; # pass not, untill the next loop/time
    if (!isset($this->aaSticks[$sp__])) $this->aaSticks[$sp__] = $aMohlc = array();

    $k___ = array_key_last($this->aaMids[$sp__]);
    #$k___ = array_key_first($this->aaMids[$sp__]); # index by append

    foreach($this->aaMids[$sp__] as $k__ => $aaPrices)
    {
      if ($k___ !== $k__) continue;
      $aaSessionAsks = array_column($aaPrices, 'asks');
      $aaSessionBids = array_column($aaPrices, 'bids');
      $aaSessionMids = array_column($aaPrices, 'mids');
      $aMohlc['open'] = end($aaSessionMids); # ???
      $aMohlc['high'] = max($aaSessionAsks);
      $aMohlc['low'] = min($aaSessionBids);
      $aMohlc['close'] = reset($aaSessionMids);
      $aMohlc['sticks'] = array();
    }

    array_shift($this->aaMids[$sp__]);
    if (count($this->aaSticks[$sp__]) > $this->iElementsNo) array_shift($this->aaSticks[$sp__]);
    
    $aPrice = array_merge(
      $aPrice,
      $aMohlc
    );

    array_push($this->aaSticks[$sp__], $aPrice);

    $this->abTimeSave[$sp__] = 0;
    return true;

  }


  protected function set_sticks_functions(&$aPrice, $bStream, $bAppendPatterns)
  {
    $sp__ = $this->sPair;
    if ($this->abTimeSave[$sp__] <= 0) return true;

    if ($bAppendPatterns) 
    {
      $iLastKey = array_key_last($this->aaSticks[$sp__]); #
      $aPrice = array_merge(
        $aPrice,
        $this->aaSticks[$sp__][$iLastKey]
      );
      $this->abTimeSave[$sp__] = 0;
      return true;
    }

    $aTraderArguments = $this->aaSticks[$sp__];
    $aMohlc = array_map(function($k__) use ($aTraderArguments) {
      return array_column($aTraderArguments, $k__);
    }, array('open','high','low','close'));
    if (empty($aMohlc)) return true; #
    $iLastKey = array_key_last($this->aaSticks[$sp__]); #

    #$this->set_trader_functions($aMohlc, $iLastKey);

    $aPrice = array_merge(
      $aPrice,
      $this->aaSticks[$sp__][$iLastKey]
    );

    $this->abTimeSave[$sp__] = 0;
    return true;
  }



  protected function set_trader_functions($aMohlc, $iLastKey)
  {
    $sp__ = $this->sPair;

    foreach ($this->aFunctions as $sFunction)
    {
      #$oReflection = new ReflectionFunction($sFunction);
      
      $aParameters = array(
        $aMohlc[0], 
        $aMohlc[1], 
        $aMohlc[2], 
        $aMohlc[3]
      );
#      if ($oReflection->getNumberOfParameters() == 5)
#      {
#        $aParameters = array_merge(
#          $aParameters,
#          array(0)
#        );
#      }

      #if (!function_exists($sFunction)) continue;
#var_dump($sFunction);
      $aResponse = call_user_func_array($sFunction, $aParameters);

      if (empty($aResponse)) continue;
      $aResponse = array_filter($aResponse);

      foreach ($aResponse as $i => $fValue)
      {
        if ($fValue == 0) continue;

        if ($i !== $iLastKey) continue;

        array_unshift($this->aaSticks[$sp__][$i]['sticks'], 
          array(str_replace('trader_cdl', '', $sFunction), intval($fValue))
        );
      }

    }


#var_dump(json_encode([5, $sp__, $this->aaSticks[$sp__][$iLastKey]['sticks']]));

    return true;
  }
  
/** Framework  */

  public function variable_set_scale($aPrices)
  {
    if (!isset($this->iScale)) exit('variable_set_scale');
    bcscale($this->iScale);

    // if (!isset($this->iScale))
    // {
    //   $sp__ = $this->sPair;
    //   $aPrices = (array) $aPrices;
    //   $fPriceAsk = explode(".", $aPrices['closeoutAsk']);
    //   $fPriceBid = explode(".", $aPrices['closeoutBid']);

    //   if (count($fPriceAsk) > 1) $iScaleAsk = strlen($fPriceAsk[1]); #zeros
    //   else $iScaleAsk = 0; #zeros
    //   if (count($fPriceBid) > 1) $iScaleBid = strlen($fPriceBid[1]); #zeros
    //   else $iScaleBid = 0; #zeros
    //   $this->iScale = max(array($iScaleAsk, $iScaleBid));
    // }
    // bcscale($this->iScale);
    
    return true;
  }
/** Framework  */

  public function variable_start_reset()
  {

  }

  public function variable_set_sticks_functions()
  {
    if (isset($this->aFunctions)) return true;
    $this->aFunctions = array(
      'trader_cdl3blackcrows',
      'trader_cdl2crows',
      'trader_cdl3inside',
      'trader_cdl3linestrike',
      'trader_cdl3outside',
      'trader_cdl3starsinsouth',
      'trader_cdl3whitesoldiers',
      'trader_cdlabandonedbaby',
      'trader_cdladvanceblock',
      'trader_cdlbelthold',
      'trader_cdlbreakaway',
      'trader_cdlclosingmarubozu',
      'trader_cdlconcealbabyswall',
      'trader_cdlcounterattack',
      'trader_cdldarkcloudcover',
      'trader_cdldoji',
      'trader_cdldojistar',
      'trader_cdldragonflydoji',
      'trader_cdlengulfing',
      'trader_cdleveningdojistar',
      'trader_cdleveningstar',
      'trader_cdlgapsidesidewhite',
      'trader_cdlgravestonedoji',
      'trader_cdlhammer',
      'trader_cdlhangingman',
      'trader_cdlharami',
      'trader_cdlharamicross',
      'trader_cdlhighwave',
      'trader_cdlhikkake',
      'trader_cdlhikkakemod',
      'trader_cdlhomingpigeon',
      'trader_cdlidentical3crows',
      'trader_cdlinneck',
      'trader_cdlinvertedhammer',
      'trader_cdlkicking',
      'trader_cdlkickingbylength',
      'trader_cdlladderbottom',
      'trader_cdllongleggeddoji',
      'trader_cdllongline',
      'trader_cdlmarubozu',
      'trader_cdlmatchinglow',
      'trader_cdlmathold',
      'trader_cdlmorningdojistar',
      'trader_cdlmorningstar',
      'trader_cdlonneck',
      'trader_cdlpiercing',
      'trader_cdlrickshawman',
      'trader_cdlrisefall3methods',
      'trader_cdlseparatinglines',
      'trader_cdlshootingstar',
      'trader_cdlshortline',
      'trader_cdlspinningtop',
      'trader_cdlstalledpattern',
      'trader_cdlsticksandwich',
      'trader_cdltakuri',
      'trader_cdltasukigap',
      'trader_cdlthrusting',
      'trader_cdltristar',
      'trader_cdlunique3river',
      'trader_cdlupsidegap2crows',
      'trader_cdlxsidegap3methods');
      return true;
  }


  function __destruct()
  {
    #ob_end_flush();
  }

/*

DateTimeA date and time value using either RFC3339 or UNIX time representation.

Type 	string
Format 	The RFC 3339 representation is a string conforming to https://tools.ietf.org/rfc/rfc3339.txt. The Unix representation is a string representing the number of seconds since the Unix Epoch (January 1st, 1970 at UTC). The value is a fractional number, where the fractional part represents a fraction of a second (up to nine decimal places).
 * set_class_variables :bcdiv are 2 decimale. Pt ce?
 * set_analysis :pe sesiune set_sticks_red_first_loop sare peste un pret. De ce?+++ pt a putea calcula cu prev

 
*/
}

?>