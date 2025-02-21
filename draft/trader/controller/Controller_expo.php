<?php

namespace Ziel\Providers\Oanda\Controller;

use Ziel\Framework\Controller;


class Controller_expo extends Controller
{

  public $iElementsNo = 20;
  public $aaSticks = array();
  # analysis
  public $aAnalysis;
  public $fSpread;
  

	public function __construct()
	{
    $this->set_trader_variable();
  }

  private function set_trader_variable()
  {
    $this->aExpo = array(
      'sma' => array('real' => array(), 'timePeriod' => 20), # 2 to 100000.
      'ema' => array('real' => array(), 'timePeriod' => 20), # 2 to 100000.
      'wma' => array('real' => array(), 'timePeriod' => 20), # 2 to 100000.
      'dema' => array('real' => array(), 'timePeriod' => 20), # 2 to 100000.
      'tema' => array('real' => array(), 'timePeriod' => 20), # 2 to 100000.
      'trima' => array('real' => array(), 'timePeriod' => 20), # 2 to 100000.
      'kama' => array('real' => array(), 'timePeriod' => 20), # 2 to 100000.
      't3' => array('real' => array(), 'timePeriod' => 20, 'vFactor' => 1), # 2 to 100000. # 1 to 0. 
    );
  }

  public function expo_stream_client(&$aPrice, $aStream)
  {
    #if (!isset($aPrice['close'])) return true;
    foreach ($aStream as $k => $a) $this->{$k} = $a;

    
    #if (!$this->juggle_expo_prices($aPrice, true)) exit('juggle_expo_prices');
    if (!$this->set_class_variables($aPrice, true)) exit('set_class_variables');
    if (!$this->set_expo_lines($aPrice, true)) exit('set_expo_lines');
    #if (!$this->set_expo_line($aPrice, true)) exit('set_expo_line');

    return true;
  }

  public function expo_view_client(&$aPrice, $aView)
  {
    #if (!isset($aPrice['close'])) return true;
    foreach ($aView as $k => $a) $this->{$k} = $a;

    if (!$this->juggle_expo_prices($aPrice, true)) exit('juggle_expo_prices');
    if (!$this->set_class_variables($aPrice, true)) exit('set_class_variables');
    if (!$this->set_expo_lines($aPrice, true)) exit('set_expo_lines');
    if (!$this->set_expo_line($aPrice, true)) exit('set_expo_line');
    if (!$this->set_swing($aPrice, true)) exit('set_swing');
    if (!$this->test_trader($aPrice, true)) exit('test_trader');

    return true;
  }

  public function test_trader(&$aPrice, $bView = false)
  {
    $this->iElementsNo = 15;

    if (!isset($aPrice['close'])) return true;
    if (isset($aPrice['close'])) if ($aPrice['close'] == 0) return true;
    $sp__ = $this->sPair;	

    if (!isset($this->aaSticks[$sp__])) $this->aaSticks[$sp__] = array();

    array_unshift($this->aaSticks[$sp__], $aPrice);

    if (count($this->aaSticks[$sp__]) < $this->iElementsNo) return true;
    if (count($this->aaSticks[$sp__]) > $this->iElementsNo) array_pop($this->aaSticks[$sp__]);

    $aTraderArguments = $this->aaSticks[$sp__];

    $aMohlc = array_map(function($k__) use ($aTraderArguments) {
      return array_column($aTraderArguments, $k__);
    }, array('open','high','low','close'));

    $a_sma = trader_sma($aMohlc[3], $this->iElementsNo);

    $aPrice['expo_line_1'][0] = array_pop($a_sma); #$fExpoLine;

    return true;
  }

