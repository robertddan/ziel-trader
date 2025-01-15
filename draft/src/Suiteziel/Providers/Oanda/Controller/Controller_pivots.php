<?php

namespace App\Suiteziel\Providers\Oanda\Controller;


use App\Suiteziel\Framework\Controller;

class Controller_pivots extends Controller
{

	public function __construct()
	{

  }

  public function pivots_stream_client(&$aPrice, $aStream)
  {
    foreach ($aStream as $k => $a) $this->{$k} = $a;
    if (!isset($aPrice['close'])) return true;
    if (isset($aPrice['close'])) if ($aPrice['close'] == 0) return true;
    bcscale($this->iScale);

    if (!$this->set_class_variables($aPrice, false)) exit('set_class_variables');
    if (!$this->set_ma($aPrice, false)) exit('set_ma');

    #if (!$this->fibonacci_retracement($aPrice, false)) exit('fibonacci_retracement');
    #if (!$this->fibonacci_timezones($aPrice, false)) exit('fibonacci_timezones');
    #if (!$this->fibonacci_fans($aPrice, false)) exit('fibonacci_fans');
    #if (!$this->standard_pivot_points($aPrice, false)) exit('standard_pivot_points');
    #if (!$this->demark_pivot_points($aPrice, false)) exit('demark_pivot_points');
    

    return true;
  }

  public function pivots_view_client(&$aPrice, $aView)
  {
    foreach ($aView as $k => $a) $this->{$k} = $a;
    if (!isset($aPrice['close'])) return true;
    if (isset($aPrice['close'])) if ($aPrice['close'] == 0) return true;
    bcscale($this->iScale);
    if (!$this->set_class_variables($aPrice, true)) exit('set_class_variables');
    if (!$this->set_ma($aPrice, true)) exit('set_ma');

    if (!$this->fibonacci_retracement($aPrice, true)) exit('fibonacci_retracement');
    if (!$this->fibonacci_timezones($aPrice, true)) exit('fibonacci_timezones');
    if (!$this->fibonacci_fans($aPrice, true)) exit('fibonacci_fans');
    if (!$this->standard_pivot_points($aPrice, true)) exit('standard_pivot_points');
    if (!$this->demark_pivot_points($aPrice, true)) exit('demark_pivot_points');
    

    return true;
  }

