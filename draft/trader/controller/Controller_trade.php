<?php

namespace Ziel\Providers\Oanda\Controller;


use Ziel\Framework\Controller;

class Controller_trade extends Controller
{

  #pairs_stuff
  public $sPair;
  #trade
  public $aFunctionsSignal = array();
  public $aFunctionsConfirmation = array();
  public $aFunctionsIndecision = array();
  public $aFunctionsContinuation = array();
  public $aSignal = array();
  public $aConfirmation = array();
  #public $iUnits = 1;

  public $aTrade = array();
  #
  public $aFirstLoop = array();

	public function __construct()
	{
    #$this->variable_set_sticks_functions();
    #unset($_SESSION);
  }

  
/*
TODO

- uptrend && downtrend lines
- engulfing in up/down trend

- signal close/open on session
*/


  public function trade_stream_client(&$aPrice, $aView, $bStream = true)
  {

    foreach ($aView as $k => $a) $this->{$k} = $a;
    $this->iUnits = $this->aPair['units'];

    bcscale($this->iScale);
    if(!$this->set_local_variables()) exit('set_local_variables');


    #if(!$this->signal_confirmation($aPrice, $bStream)) exit('signal_confirmation');
    if (!$this->trade_candles($aPrice, $bStream)) exit('trade_candles');
    if (!$this->trade_pivots($aPrice, $bStream)) exit('trade_pivots');
    if (!$this->trade_market($aPrice, $bStream)) exit('trade_market');
    if (!$this->log_sticks($aPrice, $bStream)) exit('log_sticks');

    return true;
  }


  public function trade_view_client(&$aPrice, $aView, $bStream)
  {

    foreach ($aView as $k => $a) $this->{$k} = $a;
    $this->iUnits = $this->aPair['units'];

    bcscale($this->iScale);
    if(!$this->set_local_variables()) exit('set_local_variables');

    
    #if(!$this->signal_confirmation($aPrice, $bStream)) exit('signal_confirmation');
# mai trebuie o functie fara sticks pentru a compara preturile, doar preturile cu fibonacci!
    if(!$this->trade_candles($aPrice, $bStream)) exit('trade_candles'); # debug
    if (!$this->trade_pivots($aPrice, $bStream)) exit('trade_pivots');
    if (!$this->trade_market($aPrice, $bStream)) exit('trade_market');
    if (!$this->log_sticks($aPrice, $bStream)) exit('log_sticks');

    return true;
  }

  public function log_sticks(&$aPrice, $bStream = false)
  {
    $sp__ = $this->sPair;
    if (!isset($this->aaSticks)) $this->aaSticks = array();
    if (!isset($this->aaSticks[$sp__])) $this->aaSticks[$sp__] = array();

    if (count($this->aaSticks[$sp__]) >= 30) array_pop($this->aaSticks[$sp__]);
    array_unshift($this->aaSticks[$sp__], $aPrice);

    return true;
  }

  public function trade_market(&$aPrice, $bStream = false)
  {

    $sp__ = $this->sPair;

    if (!isset($this->aSignal[$sp__])) $this->aSignal[$sp__] = false;

    if ($this->aSignal[$sp__] == false) return true;

    # to add the market_order at the beggining of the next candle not at the end
    if (isset($aPrice['sticks'])) # because not the first candle after chart_open one/ on view
    {
      print_r('######');
      $aPrice['market_order'] = $_SESSION[$sp__]['market_orders'] = $this->aTrade[$sp__]['single']; 

      $this->aSignal[$sp__] = false;
    }


    return true;
  }
  