  public function set_swing(&$aPrice, $bView = false)
  {
    $this->iElementsNo = 15;

    if (!isset($aPrice['high'])) return true;
    #if (isset($aPrice['high'])) if ($aPrice['high'] == 0) return true;
    $sp__ = $this->sPair;	


    if (!isset($this->aaSwing[$sp__])) $this->aaSwing[$sp__] = array();
    if (!isset($this->aaSwing[$sp__]['high'])) $this->aaSwing[$sp__]['high'] = $aPrice['high'];
    if (!isset($this->aaSwing[$sp__]['low'])) $this->aaSwing[$sp__]['low'] = $aPrice['low'];

    if ($aPrice['high'] > $this->aaSwing[$sp__]['high']) $this->aaSwing[$sp__]['high'] = $aPrice['high'];

    if ($aPrice['low'] < $this->aaSwing[$sp__]['low']) $this->aaSwing[$sp__]['low'] = $aPrice['low'];

    $aPrice['expo_line_2'][0] = $this->aaSwing[$sp__]['high'];

    $aPrice['expo_line_3'][0] = $this->aaSwing[$sp__]['low'];


    $aPrice['expo_line_1'][0] = $aPrice['expo_line_4'][0] = $aPrice['expo_line_5'][0] = $aPrice['expo_line_6'][0] = $aPrice['expo_line_7'][0] = -200;

    return true;
  }


  public function set_expo_line(&$aPrices, $bView = false)
  {
    $sp__ = $this->sPair;	
    #bcscale($this->iScale);

    if (!isset($this->aAnalysis[$sp__]['fSpread'])) $this->aAnalysis[$sp__]['fSpread'] = 0;

    #slice
    $aSliceExpo = array_slice($this->aAnalysis[$sp__]['aMidsPrices'], 0, $this->iTimeframes);
    #
    if (count($aSliceExpo) < $this->iTimeframes) return true;


    if ($this->aAnalysis[$sp__]['fSpread'] == 0)
    {
      #trader_linearreg_angle
      #trader_linearreg_slope 
      #trader_linearreg
      #trader_wma
      
      $aParameters = array($this->aAnalysis[$sp__]['aMidsPrices'], $this->iTimeframes);
      $fAwma = call_user_func_array($this->aPair['ma_function'], $aParameters);
      #$fAwma = trader_linearreg_angle($this->aAnalysis[$sp__]['aMidsPrices'], $this->iTimeframes); #abs(bcsub($fhh, $fll));
      $this->aAnalysis[$sp__]['fSpread'] = array_pop($fAwma); #abs(bcsub($fhh, $fll)); #
    }

    if (isset($aPrices['market_order'])) $this->aAnalysis[$sp__]['fPrice0'] = 0;


    if (!isset($this->aAnalysis[$sp__]['fSpread0'])) $this->aAnalysis[$sp__]['fSpread0'] = 0;
    if (!isset($this->aAnalysis[$sp__]['fPrice0'])) $this->aAnalysis[$sp__]['fPrice0'] = 0;
    if ($this->aAnalysis[$sp__]['fSpread0'] == 0)
    {
      $this->aAnalysis[$sp__]['fSpread0'] = abs($this->aAnalysis[$sp__]['fSpread'] / $this->aPair['fRatio']); #abs(bcdiv($this->aAnalysis[$sp__]['fSpread'], $this->aPair['fRatio'])); # 0.100000000)); 
    }

  
    if ($this->aAnalysis[$sp__]['fPrice0'] == 0) $this->aAnalysis[$sp__]['fPrice0'] = $aPrices['high']; 
    $f_____ = abs(bcsub($this->aAnalysis[$sp__]['fPrice0'], $aPrices['high']));
    $fExpoLine = $this->aAnalysis[$sp__]['fPrice0'];

    if ($f_____ >= $this->aAnalysis[$sp__]['fSpread0']) $this->aAnalysis[$sp__]['fPrice0'] = 0;

    if (!isset($this->aExpoLines['expo_line_0'])) $this->aExpoLines['expo_line_0'] = array();
    if (count($this->aExpoLines['expo_line_0']) >= $this->iTimeframes) array_pop($this->aExpoLines['expo_line_0']);

    if (empty($this->aExpoLines['expo_line_0']))
    array_unshift($this->aExpoLines['expo_line_0'], $fExpoLine);
    elseif (bccomp($this->aExpoLines['expo_line_0'][0], $fExpoLine) != 0)
    array_unshift($this->aExpoLines['expo_line_0'], $fExpoLine);

    $aPrices['expo_line_0'] = $this->aExpoLines['expo_line_0']; #$fExpoLine;

    return true;
  }