  protected function fibonacci_retracement(&$aPrices)
  {
    /*
    Pivot Point (P) = (High + Low + Close)/3
    Support 1 (S1) = P - {.382 * (High  -  Low)}
    Support 2 (S2) = P - {.618 * (High  -  Low)}
    Support 3 (S3) = P - {1 * (High  -  Low)}
    Resistance 1 (R1) = P + {.382 * (High  -  Low)}
    Resistance 2 (R2) = P + {.618 * (High  -  Low)}
    Resistance 3 (R3) = P + {1 * (High  -  Low)}
    */

    $sp__ = $this->sPair;	
    if (empty($this->aaPivots[$sp__]['sma'])) return true;
    $a__sma = $this->aaPivots[$sp__]['sma'];

    $fPivotPointP = ($a__sma['h'] + $a__sma['l'] + $a__sma['c']) / 3;
    $fSupport0 = $fPivotPointP - (0.236 * ($a__sma['h'] - $a__sma['l']));
    $fSupport1 = $fPivotPointP - (0.382 * ($a__sma['h'] - $a__sma['l']));
    $fSupport2 = $fPivotPointP - (0.500 * ($a__sma['h'] - $a__sma['l']));
    $fSupport3 = $fPivotPointP - (0.618 * ($a__sma['h'] - $a__sma['l']));
    $fSupport4 = $fPivotPointP - (0.750 * ($a__sma['h'] - $a__sma['l']));
    $fSupport5 = $fPivotPointP - (1.000 * ($a__sma['h'] - $a__sma['l']));
    $fSupport6 = $fPivotPointP - (1.382 * ($a__sma['h'] - $a__sma['l']));
    $fSupport7 = $fPivotPointP - (1.618 * ($a__sma['h'] - $a__sma['l']));
    $fSupport8 = $fPivotPointP - (2.000 * ($a__sma['h'] - $a__sma['l']));
    $fSupport9 = $fPivotPointP - (2.618 * ($a__sma['h'] - $a__sma['l']));
    $fResistance0 = $fPivotPointP + (0.236 * ($a__sma['h'] - $a__sma['l']));
    $fResistance1 = $fPivotPointP + (0.382 * ($a__sma['h'] - $a__sma['l']));
    $fResistance2 = $fPivotPointP + (0.500 * ($a__sma['h'] - $a__sma['l']));
    $fResistance3 = $fPivotPointP + (0.618 * ($a__sma['h'] - $a__sma['l']));
    $fResistance4 = $fPivotPointP + (0.750 * ($a__sma['h'] - $a__sma['l']));
    $fResistance5 = $fPivotPointP + (1.000 * ($a__sma['h'] - $a__sma['l']));
    $fResistance6 = $fPivotPointP + (1.382 * ($a__sma['h'] - $a__sma['l']));
    $fResistance7 = $fPivotPointP + (1.618 * ($a__sma['h'] - $a__sma['l']));
    $fResistance8 = $fPivotPointP + (2.000 * ($a__sma['h'] - $a__sma['l']));
    $fResistance9 = $fPivotPointP + (2.618 * ($a__sma['h'] - $a__sma['l']));
#61.8%, 100%, 161.8%, 200%, and 261.8%. 
  
    $aFibonacci = array(
      '__p' => $fPivotPointP,
      '__s0' => $fSupport0,
      '__s1' => $fSupport1,
      '__s2' => $fSupport2,
      '__s3' => $fSupport3,
      '__s4' => $fSupport4,
      '__s5' => $fSupport5,
      '__s6' => $fSupport6,
      '__s7' => $fSupport7,
      '__s8' => $fSupport8,
      '__s9' => $fSupport9,
      '__r0' => $fResistance0,
      '__r1' => $fResistance1,
      '__r2' => $fResistance2,
      '__r3' => $fResistance3,
      '__r4' => $fResistance4,
      '__r5' => $fResistance5,
      '__r6' => $fResistance6,
      '__r7' => $fResistance7,
      '__r8' => $fResistance8,
      '__r9' => $fResistance9,
    );

    if (empty($this->aaPivots[$sp__]['retracement'])) {$aPrices['pivots']['retracement'] = $this->aaPivots[$sp__]['retracement'] = $aFibonacci; return true;}

    if (
      bccomp($aPrices['low'], $this->aaPivots[$sp__]['retracement']['__r5']) > 0
    )
    {
      $aPrices['pivots']['retracement'] = $this->aaPivots[$sp__]['retracement'] = $aFibonacci;
      #$this->aaPivots[$sp__]['swing_low'] = array(); #$_SESSION[$sp__]['swing_low'];
    }
    elseif (
      bccomp($aPrices['high'], $this->aaPivots[$sp__]['retracement']['__s5']) < 0
    )
    {
      $aPrices['pivots']['retracement'] = $this->aaPivots[$sp__]['retracement'] = $aFibonacci;
      #$this->aaPivots[$sp__]['swing_high'] = array(); #$_SESSION[$sp__]['swing_high'];
    }
    else
    {
      $aPrices['pivots']['retracement'] = $this->aaPivots[$sp__]['retracement'];
    }
  
    return true;
  }

