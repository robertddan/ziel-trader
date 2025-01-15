<?php

namespace App\Suiteziel\Providers\Oanda\Controller;

use App\Suiteziel\Framework\Controller;


class Controller_sticks extends Controller
{

  #class
  public $sPair;
  public $aFunctions;
  public $aiTimeKey = array();
  public $abTimeSave = array();
  public $aaMids = array();
  public $aaSticks = array();
  public $iElementsNo = 2; # > 1

  public $iTimer = array();
  public $bFocused = false;
  public $aTimeSince = array();
  #chart
  public $iTimeChart = 60; # 1 5 15 30 60 
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
    if(!$this->set_times_index($aPrice, true, true)) exit('set_times_index');
    if(!$this->set_chart_prices($aPrice, true)) exit('set_chart_prices');
    if(!$this->set_sticks_functions($aPrice, true, $bAppendPatterns)) exit('set_sticks_functions');

    return true;
  }


  public function sticks_view_client(&$aPrice, $aView, $bAppendPatterns = false, $ik__ = 0)
  {


    #variales
    foreach ($aView as $k => $a) $this->{$k} = $a;
    $this->iTimeChart = $this->aPair['iTimeChart'];
    if(!$this->variable_set_scale($aPrice)) exit('variable_set_scale');
    #functions
    if(!$this->set_times_index($aPrice, false, $ik__)) return true; #exit('set_times_index');

    if(!$this->set_chart_prices($aPrice, false)) exit('set_chart_prices');
    #if(!$this->set_sticks_functions($aPrice, false, $bAppendPatterns)) exit('set_sticks_functions');




    
    return true;
  }