  public function set_expo_lines(&$aPrices, $bView = false)
  {
    $sp__ = $this->sPair;	
    #bcscale($this->iScale);

    if (!isset($this->aAnalysis[$sp__]['fSpread'])) $this->aAnalysis[$sp__]['fSpread'] = 0;

    #slice
    $aSliceExpo = array_slice($this->aAnalysis[$sp__]['aMidsPrices'], 0, $this->iTimeframes);
    #
    if (count($aSliceExpo) < $this->iTimeframes) return true;


    if ($this->aAnalysis[$sp__]['fSpread'] == 0)
    {
      #trader_linearreg_angle
      #trader_linearreg_slope 
      #trader_linearreg
      #trader_wma
      
      $aParameters = array($this->aAnalysis[$sp__]['aMidsPrices'], $this->iTimeframes);
      $fAwma = call_user_func_array($this->aPair['ma_function'], $aParameters);
      #$fAwma = trader_linearreg_angle($this->aAnalysis[$sp__]['aMidsPrices'], $this->iTimeframes); #abs(bcsub($fhh, $fll));
      $this->aAnalysis[$sp__]['fSpread'] = array_pop($fAwma); #abs(bcsub($fhh, $fll)); #
    }

    $iRange = null;
    for ($i = 0; $i <= 0; $i++)
    {
      if (isset($aPrices['market_order'])) $this->aAnalysis[$sp__]['fPrice'. $i] = 0;


      if (!isset($this->aAnalysis[$sp__]['fSpread'. $i])) $this->aAnalysis[$sp__]['fSpread'. $i] = 0;
      if (!isset($this->aAnalysis[$sp__]['fPrice'. $i])) $this->aAnalysis[$sp__]['fPrice'. $i] = 0;
      if ($this->aAnalysis[$sp__]['fSpread'. $i] == 0)
      {
        $this->aAnalysis[$sp__]['fSpread'. $i] = abs($this->aAnalysis[$sp__]['fSpread'] / $this->aPair['fRatio']); #abs(bcdiv($this->aAnalysis[$sp__]['fSpread'], $this->aPair['fRatio'])); # 0.100000000)); 
      }


      if ($i == 0)
      {
        if ($this->aAnalysis[$sp__]['fPrice'. $i] == 0) $this->aAnalysis[$sp__]['fPrice'. $i] = $aPrices['mids']; 
        $f_____ = abs(bcsub($this->aAnalysis[$sp__]['fPrice'. $i], $aPrices['mids']));
        $fExpoLine = $this->aAnalysis[$sp__]['fPrice'. $i];
      }
      else
      {
        if ($i % 2) # ask
        {
          if ($this->aAnalysis[$sp__]['fPrice'. $i] == 0) $this->aAnalysis[$sp__]['fPrice'. $i] = $aPrices['closeoutAsk'];  
          $f_____ = abs(bcsub($this->aAnalysis[$sp__]['fPrice'. $i], $aPrices['closeoutAsk']));
          # not in the middle the line on view .ok 4. To buy/sell test
          $iRange = bcmul(5, (2/($i == 0 ? 2 : ($i*2))));
          $fExpoLine = bcadd($this->aAnalysis[$sp__]['fPrice'. $i], bcmul($this->aAnalysis[$sp__]['fSpread'], $iRange));
        }
        else # bid
        {
          if ($this->aAnalysis[$sp__]['fPrice'. $i] == 0) $this->aAnalysis[$sp__]['fPrice'. $i] = $aPrices['closeoutBid']; 
          $f_____ = abs(bcsub($this->aAnalysis[$sp__]['fPrice'. $i], $aPrices['closeoutBid']));
          # not in the middle the line on view .ok 4. To buy/sell test
          $fExpoLine = bcsub($this->aAnalysis[$sp__]['fPrice'. $i], bcmul($this->aAnalysis[$sp__]['fSpread'], $iRange));
        }
      }

      if ($f_____ >= $this->aAnalysis[$sp__]['fSpread'. $i]) $this->aAnalysis[$sp__]['fPrice'. $i] = 0;

      if (!isset($this->aExpoLines['expo_line_'. $i])) $this->aExpoLines['expo_line_'. $i] = array();
      if (count($this->aExpoLines['expo_line_'. $i]) >= $this->iTimeframes) array_pop($this->aExpoLines['expo_line_'. $i]);

      if (empty($this->aExpoLines['expo_line_'. $i]))
      array_unshift($this->aExpoLines['expo_line_'. $i], $fExpoLine);
      elseif (bccomp($this->aExpoLines['expo_line_'. $i][0], $fExpoLine) != 0)
      array_unshift($this->aExpoLines['expo_line_'. $i], $fExpoLine);

      $aPrices['expo_line_'. $i] = $this->aExpoLines['expo_line_'. $i]; #$fExpoLine;

    }


    return true;

  }