  public function trade_pivots(&$aPrice, $bStream = false)
  {
    $sp__ = $this->sPair;

    if (!isset($aPrice['high'])) return true;
    if (!isset($aPrice['low'])) return true;

    if (!isset($aPrice['sticks'])) return true;

    if (!isset($aPrice['pivots'])) return true;
    if (!isset($aPrice['pivots']['retracement'])) return true;
    if ($aPrice['pivots']['retracement']['__p'] == 0) return true;

    #if (!isset($_SESSION[$sp__]['log']['volatility']['high'])) $_SESSION[$sp__]['log']['volatility']['high'] = bcadd($aPrice['high'], bcdiv(bcsub($aPrice['high'], $aPrice['low']), 2));
    #if (!isset($_SESSION[$sp__]['log']['volatility']['low'])) $_SESSION[$sp__]['log']['volatility']['low'] = bcsub($aPrice['low'], bcdiv(bcsub($aPrice['high'], $aPrice['low']), 2));
    #if (!isset($_SESSION[$sp__]['log']['volatility']['true'])) $_SESSION[$sp__]['log']['volatility']['true'] = 0;

    #if (
    #  bccomp($_SESSION[$sp__]['log']['volatility']['low'], $aPrice['high']) > 0
    #  ||
    #  bccomp($_SESSION[$sp__]['log']['volatility']['high'], $aPrice['low']) < 0
    #) $_SESSION[$sp__]['log']['volatility']['true'] = 1;

    
    if (!isset($_SESSION[$sp__]['log']['pivots']['__p'])) $_SESSION[$sp__]['log']['pivots']['__p'] = 0;
    if (!isset($_SESSION[$sp__]['log']['market'])) $_SESSION[$sp__]['log']['market'] = array();
    
    
    if (bcscale() == 1) $fBabyPips = $this->aPair['aBbyPip'][1]; # 0.2
    elseif (bcscale() == 2) $fBabyPips = $this->aPair['aBbyPip'][0]; # .20
    else $fBabyPips = floatval('0.'. str_pad($this->aPair['aBbyPip'][2], bcscale(), 0, STR_PAD_LEFT)); # 20


# reset lp log is touch p
if (
  $_SESSION[$sp__]['log']['pivots']['__p'] != 0
  && $aPrice['close'] < $aPrice['pivots']['retracement']['__r0']
  && $aPrice['close'] > $aPrice['pivots']['retracement']['__s0']
)
{
  $_SESSION[$sp__]['log']['pivots']['__p'] = 0;
}
elseif (
  $_SESSION[$sp__]['log']['pivots']['__p'] != 0
  && $aPrice['low'] < $aPrice['pivots']['retracement']['__r1']
  && $aPrice['low'] > $aPrice['pivots']['retracement']['__r0']
)
{
  $_SESSION[$sp__]['log']['pivots']['__p'] = 0;
}
elseif (
  $_SESSION[$sp__]['log']['pivots']['__p'] != 0
  && $aPrice['high'] < $aPrice['pivots']['retracement']['__s0']
  && $aPrice['high'] > $aPrice['pivots']['retracement']['__s1']
)
{
  $_SESSION[$sp__]['log']['pivots']['__p'] = 0;
}


# tp buy
if ( 
  $this->aTrade[$sp__]['single']['buy'] == true
  && isset($this->aTrade[$sp__]['single']['aPrice']['high'])
)
{
  if (
    $aPrice['low'] - $fBabyPips > $this->aTrade[$sp__]['single']['aPrice']['high']
  )
  {
    $this->aTrade[$sp__]['single']['aPrice'] = $_SESSION[$sp__]['market_orders']['aPrice'] = $aPrice;
    #$aPrice['tp'] = true;
    $_SESSION[$sp__]['log']['pivots']['__p'] = $aPrice['pivots']['retracement']['__p'];
    #return true;
  }
}
# sl buy
if (
  $this->aTrade[$sp__]['single']['buy'] == true
  && isset($this->aTrade[$sp__]['single']['aPrice']['low'])
)
{
  if (
    bccomp(
      $aPrice['low'] + $fBabyPips, 
      $this->aTrade[$sp__]['single']['aPrice']['low']# - $fBabyPips
    ) < 0
  )
  {
    $this->aTrade[$sp__]['single']['units'] = 0;
    $this->aTrade[$sp__]['single']['buy'] = false; 
    $this->aTrade[$sp__]['single']['sell'] = false;
    $this->aTrade[$sp__]['single']['aPrice'] = $aPrice;
    $this->aTrade[$sp__]['single']['close'] = true;
    $this->aTrade[$sp__]['single']['close_param'] = array('longUnits' => 'ALL');
    $this->aSignal[$sp__] = true;
    $_SESSION[$sp__]['log']['pivots']['__p'] = $aPrice['pivots']['retracement']['__p'];
    return true;
  }
}


# tp sell
if (
  $this->aTrade[$sp__]['single']['sell'] == true
  && isset($this->aTrade[$sp__]['single']['aPrice']['low'])
)
{
  if (
    $aPrice['high'] - $fBabyPips < $this->aTrade[$sp__]['single']['aPrice']['low']
  )
  {
    $this->aTrade[$sp__]['single']['aPrice'] = $_SESSION[$sp__]['market_orders']['aPrice'] = $aPrice;
    #$aPrice['tp'] = true;
    $_SESSION[$sp__]['log']['pivots']['__p'] = $aPrice['pivots']['retracement']['__p'];
    #return true;
  }
}
# sl sell
if (
  $this->aTrade[$sp__]['single']['sell'] == true
  && isset($this->aTrade[$sp__]['single']['aPrice']['high'])
)
{
  if (
    bccomp(
      $aPrice['high'] - $fBabyPips,
      $this->aTrade[$sp__]['single']['aPrice']['high']# + $fBabyPips
    ) > 0
  )
  {
    $this->aTrade[$sp__]['single']['units'] = 0;
    $this->aTrade[$sp__]['single']['buy'] = false; 
    $this->aTrade[$sp__]['single']['sell'] = false;
    $this->aTrade[$sp__]['single']['aPrice'] = $aPrice;
    $this->aTrade[$sp__]['single']['close'] = true;
    $this->aTrade[$sp__]['single']['close_param'] = array('shortUnits' => 'ALL');
    $this->aSignal[$sp__] = true;
    $_SESSION[$sp__]['log']['pivots']['__p'] = $aPrice['pivots']['retracement']['__p'];
    return true;
  }
}

#####
#if ($_SESSION[$sp__]['log']['volatility']['true'] !== 1) return true;
#else print_r(777); 
######

/*
if ($bStream )
var_dump([
[
  $this->aTrade[$sp__]['single']['sell'] !== true,
  $aPrice['close'], $aPrice['pivots']['retracement']['__s0'],
  bccomp($aPrice['close'], $aPrice['pivots']['retracement']['__s0']) < 0,
  (
    $_SESSION[$sp__]['log']['pivots']['__p'] == 0
    || $_SESSION[$sp__]['log']['pivots']['__p'] !== $aPrice['pivots']['retracement']['__p']
  )
],
[
  $this->aTrade[$sp__]['single']['buy'] !== true,
  $aPrice['close'], $aPrice['pivots']['retracement']['__r1'],
  bccomp($aPrice['close'], $aPrice['pivots']['retracement']['__r1']) > 0,
  (
    $_SESSION[$sp__]['log']['pivots']['__p'] == 0
    || $_SESSION[$sp__]['log']['pivots']['__p'] !== $aPrice['pivots']['retracement']['__p']
  )
  ],
[
  $_SESSION[$sp__]['log']['pivots']['__p'] == 0,
  $_SESSION[$sp__]['log']['pivots']['__p'],
  $aPrice['pivots']['retracement']['__p'],
  $aPrice['pivots']['retracement'],
  $aPrice['pivots'],
  $sp__
]
]);
*/

# buy
if (
  bccomp($aPrice['close'], $aPrice['pivots']['retracement']['__r2']) > 0 
  && $this->aTrade[$sp__]['single']['buy'] !== true
  #&& (
  #  $_SESSION[$sp__]['log']['pivots']['__p'] == 0
  #  || $_SESSION[$sp__]['log']['pivots']['__p'] !== $aPrice['pivots']['retracement']['__p']
  #)
)
{
  if ($this->aTrade[$sp__]['single']['sell']) 
  { 
    $this->aTrade[$sp__]['single']['units'] = (+$this->iUnits);
  }
  $this->aTrade[$sp__]['single']['units'] = ($this->aTrade[$sp__]['single']['units'] + $this->iUnits);
  $this->aTrade[$sp__]['single']['buy'] = true; 
  $this->aTrade[$sp__]['single']['sell'] = false;
  $this->aTrade[$sp__]['single']['aPrice'] = $aPrice;
  $this->aTrade[$sp__]['single']['close'] = false;
  $this->aTrade[$sp__]['single']['close_param'] = null;
  $this->aSignal[$sp__] = true;

  $_SESSION[$sp__]['log']['pivots']['__p'] = $aPrice['pivots']['retracement']['__p'];
  $_SESSION[$sp__]['log']['market']['buy'] = $aPrice['pivots']['retracement']['__r0'];

if (!isset($aPrice['chart_draw_swing_buy'])) $aPrice['chart_draw_swing_buy'] = array();
if (!isset($aPrice['draw_swing_buy'])) $aPrice['draw_swing_buy'] = array();

$aPrice['chart_draw_swing_buy']['x'] = $aPrice['swing_low']['x'] + 1;
$aPrice['chart_draw_swing_buy']['y'] = $aPrice['swing_low']['y']['low'];

$aPrice['draw_swing_buy']['x'] = ($aPrice['swing_low']['x'] + 1) + 2000 * cos(deg2rad(180 - 50));
$aPrice['draw_swing_buy']['y'] = $aPrice['swing_low']['y']['low'] + 2000 * sin(deg2rad(180 - 50));

return true;

}


# sell
if (
  bccomp($aPrice['close'], $aPrice['pivots']['retracement']['__s2']) < 0
  && $this->aTrade[$sp__]['single']['sell'] !== true
  && (
    $_SESSION[$sp__]['log']['pivots']['__p'] == 0
    || $_SESSION[$sp__]['log']['pivots']['__p'] !== $aPrice['pivots']['retracement']['__p']
  )
)
{
  if ($this->aTrade[$sp__]['single']['buy']) 
  {
    $this->aTrade[$sp__]['single']['units'] = (-$this->iUnits);
  }
  $this->aTrade[$sp__]['single']['units'] = ($this->aTrade[$sp__]['single']['units'] - $this->iUnits);
  $this->aTrade[$sp__]['single']['buy'] = false;
  $this->aTrade[$sp__]['single']['sell'] = true;
  $this->aTrade[$sp__]['single']['aPrice'] = $aPrice;
  $this->aTrade[$sp__]['single']['close'] = false;
  $this->aTrade[$sp__]['single']['close_param'] = null;
  $this->aSignal[$sp__] = true;

  $_SESSION[$sp__]['log']['pivots']['__p'] = $aPrice['pivots']['retracement']['__p'];
  $_SESSION[$sp__]['log']['market']['sell'] = $aPrice['pivots']['retracement']['__s0'];

if (!isset($aPrice['chart_draw_swing_sell'])) $aPrice['chart_draw_swing_sell'] = array();
if (!isset($aPrice['draw_swing_sell'])) $aPrice['draw_swing_sell'] = array();

$aPrice['chart_draw_swing_sell']['x'] = $aPrice['swing_high']['x'] + 1;
$aPrice['chart_draw_swing_sell']['y'] = $aPrice['swing_high']['y']['high'];

$aPrice['draw_swing_sell']['x'] = ($aPrice['swing_high']['x'] + 1) + 2000 * cos(deg2rad(180 + 50));
$aPrice['draw_swing_sell']['y'] = $aPrice['swing_high']['y']['high'] + 2000 * sin(deg2rad(180 + 50));

return true;

}

    return true;
  }

## start trade_candles
  public function trade_candles(&$aPrice, $bStream = false)
  {
    $sp__ = $this->sPair;

    if (!isset($aPrice['sticks'])) return true;
    if (empty($aPrice['sticks'])) return true;
    
    foreach ($aPrice['sticks'] as $i => $aPatterns) # 0 - function, 1  -value
    {

      #if(count($aPrice['sticks']) > 1) break;
      #if ($i > 0) continue;
      #if (in_array($aPatterns[0], $this->aFunctionsIndecision)) if ($this->aTrade[$sp__]['single']['units'] == 0) continue;
      #if (in_array($aPatterns[0], $this->aFunctionsContinuation)) if ($this->aTrade[$sp__]['single']['units'] == 0) continue;
      #if (in_array($aPatterns[0], $this->aFunctionsIndecision)) break;
      #if (in_array($aPatterns[0], $this->aFunctionsContinuation)) break;
      #if ($this->aTrade[$sp__]['single']['units'] !== 0) if (!$this->signal_close($aPrice, $aPatterns[0], $iValue)) exit('signal_close');
      #if (isset($this->aSignal[$sp__])) if (!$this->signal_confirmation($aPrice, $aPatterns[0], $iValue)) exit('signal_confirmation');
      #if (in_array($aPatterns[0], $this->aFunctionsReversal)) 
      if (!$this->signal_reversal($aPrice, $aPatterns[0], $aPatterns[1], $bStream)) exit('signal_reversal');

    }

    return true;
  }