/**  */


  protected function set_times_index(&$aPrices, $bStream, $ik__ )
  {

/*
skip first candles  start from fix point

CACHE FIRST TIME IN THE MORNING FIRST

fix the gaps where there is no prices in the time frame 1/3/5/10... minutes
set last close price
*/

    $sp__ = $this->sPair;
    if (!isset($this->iTimer[$sp__])) $this->iTimer[$sp__] = 0;
    if (!isset($this->abTimeSave[$sp__])) $this->abTimeSave[$sp__] = 0;
    if (!isset($this->aTimeSince[$sp__])) $this->aTimeSince[$sp__] = 0;
    if (!isset($this->aaSticks[$sp__])) $this->aaSticks[$sp__] = array(); 
    if (!isset($this->aaMids[$sp__])) $this->aaMids[$sp__] = array();
    if (!isset($this->aiLastKey[$sp__])) $this->aiLastKey[$sp__] = 0;
    if (!isset($this->aiLastTime[$sp__])) $this->aiLastTime[$sp__] = 0;

    $aTimeSplit = explode(".", str_replace('Z', '', $aPrices['time']));
    $iTimeDate = strtotime(str_replace('T', ' ', $aTimeSplit[0]));
/*
var_dump(array(
  $aTimeSplit,
  str_replace('T', ' ', $aTimeSplit[0]),
  $iTimeDate
));
exit();

8Z"] # buy # sell 
["2020-11-20 15:05:00","2020-11-20T15:05:00.240855581Z"] # buy # sell 
["2020-11-20 15:10:00","2020-11-20T16:24:29.378963236Z"] # buy # sell 
["2020-11-20 15:15:00","2020-11-20T16:24:29.378963236Z"] # buy # sell 
["2020-11-20 15:20:00","2020-11-20T16:24:29.378963236Z"
*/

    # skip if time <> 
    #if (bccomp($iTimeDate, strtotime(date('2020-11-20 14:50:00'))
    #) < 0) return false; # TODO checkout 
    #if (bccomp($iTimeDate, strtotime(date('2020-11-20 16:45:00'))
    #) > 0) return false; # TODO checkout 


    # if there is a gap/lag of 1/2/3 ... hours/minutes/days

    # Skip first prices not multiple of chart time: 1/3/5/10... minutes
    # To be further edited:
    # - chiar daca e deja in minutel 02, incep cheia de la minutul 00 in cazl a 05 minute candle
    # Run only once at the begining
    # Update: there can be fist 59 seconds of prices missing <- to be fixed!!!
	
/*
var_dump(array(

	'set_times_index',
	$this->bFocused,
	$iTimeDate,
	date('i', $iTimeDate), 
	$this->iTimeChart

));
*/
    if (!$this->bFocused) 
		if (($this->isMultipleof(date('i', $iTimeDate), $this->iTimeChart)) || (intval(date('i', $iTimeDate)) == 0 )) 
    {
      # for update fix: (intval(date('s', $iTimeDate)) == 0 )) -- wait
      $this->bFocused = true;
      $this->aiTimeKey[$sp__] = strtotime(date('Y-m-d H:i:00', $iTimeDate));
      if (!isset($this->aaMids[$sp__][$this->aiTimeKey[$sp__]])) $this->aaMids[$sp__][$this->aiTimeKey[$sp__]] = array();
    }
    else 
    {
      return false;
    }


/*
= divided by functions =

- is multiple of x or is minute 00 - true/false
- set the key
- ignore small prices than key
- add 60*x to key to compare with current time
- if comparativ is bigger, # compare unix-times, if difference bigger than time-chart ...
    deploy the mids array, save last-key and generate a new key
- merge and unshift price

*/


    # If prices are from the past ignore
    if (bccomp($iTimeDate, $this->aiLastTime[$sp__]) < 0) return false; # TODO checkout !!!
    $this->aiLastTime[$sp__] = $iTimeDate;


    # verify time poools and key
    # update key
    $iForwardTime = bcadd($this->aiTimeKey[$sp__], bcmul($this->iTimeChart, 60, 0), 0);
    #$iForwardTime = bcadd(strtotime(date('Y-m-d H:i:00', $iTimeDate)), bcmul($this->iTimeChart, 60, 0), 0);
    if (bccomp($iForwardTime, $iTimeDate, 0) <= 0)
    {
      $this->abTimeSave[$sp__] = 1;
      $this->aiLastKey[$sp__] = $this->aiTimeKey[$sp__];


      # ((new price - last price) > (60 * time chart)) $this->bFocused = false;
      # build the gap


      # buggy - if there is a gap there can not be an addition to the last time period
      /*
        $this->aiTimeKey[$sp__] 
      */

      $iTimeGap = bcsub(strtotime(date('Y-m-d H:i:s', $iTimeDate)), $this->aiTimeKey[$sp__], 0);

/*
var_dump([
  bcadd($this->aiTimeKey[$sp__], bcadd(bcmul($this->iTimeChart, 60, 0), 59, 0), 0),
  '$iTimeGap',
  $iTimeGap / 60,

  $iTimeGap,

  $this->aiTimeKey[$sp__], bcadd(bcmul($this->iTimeChart, 60, 0), 59, 0)
]);

["2020-11-19 23:20:00","2020-11-19T23:20:00.560191832Z"] # buy # sell 
["2020-11-19 23:25

["2020-11-20 00:25:00","2020-11-20T00:25:00.853517825Z"] # buy # sell 
["2020-11-20 00:30:00","2020-11-20T00:30:07.575515191Z"] # buy # sell 

-11-20 06:35:00","2020-11-20T06:35:01.848287620Z"] # buy # sell 
["2020-11-20 06:40:00","2020-11-20T06:40:18.450371928Z"] # buy # sell 
["2020-11-20 06:45:00","2020-11-20T06:45:04.392197619Z"] # buy # sell ["2020-1

1-20 00:55:00","2020-11-20T00:55:00.530815429Z"] # buy # sell 
["2020-11-20 01:00:00","2020-11-20T01:00:01.123387742Z"] # buy # sell 
["2020-11-20 01:05:00","2020-11-20T01:05:02.629890493Z"] # buy # sell 
["2020-11-20 01:10:00","2020-11-20T01:10:36.160576440Z"] # buy # sell 
["2020-11-20 01:15:00","2020-11-20T01:15:01.133504203Z"] # buy # sell # buy # sell 
["2020-11-2

*/

      if (bccomp(bcadd(bcmul($this->iTimeChart, 60, 0), 59, 0), $iTimeGap, 0) <= 0)
      #if (bccomp(bcadd($this->aiTimeKey[$sp__], bcadd(bcmul($this->iTimeChart, 60, 0), 59, 0), 0), $iTimeGap, 0) <= 0)
      {
        $this->bFocused = false;
        $this->aiTimeKey[$sp__] = 0;
        $this->aiLastTime[$sp__] = 0;
        return true;
      }
      else
      {
        $this->aiTimeKey[$sp__] = bcadd($this->aiTimeKey[$sp__], bcmul($this->iTimeChart, 60, 0), 0); 
      }

      #var_dump($iTimeGap);

      if (!isset($this->aaMids[$sp__][$this->aiTimeKey[$sp__]])) $this->aaMids[$sp__][$this->aiTimeKey[$sp__]] = array();
      #var_dump(count($this->aaMids[$sp__][$this->aiLastKey[$sp__]]));
    }

    
    # merge new data
    $aPrices = array_merge(
      $aPrices,
      array(
        'open' => null,
        'high' => null,
        'low' => null,
        'close' => null,
        'note' => array(date('Y-m-d H:i:s', $this->aiTimeKey[$sp__]), $aPrices['time']),
        'asks' => $aPrices['closeoutAsk'],
        'bids' => $aPrices['closeoutBid'],
        'mids' => bcdiv(bcadd($aPrices['closeoutAsk'], $aPrices['closeoutBid'], $this->iScale), 2, $this->iScale),
        'key' => $this->aiTimeKey[$sp__],
        'closeoutAsk' => $aPrices['closeoutAsk'],
        'closeoutBid' => $aPrices['closeoutBid'],
      )
    );


    # good to paralel debug prices date //
    #var_dump(array(date('Y-m-d H:i:s', $this->aiTimeKey[$sp__]), $aPrices));

    # push to main array
    array_unshift(
      $this->aaMids[$sp__][$this->aiTimeKey[$sp__]],
      $aPrices
    );

    return true;
  }