  public function juggle_expo_prices(&$aPrice, $aView)
  {
    $sp__ = $this->sPair;
    if (!isset($this->aaSticks[$sp__])) $this->aaSticks[$sp__] = array();

    array_push($this->aaSticks[$sp__], $aPrice);

    if (count($this->aaSticks[$sp__]) < $this->iElementsNo) return true;
    if (count($this->aaSticks[$sp__]) > $this->iElementsNo) array_shift($this->aaSticks[$sp__]);

    $aTraderArguments = $this->aaSticks[$sp__];
    $aMohlc = array_map(function($k__) use ($aTraderArguments) {
      return array_column($aTraderArguments, $k__);
    }, array('open','high','low','close'));

    $aResponse = array();

    foreach($this->aExpo as $sFunction => $aParameters)
    {
      $aParameters['real'] = $aMohlc[3];
      $aCallback = call_user_func_array('trader_'. $sFunction, $aParameters);

      array_push($aResponse,
        array($sFunction, (!empty($aCallback) ? end($aCallback) : false))
      );
    }

    $aPrice['expo'] = $aResponse;

    return true;
  }



  protected function set_class_variables(&$aPrices)
  {
    $sp__ = $this->sPair;

    #analysis
    if (!isset($this->iTimeframes)) $this->iTimeframes = 2;

    if (!isset($this->aAnalysis)) $this->aAnalysis = array();
    #if (!isset($this->fSpread)) $this->fSpread = array();
    if (!isset($this->aAnalysis[$sp__])) $this->aAnalysis[$sp__] = array();
    #if (!isset($this->fSpread[$sp__])) $this->fSpread[$sp__] = array();
    if (!isset($this->aAnalysis[$sp__]['aMidsPrices'])) $this->aAnalysis[$sp__]['aMidsPrices'] = array();

    $fhh = $aPrices['closeoutAsk'];
    $fll = $aPrices['closeoutBid'];
    if (count($this->aAnalysis[$sp__]['aMidsPrices']) >= $this->iTimeframes) array_pop($this->aAnalysis[$sp__]['aMidsPrices']);
    # add mids price
    if (!isset($aPrices['mids'])) $aPrices['mids'] = bcdiv(bcadd($fhh, $fll), 2);
    # add spread
    array_unshift($this->aAnalysis[$sp__]['aMidsPrices'], $aPrices['high']);

    return true;
  }





  public function __destruct()
  {
    #session_destroy();
  }

}

?>