  protected function fibonacci_timezones(&$aPrices)
  {
    /*
    011 23 58 13 21 34 52 84 
    */

    $sp__ = $this->sPair;	

    if (!isset($aPrices['pivots']['retracement'])) return true;

    if (empty($this->aaPivots[$sp__]['sma'])) return true;
    $a__sma = $this->aaPivots[$sp__]['sma'];

    $fPivotPointP = ($a__sma['h'] + $a__sma['l'] + $a__sma['c']) / 3;
    $fSupport0S0 = $fPivotPointP - (0.236 * ($a__sma['h'] - $a__sma['l']));
    $fSupport1S1 = $fPivotPointP - (0.382 * ($a__sma['h'] - $a__sma['l']));
    $fSupport2S2 = $fPivotPointP - (0.618 * ($a__sma['h'] - $a__sma['l']));
    $fSupport3S3 = $fPivotPointP - (1.000 * ($a__sma['h'] - $a__sma['l']));
    $fSupport4S4 = $fPivotPointP - (1.382 * ($a__sma['h'] - $a__sma['l']));
    $fSupport5S5 = $fPivotPointP - (1.618 * ($a__sma['h'] - $a__sma['l']));
    $fSupport6S6 = $fPivotPointP - (2.000 * ($a__sma['h'] - $a__sma['l']));
    $fSupport7S7 = $fPivotPointP - (2.618 * ($a__sma['h'] - $a__sma['l']));
    $fResistance0R0 = $fPivotPointP + (0.236 * ($a__sma['h'] - $a__sma['l']));
    $fResistance1R1 = $fPivotPointP + (0.382 * ($a__sma['h'] - $a__sma['l']));
    $fResistance2R2 = $fPivotPointP + (0.618 * ($a__sma['h'] - $a__sma['l']));
    $fResistance3R3 = $fPivotPointP + (1.000 * ($a__sma['h'] - $a__sma['l']));
    $fResistance4R4 = $fPivotPointP + (1.382 * ($a__sma['h'] - $a__sma['l']));
    $fResistance5R5 = $fPivotPointP + (1.618 * ($a__sma['h'] - $a__sma['l']));
    $fResistance6R6 = $fPivotPointP + (2.000 * ($a__sma['h'] - $a__sma['l']));
    $fResistance7R7 = $fPivotPointP + (2.618 * ($a__sma['h'] - $a__sma['l']));
#61.8%, 100%, 161.8%, 200%, and 261.8%. 
  
    $aFibonacci = array(
      '___p' => $fPivotPointP,
      '___s0' => $fSupport0S0,
      '___s1' => $fSupport1S1,
      '___s2' => $fSupport2S2,
      '___s3' => $fSupport3S3,
      '___s4' => $fSupport4S4,
      '___s5' => $fSupport5S5,
      '___s6' => $fSupport6S6,
      '___s7' => $fSupport7S7,
      '___r0' => $fResistance0R0,
      '___r1' => $fResistance1R1,
      '___r2' => $fResistance2R2,
      '___r3' => $fResistance3R3,
      '___r4' => $fResistance4R4,
      '___r5' => $fResistance5R5,
      '___r6' => $fResistance6R6,
      '___r7' => $fResistance7R7,
    );

    if (empty($this->aaPivots[$sp__]['timezones'])) {$aPrices['pivots']['timezones'] = $this->aaPivots[$sp__]['timezones'] = $aFibonacci; return true;}

    if (
      $aPrices['closeoutBid'] > $this->aaPivots[$sp__]['timezones']['___r5']
      )
    {
      $aPrices['pivots']['timezones'] = $this->aaPivots[$sp__]['timezones'] = $aFibonacci;
    }
    elseif (
      $aPrices['closeoutAsk'] < $this->aaPivots[$sp__]['timezones']['___s5']
      )
    {
      $aPrices['pivots']['timezones'] = $this->aaPivots[$sp__]['timezones'] = $aFibonacci;
    }
    else
    {
      $aPrices['pivots']['timezones'] = $this->aaPivots[$sp__]['timezones'];
    }

    return true;
  }