  public function signal_reversal(&$aPrice, $sPattern, $iValue, $bStream)
  {

    $sp__ = $this->sPair;
    bcscale($this->iScale);


if (
  $sPattern == 'piercing' ||
  $sPattern == 'darkcloudcover' ||
  $sPattern == '3whitesoldiers' || 
  $sPattern == '3blackcrows'
)
{
print_r($sPattern.'-');
}


    if (isset($this->aTrade[$sp__]['single']['aPrice']['sticks']))
    {

      $aSignal = array();
      foreach($this->aTrade[$sp__]['single']['aPrice']['sticks'] as $i => $aPattern)
      {
        if ($aPattern[0] == 'piercing')
        {
          $aSignal = array(true);
        }
        elseif ($aPattern[0] == 'darkcloudcover')
        {
          $aSignal = array(true);
        }
      }
      if (
        $sPattern !== 'piercing' && 
        $sPattern !== 'darkcloudcover' 
      )
      {
        if (!empty($aSignal)) return true;
      }
    }

    if ($sPattern == 'piercing')
    {

      if (bccomp($this->aaSticks[$sp__][0]['low'], $aPrice['open']) < 0)
      {
        $this->aSignal[$sp__] = false;
        return true;
      }

      #$fPrevHalfSpread = bcdiv(bcsub($this->aaSticks[$sp__][0]['open'], $this->aaSticks[$sp__][0]['close']), 2);
      #if (bccomp(bcsub($this->aaSticks[$sp__][0]['open'], $fPrevHalfSpread), $aPrice['close']) > 0) return true;
      
      if ($this->aTrade[$sp__]['single']['buy']) 
      {
        $this->aSignal[$sp__] = false;
        return true;
      }
      
      if ($this->aTrade[$sp__]['single']['sell']) 
      { 
        $this->aTrade[$sp__]['single']['units'] = (+$this->iUnits);
      }
      $this->aTrade[$sp__]['single']['units'] = $this->aTrade[$sp__]['single']['units'] + $this->iUnits;
      $this->aTrade[$sp__]['single']['buy'] = true; 
      $this->aTrade[$sp__]['single']['sell'] = false;
      $this->aTrade[$sp__]['single']['aPrice'] = $aPrice;
      $this->aTrade[$sp__]['single']['close'] = false;
      $this->aSignal[$sp__] = true;
      return true;
      #var_dump(['-----------------------------------------------------------------------',$sPattern]);
    }

    if ($sPattern == 'darkcloudcover')
    {

      if (bccomp($this->aaSticks[$sp__][0]['high'], $aPrice['open']) > 0)
      {
        $this->aSignal[$sp__] = false;
        return true;
      }

      #$fPrevHalfSpread = bcdiv(bcsub($this->aaSticks[$sp__][0]['close'], $this->aaSticks[$sp__][0]['open']), 2);
      #if (bccomp(bcsub($this->aaSticks[$sp__][0]['close'], $fPrevHalfSpread), $aPrice['close']) < 0) return true;
      
      if ($this->aTrade[$sp__]['single']['sell']) 
      {
        $this->aSignal[$sp__] = false;
        return true;
      }

      if ($this->aTrade[$sp__]['single']['buy']) 
      {
        $this->aTrade[$sp__]['single']['units'] = (-$this->iUnits);
      }
      $this->aTrade[$sp__]['single']['units'] = ($this->aTrade[$sp__]['single']['units'] - $this->iUnits);
      $this->aTrade[$sp__]['single']['buy'] = false;
      $this->aTrade[$sp__]['single']['sell'] = true;
      $this->aTrade[$sp__]['single']['aPrice'] = $aPrice;
      $this->aTrade[$sp__]['single']['close'] = false;
      $this->aTrade[$sp__]['single']['close_param'] = null;
      $this->aSignal[$sp__] = true;
      return true;
      #var_dump(['-----------------------------------------------------------------------',$sPattern]);
    }

#OK

    if (isset($this->aTrade[$sp__]['single']['aPrice']['sticks']))
    {
      $aSignal = array();
      foreach($this->aTrade[$sp__]['single']['aPrice']['sticks'] as $i => $aPattern)
      {
        if ($aPattern[0] == '3whitesoldiers')
        {
          $aSignal = array(true);
        }
        else if ($aPattern[0] == '3blackcrows')
        {
          $aSignal = array(true);
        }
      }
      if (
        $sPattern !== '3whitesoldiers' && 
        $sPattern !== '3blackcrows'
      )
      {
        if (!empty($aSignal)) return true;
      }
    }

    if ($sPattern == '3whitesoldiers')
    {
      if ($this->aTrade[$sp__]['single']['buy']) 
      {
        $this->aSignal[$sp__] = false;
        return true;
      }

      if ($this->aTrade[$sp__]['single']['sell']) 
      { 
        $this->aTrade[$sp__]['single']['units'] = (+$this->iUnits);
      }
      
      $this->aTrade[$sp__]['single']['units'] = $this->aTrade[$sp__]['single']['units'] + $this->iUnits;
      $this->aTrade[$sp__]['single']['buy'] = true; 
      $this->aTrade[$sp__]['single']['sell'] = false;
      $this->aTrade[$sp__]['single']['aPrice'] = $aPrice;
      $this->aTrade[$sp__]['single']['close'] = false;
      $this->aSignal[$sp__] = true;
      #var_dump(['++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++',$sPattern]);
    }

    if ($sPattern == '3blackcrows')
    {
      if ($this->aTrade[$sp__]['single']['sell']) 
      {
        $this->aSignal[$sp__] = false;
        return true;
      }

      if ($this->aTrade[$sp__]['single']['buy']) 
      {
        $this->aTrade[$sp__]['single']['units'] = (-$this->iUnits);
      }

      $this->aTrade[$sp__]['single']['units'] = $this->aTrade[$sp__]['single']['units'] - $this->iUnits;
      $this->aTrade[$sp__]['single']['buy'] = false;
      $this->aTrade[$sp__]['single']['sell'] = true;
      $this->aTrade[$sp__]['single']['aPrice'] = $aPrice;
      $this->aTrade[$sp__]['single']['close'] = false;
      $this->aSignal[$sp__] = true;
      #var_dump(['-----------------------------------------------------------------------',$sPattern]);
    }

    return true;

  }
## end trade_candles