/*
  ["2020-11-20 15:15:00","2020-11-20T16:24:29.378963236Z"] # buy # sell 
  ["2020-11-20 15:20:00","2020-11-20T16:24:29.378963236Z"] # buy # sell 
  ["2020-11-20 15:25:00","2020-11-20T16:24:29.378963236Z"] # buy # sell 
  ["2020-11-20 15:30:00","2020-11-20T16:24:29.378963236Z"] # buy # sell 
  ["2020-11-20 15:35:00","2020-11-20T16:24:29.378963236Z"] # buy # sell 
  ["2020-11-20 15:40:00","2020-11-20T16:24:32.170746877Z"] # buy # sell 
  ["2020-11-20 15:45:00","2020-11-20T16:24:32.170746877Z"] # buy # sell 
  ["2020-11-20 15:50:00","2020-11-20T16:24:32.170746877Z"] # buy # sell 
  ["2020-11-20 15:55:00","2020-11-20T16:24:32.170746877Z"] # buy # sell 
  ["2020-11-20 16:00:00","2020-11-20T16:24:32.170746877Z"] # buy # sell 
  ["2020-11-20 16:05:00","2020-11-20T16:24:32.170746877Z"] # buy # sell 
  ["2020-11-20 16:10:00","2020-11-20T16:24:39.607947012Z"] # buy # sell 
  ["2020-11-20 16:15:00","2020-11-20T16:24:39.607947012Z"] # buy # sell 
  ["2020-11-20 16:20:00","2020-11-20T16:24:39.607947012Z"] # buy # sell 
  ["2020-11-20 16:25:00","2020-11-20T16:25:02.389045819Z"] # buy # sell 
*/

  protected function set_chart_prices(&$aPrice, $bStream)
  {

    # to reduce loading time !important
    # aaMids is full array when enters loop, or halt empty? :: abTimeSave[$sp__] <= 0 :: ok

    $sp__ = $this->sPair;
    if ($this->abTimeSave[$sp__] <= 0) return true; # pass not, untill the next loop/time
    if (!isset($this->aaSticks[$sp__])) $this->aaSticks[$sp__] = array();
		
    if (empty($this->aaMids[$sp__])) exit('aaMids');
    if ($this->aiLastKey[$sp__] == 0) exit('last keys');
    $aMohlc = array();
    $aaPrices = $this->aaMids[$sp__][$this->aiLastKey[$sp__]];

    $aaSessionAsks = array_column($aaPrices, 'asks');
    $aaSessionBids = array_column($aaPrices, 'bids');
    $aaSessionMids = array_column($aaPrices, 'mids');
    $aMohlc['open'] = end($aaSessionMids); # ???
    $aMohlc['high'] = max($aaSessionAsks);
    $aMohlc['low'] = min($aaSessionBids);
    $aMohlc['close'] = reset($aaSessionMids);
    $aMohlc['sticks'] = array();

    unset($this->aaMids[$sp__][$this->aiLastKey[$sp__]]);
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

  // https://www.geeksforgeeks.org/check-if-a-number-is-multiple-of-5-without-using-and-operators/
  // assumes that n is a positive integer
  function isMultipleof ($iM, $iF)
  {
    while ( $iM > 0 ) $iM = $iM - $iF;
    if ( $iM == 0 ) return true;
    return false;
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

 

 REFERENCE
https://www.geeksforgeeks.org/check-if-a-number-is-multiple-of-5-without-using-and-operators/

*/
}

?>