  protected function fibonacci_fans(&$aPrices)
  {
    /*
    Pivot Point (P) = (High + Low + Close)/3
    Support 1 (S1) = P - {.382 * (High  -  Low)}
    Support 2 (S2) = P - {.618 * (High  -  Low)}
    Support 3 (S3) = P - {1 * (High  -  Low)}
    Resistance 1 (R1) = P + {.382 * (High  -  Low)}
    Resistance 2 (R2) = P + {.618 * (High  -  Low)}
    Resistance 3 (R3) = P + {1 * (High  -  Low)}
    */

    $sp__ = $this->sPair;	

    #if (!isset($aPrices['close'])) return true;
    #if (isset($aPrices['close'])) if ($aPrices['close'] == 0) return true;
    if (empty($this->aaPivots[$sp__]['sma'])) return true;
    $a__sma = $this->aaPivots[$sp__]['sma'];

    $fPivotPointP = ($a__sma['h'] + $a__sma['l'] + $a__sma['c']) / 3;
    $fSupport0S0 = $fPivotPointP - (0.236 * ($a__sma['h'] - $a__sma['l']));
    $fSupport1S1 = $fPivotPointP - (0.382 * ($a__sma['h'] - $a__sma['l']));
    $fSupport2S2 = $fPivotPointP - (0.618 * ($a__sma['h'] - $a__sma['l']));
    $fSupport3S3 = $fPivotPointP - (1.000 * ($a__sma['h'] - $a__sma['l']));
    $fSupport4S4 = $fPivotPointP - (1.382 * ($a__sma['h'] - $a__sma['l']));
    $fSupport5S5 = $fPivotPointP - (1.618 * ($a__sma['h'] - $a__sma['l']));
    $fSupport6S6 = $fPivotPointP - (2.000 * ($a__sma['h'] - $a__sma['l']));
    $fSupport7S7 = $fPivotPointP - (2.618 * ($a__sma['h'] - $a__sma['l']));
    $fResistance0R0 = $fPivotPointP + (0.236 * ($a__sma['h'] - $a__sma['l']));
    $fResistance1R1 = $fPivotPointP + (0.382 * ($a__sma['h'] - $a__sma['l']));
    $fResistance2R2 = $fPivotPointP + (0.618 * ($a__sma['h'] - $a__sma['l']));
    $fResistance3R3 = $fPivotPointP + (1.000 * ($a__sma['h'] - $a__sma['l']));
    $fResistance4R4 = $fPivotPointP + (1.382 * ($a__sma['h'] - $a__sma['l']));
    $fResistance5R5 = $fPivotPointP + (1.618 * ($a__sma['h'] - $a__sma['l']));
    $fResistance6R6 = $fPivotPointP + (2.000 * ($a__sma['h'] - $a__sma['l']));
    $fResistance7R7 = $fPivotPointP + (2.618 * ($a__sma['h'] - $a__sma['l']));
#61.8%, 100%, 161.8%, 200%, and 261.8%. 
  
    $aFibonacci = array(
      '___p' => $fPivotPointP,
      '___s0' => $fSupport0S0,
      '___s1' => $fSupport1S1,
      '___s2' => $fSupport2S2,
      '___s3' => $fSupport3S3,
      '___s4' => $fSupport4S4,
      '___s5' => $fSupport5S5,
      '___s6' => $fSupport6S6,
      '___s7' => $fSupport7S7,
      '___r0' => $fResistance0R0,
      '___r1' => $fResistance1R1,
      '___r2' => $fResistance2R2,
      '___r3' => $fResistance3R3,
      '___r4' => $fResistance4R4,
      '___r5' => $fResistance5R5,
      '___r6' => $fResistance6R6,
      '___r7' => $fResistance7R7,
    );

    if (empty($this->aaPivots[$sp__]['fibonacci_fans'])) {$aPrices['pivots']['fibonacci_fans'] = $this->aaPivots[$sp__]['fibonacci_fans'] = $aFibonacci; return true;}

    if (
      $aPrices['closeoutBid'] > $this->aaPivots[$sp__]['fibonacci_fans']['___r5']
      )
    {
      $aPrices['pivots']['fibonacci_fans'] = $this->aaPivots[$sp__]['fibonacci_fans'] = $aFibonacci;
    }
    elseif (
      $aPrices['closeoutAsk'] < $this->aaPivots[$sp__]['fibonacci_fans']['___s5']
      )
    {
      $aPrices['pivots']['fibonacci_fans'] = $this->aaPivots[$sp__]['fibonacci_fans'] = $aFibonacci;
    }
    else
    {
      $aPrices['pivots']['fibonacci_fans'] = $this->aaPivots[$sp__]['fibonacci_fans'];
    }

    return true;
  }