  public function variable_set_sticks_functions()
  {
    if (!empty($this->aFunctionsSignal)) return true;
    if (!empty($this->aFunctionsConfirmation)) return true;
    if (!empty($this->aFunctionsIndecision)) return true;
    if (!empty($this->aFunctionsContinuation)) return true;


    $this->aFunctionsConfirmation = array(

    );

    $this->aFunctionsSignal = array(
      '2crows',
      '3blackcrows',
      '3inside',
      '3linestrike',
      '3outside',
      '3starsinsouth',
      '3whitesoldiers',
      'abandonedbaby',
      'advanceblock',
      'belthold',
      'breakaway',
      'closingmarubozu',
      'concealbabyswall',
      'counterattack',
      'darkcloudcover',
      'doji',
      'dojistar',
      'dragonflydoji',
      'engulfing',
      'eveningdojistar',
      'eveningstar',
      'gapsidesidewhite',
      'gravestonedoji',
      'hammer',
      'hangingman',
      'harami',
      'haramicross',
      'highwave',
      'hikkake',
      'hikkakemod',
      'homingpigeon',
      'identical3crows',
      'inneck',
      'invertedhammer',
      'kicking',
      'kickingbylength',
      'ladderbottom',
      'longleggeddoji',
      'longline',
      'marubozu',
      'matchinglow',
      'mathold',
      'morningdojistar',
      'morningstar',
      'onneck',
      'piercing',
      'rickshawman',
      'risefall3methods',
      'separatinglines',
      'shootingstar',
      'shortline',
      'spinningtop',
      'stalledpattern',
      'sticksandwich',
      'takuri',
      'tasukigap',
      'thrusting',
      'tristar',
      'unique3river',
      'upsidegap2crows',
      'xsidegap3methods'
    );

    $this->aFunctionsReversal = array(
      '2crows',
      '3blackcrows',
      '3inside',
      '3outside',
      'abandonedbaby',
      'advanceblock',
      #'belthold',
      'breakaway',
      'counterattack',
      #'doji',
      'engulfing',
      'eveningdojistar',
      'eveningstar',
      'harami',
      'identical3crows',
      'kicking',
      'shootingstar',
      'stalledpattern',
      'tristar',
      '3starsinsouth',
      '3whitesoldiers',
      'hammer',
      'homingpigeon',
      'invertedhammer',
      'ladderbottom',
      'morningdojistar',
      'morningstar',
      'piercing',
      'sticksandwich',
      'takuri',
      'unique3river',
      #
      'dragonflydoji',
      'darkcloudcover',
      #'xsidegap3methods',
      #'hikkake',
      #'hangingman',
      #'highwave'
    );

    $this->aFunctionsContinuation = array(
      'closingmarubozu',
      'gapsidesidewhite',
      'hikkake',
      'inneck',
      'longline',
      'marubozu',
      'mathold',
      'onneck',
      'risefall3methods',
      'separatinglines'
    );

    $this->aFunctionsIndecision = array(
      'gravestonedoji',
      'longleggeddoji',
      'highwave',
      'rickshawman',
      'shortline',
      'spinningtop'
    );

    return true;
  }