  protected function standard_pivot_points(&$aPrices)
  {
    /*
    Pivot Point (P) = (High + Low + Close)/3
    Support 1 (S1) = (P x 2) - High
    Support 2 (S2) = P  -  (High  -  Low)
    Resistance 1 (R1) = (P x 2) - Low
    Resistance 2 (R2) = P + (High  -  Low)
    */

    $sp__ = $this->sPair;	
    #if (isset($aPrices['close'])) if ($aPrices['close'] == 0) return true;
    if (empty($this->aaPivots[$sp__]['sma'])) return true;
    $a__sma = $this->aaPivots[$sp__]['sma'];

    $fPivotPointP = ($a__sma['h'] + $a__sma['l'] + $a__sma['c']) / 3;
    $fSupport1S1 = ($fPivotPointP * 2) - $a__sma['h'];
    $fSupport2S2 = $fPivotPointP - ($a__sma['h'] - $a__sma['l']);
    $fResistance1R1 = ($fPivotPointP * 2) - $a__sma['l'];
    $fResistance2R2 = $fPivotPointP + ($a__sma['h'] - $a__sma['l']);

    $aStandard = array(
      'standard_pivot_p' => $fPivotPointP,
      'standard_pivot_s1' => $fSupport1S1,
      'standard_pivot_s2' => $fSupport2S2,
      'standard_pivot_r1' => $fResistance1R1,
      'standard_pivot_r2' => $fResistance2R2
    );

    if (empty($this->aaPivots[$sp__]['standard'])) {$aPrices['pivots']['standard'] = $this->aaPivots[$sp__]['standard'] = $aStandard; return true;}

    if (
      $aPrices['closeoutBid'] > $this->aaPivots[$sp__]['standard']['standard_pivot_r2']
      )
    {
      $aPrices['pivots']['standard'] = $this->aaPivots[$sp__]['standard'] = $aStandard;
    }
    elseif (
      $aPrices['closeoutAsk'] < $this->aaPivots[$sp__]['standard']['standard_pivot_s2']
      )
    {
      $aPrices['pivots']['standard'] = $this->aaPivots[$sp__]['standard'] = $aStandard;
    }
    else
    {
      $aPrices['pivots']['standard'] = $this->aaPivots[$sp__]['standard'];
    }

    return true;
  }

  protected function demark_pivot_points(&$aPrices)
  {
    /*
    If Close < Open, then X = High + (2 x Low) + Close
    If Close > Open, then X = (2 x High) + Low + Close
    If Close = Open, then X = High + Low + (2 x Close)
    Pivot Point (P) = X/4
    Support 1 (S1) = X/2 - High
    Resistance 1 (R1) = X/2 - Low
    */

    $sp__ = $this->sPair;	
    #if (isset($aPrices['close'])) if ($aPrices['close'] == 0) return true;
    if (empty($this->aaPivots[$sp__]['sma'])) return true;
    $a__sma = $this->aaPivots[$sp__]['sma'];

    if ($a__sma['c'] < $a__sma['o']) $fx____ = $a__sma['h'] + (2 * $a__sma['l']) + $a__sma['c'];
    elseif ($a__sma['c'] > $a__sma['o']) $fx____ = (2 * $a__sma['h']) + (2 + $a__sma['l']) + $a__sma['c'];
    elseif ($a__sma['c'] == $a__sma['o']) $fx____ = $a__sma['h'] + $a__sma['l'] + (2 * $a__sma['c']);
    #if ($aPrices['close'] < $aPrices['open']) $fx____ = $a__sma['h'] + (2 * $a__sma['l']) + $a__sma['c'];
    #elseif ($aPrices['close'] > $aPrices['open']) $fx____ = (2 * $a__sma['h']) + (2 + $a__sma['l']) + $a__sma['c'];
    #elseif ($aPrices['close'] == $aPrices['open']) $fx____ = $a__sma['h'] + $a__sma['l'] + (2 * $a__sma['c']);
    $fPivotPointP = $fx____ / 4;
    $fSupport1S1 = $fx____ / 2 - $a__sma['h'];
    $fResistance1R1 = $fx____ / 2 - $a__sma['l'];

    $aDemark = array(
      'demark_pivot_p' => $fPivotPointP,
      'demark_pivot_s1' => $fSupport1S1,
      'demark_pivot_r1' => $fResistance1R1
    );

    if (empty($this->aaPivots[$sp__]['demark'])) {$aPrices['pivots']['demark'] = $this->aaPivots[$sp__]['demark'] = $aDemark; return true;}

    if (
      $aPrices['closeoutAsk'] > $this->aaPivots[$sp__]['demark']['demark_pivot_r1']
      
      )
    {
      $aPrices['pivots']['demark'] = $this->aaPivots[$sp__]['demark'] = $aDemark;
    }
    elseif (
      $aPrices['closeoutBid'] < $this->aaPivots[$sp__]['demark']['demark_pivot_s1']
  
      )
    {
      $aPrices['pivots']['demark'] = $this->aaPivots[$sp__]['demark'] = $aDemark;
    }
    else
    {
      $aPrices['pivots']['demark'] = $this->aaPivots[$sp__]['demark'];
    }

    return true;
  }

/*
  
Woodie Pivot Point Calculation

This method of pivot point calculation uses the open price for the period observed. The formulas are as follows:

Pivot Point for Current Day = [High(previous day) + Low(previous day) + Open (previous day) * 2] / 4

R1 = 2 * Pivot Point – Low(previous day)
S1 = 2 * Pivot Point – High(previous day)

R2 = Pivot Point + High(previous day) – Low(previous day)
S2 = Pivot Point – High(previous day) + Low(previous day)

R3 = High(previous day) + 2 * [Pivot Point – Low(previous day)]
S3 = Low(previous day) – 2 * [High(previous day) – Pivot Point]

R4 = R3 + High(previous day) – Low(previous day)
S4 = S3 – High(previous day) + Low(previous day)


Camarilla Pivot Points

Here are the formulas:

Central Pivot Point = [High(previous day) + Low(previous day) + Close(previous day)]/3

R1 = Close(previous day) + [High(previous day) – Low(previous day)] * 1.1/12
S1 = Close(previous day) – [High(previous day) – Low(previous day)] * 1.1/12

R2 = Close(previous day) + [High(previous day) – Low(previous day)] * 1.1/6
S2 = Close(previous day) – [High(previous day) – Low(previous day)] * 1.1/6

R3 = Close(previous day) + [(High(previous day) – Low(previous day)] * 1.1/4
S3 = Close(previous day) – [(High(previous day) – Low(previous day)] * 1.1/4

R4 = Close(previous day) + [High(previous day) – Low(previous day)] * 1.1/2
S4 = Close(previous day) – [High(previous day) – Low(previous day)] * 1.1/2


Floor Pivot Points Calculation

The formulae are as follows:

Pivot Point = [High(previous day) + Low(previous day) + Close(previous day)]/3

R1 = (2*Pivot Point) – Low(previous day)
S1 = (2*Pivot Point) – High(previous day)

R2 = Pivot Point + (R1 – S1)
S2 = Pivot Point – (R1 – S1)

R3 = Pivot Point + (R2 – S2) 
S3 = Pivot Point – (R2 – S2)
*/