  public function set_local_variables()
  {
    $sp__ = $this->sPair;
    #trade
    if (!isset($this->aTrade[$sp__])) $this->aTrade[$sp__] = array();
    if (!isset($this->aTrade[$sp__]['single'])) $this->aTrade[$sp__]['single'] = array();
    if (!isset($this->aTrade[$sp__]['single']['buy'])) $this->aTrade[$sp__]['single']['buy'] = false;
    if (!isset($this->aTrade[$sp__]['single']['sell'])) $this->aTrade[$sp__]['single']['sell'] = false;
    if (!isset($this->aTrade[$sp__]['single']['units'])) $this->aTrade[$sp__]['single']['units'] = 0;
    if (!isset($this->aTrade[$sp__]['single']['close'])) $this->aTrade[$sp__]['single']['close'] = false;

    if (!isset($this->aTrade[$sp__]['single']['fulfilled'])) $this->aTrade[$sp__]['single']['fulfilled'] = 0;
    if (!isset($this->aTrade[$sp__]['single']['time'])) $this->aTrade[$sp__]['single']['time'] = 0;

    # set session for market orders
    if (!isset($_SESSION[$sp__])) $_SESSION[$sp__] = array();
    if (!isset($_SESSION[$sp__]['log'])) $_SESSION[$sp__]['log'] = array();
    if (!isset($_SESSION[$sp__]['trade'])) $_SESSION[$sp__]['trade'] = array();
    if (!isset($_SESSION[$sp__]['market_orders'])) $_SESSION[$sp__]['market_orders'] = array();
    if (!empty($_SESSION[$sp__]['market_orders'])) 
    {
      #print_r(isset($_SESSION[$sp__]['market_orders']));
      if (empty($this->aSignal[$sp__])) $this->aTrade[$sp__]['single'] = $_SESSION[$sp__]['market_orders'];
      #var_dump($this->aTrade[$sp__]['single']);
    }
    if (count($_SESSION[$sp__]['market_orders']) > 80) array_shift($_SESSION[$sp__]['market_orders']);

/*
    var_dump([
      $sp__,
      $_SESSION[$sp__]['market_orders'],
      #'$_SESSION',
      #$_SESSION,
      'aTrade',
      $this->aTrade[$sp__],
      'aSignal',
      $this->aSignal,
      empty($this->aSignal[$sp__])
    ]);
*/
    return true;
  }


  public function __destruct()
  {
    #unset($_SESSION);
    #session_destroy();
  }

}

/*
References

Bennett, J. (2015, January 14). How to trade the bearish engulfing pattern. Daily Price Action. https://dailypriceaction.com/blog/how-to-trade-the-bearish-engulfing-pattern/
*/