  protected function set_ma(&$aPrices)
  {
    $sp__ = $this->sPair;	

    if (count($this->aaPivots[$sp__]['closeout']) < $this->iTimeframes) return true;

    $aaSessionMids = $this->aaPivots[$sp__]['closeout'];
    # bid + asks for h/l
    $this->aaPivots[$sp__]['sma'] = array(
      'o' => current($aaSessionMids),
      'h' => max($aaSessionMids),
      'l' => min($aaSessionMids),
      'c' => end($aaSessionMids),
    );

    return true;
  }

  protected function set_class_variables(&$aPrices)
  {
    $sp__ = $this->sPair;

    $this->iTimeframes = $this->aPair['iTimeframes']; #5

    if (!isset($this->aaPivots)) $this->aaPivots = array();
    if (!isset($this->aaPivots[$sp__])) $this->aaPivots[$sp__] = array();
    if (!isset($this->aaPivots[$sp__]['closeout'])) $this->aaPivots[$sp__]['closeout'] = array();
    if (empty($this->aaPivots[$sp__]['sma'])) $this->aaPivots[$sp__]['sma'] = array();

    if (count($this->aaPivots[$sp__]['closeout']) > $this->iTimeframes) 
    {
      array_pop($this->aaPivots[$sp__]['closeout']); 
      #$this->aaPivots[$sp__]['closeout'] = array();
      #$this->aaPivots[$sp__]['sma'] = array();
    }
    
    array_unshift(
      $this->aaPivots[$sp__]['closeout'],
      $aPrices['mids']
    );

    return true;
  }

  public function __destruct()
  {
    #session_destroy();
  }
}


/*
References

Joeldg/bowhead. (2017, 16). GitHub. 
https://github.com/joeldg/bowhead/blob/master/app/Traits/Pivots.php

Pivot points [ChartSchool]. (n.d.). StockCharts.com | Advanced Financial Charts & Technical Analysis Tools. 
https://stockcharts.com/school/doku.php?id=chart_school%3Atechnical_indicators%3Apivot_points
*/

?>
