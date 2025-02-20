<?php

namespace App\Suiteziel\Providers\Oanda\Controller;


use App\Suiteziel\Framework\Controller;

class Controller_swings extends Controller
{

	public function __construct()
	{
  }

  public function swings_stream_client(&$aPrice, $aStream)
  {
    foreach ($aStream as $k => $a) $this->{$k} = $a;
    #if (!isset($aPrice['close'])) return true;
    #if (isset($aPrice['close'])) if ($aPrice['close'] == 0) return true;
    bcscale($this->iScale);

    if (!$this->set_class_variables($aPrice, false)) exit('set_class_variables');
    if (!$this->swings($aPrice, false)) exit('swings');
    if (!$this->hh_ll($aPrice, true)) exit('hh_ll');

    return true;
  }

  public function swings_view_client(&$aPrice, $aView)
  {
    foreach ($aView as $k => $a) $this->{$k} = $a;
    bcscale($this->iScale);
    #if (!isset($aPrice['close'])) return true;
    #if (isset($aPrice['close'])) if ($aPrice['close'] == 0) return true;
    if (!$this->set_class_variables($aPrice, false)) exit('set_class_variables');
    if (!$this->swings($aPrice, true)) exit('swings');
    if (!$this->hh_ll($aPrice, true)) exit('hh_ll');

    return true;
  }


  protected function hh_ll(&$aPrices)
  {
    $sp__ = $this->sPair;	

    if (!isset($this->aaSwings[$sp__]['swing_higher_high'])) $this->aaSwings[$sp__]['swing_higher_high'] = $aPrices['high'];
    if (!isset($this->aaSwings[$sp__]['swing_lower_low'])) $this->aaSwings[$sp__]['swing_lower_low'] = $aPrices['low'];
    if (!isset($this->aaSwings[$sp__]['swing_memory'])) $this->aaSwings[$sp__]['swing_memory'] = 0;

    if (
      bccomp($aPrices['high'], $this->aaSwings[$sp__]['swing_high']['y']['high']) >= 0
    )
    {
      if (bccomp($aPrices['high'], $this->aaSwings[$sp__]['swing_higher_high']) > 0) 
      {
        $this->aaSwings[$sp__]['swing_higher_high'] = $aPrices['high'];
        #$this->aaSwings[$sp__]['swing_lower_low'] = $aPrices['low'];
        $this->aaSwings[$sp__]['swing_memory'] = +1;
        var_dump(+1);
      }
    }
    elseif (
      bccomp($aPrices['low'], $this->aaSwings[$sp__]['swing_low']['y']['low']) <= 0
    )
    {
      if (bccomp($aPrices['low'], $this->aaSwings[$sp__]['swing_lower_low']) < 0) 
      { 
        $this->aaSwings[$sp__]['swing_lower_low'] = $aPrices['low'];
        #$this->aaSwings[$sp__]['swing_higher_high'] = $aPrices['high'];
        $this->aaSwings[$sp__]['swing_memory'] = -1;
        var_dump(-1);
      }
    }

    $aPrices['swing_lower_low'] = $this->aaSwings[$sp__]['swing_lower_low']; 
    $aPrices['swing_higher_high'] = $this->aaSwings[$sp__]['swing_higher_high'];

    return true;
  }

  protected function swings(&$aPrices)
  {
    $sp__ = $this->sPair;	

    if (!isset($this->aaSwings[$sp__]['swing_low']['y'])) $this->aaSwings[$sp__]['swing_low']['y'] = $aPrices;
    if (!isset($this->aaSwings[$sp__]['swing_low']['x'])) $this->aaSwings[$sp__]['swing_low']['x'] = 0;
    if (!isset($this->aaSwings[$sp__]['swing_low']['set'])) $this->aaSwings[$sp__]['swing_low']['set'] = false;

    if (!isset($this->aaSwings[$sp__]['swing_high']['y'])) $this->aaSwings[$sp__]['swing_high']['y'] = $aPrices;
    if (!isset($this->aaSwings[$sp__]['swing_high']['x'])) $this->aaSwings[$sp__]['swing_high']['x'] = 0;
    if (!isset($this->aaSwings[$sp__]['swing_high']['set'])) $this->aaSwings[$sp__]['swing_high']['set'] = false;

/*
    if (!isset($_SESSION[$sp__]['swings_high'])) $_SESSION[$sp__]['swings_high'] = false;
    if (!isset($_SESSION[$sp__]['swings_low'])) $_SESSION[$sp__]['swings_low'] = false;

    if (!isset($_SESSION[$sp__]['swing_plege']['high'])) $_SESSION[$sp__]['swing_plege']['high'] = array();
    if (!isset($_SESSION[$sp__]['swing_plege']['low'])) $_SESSION[$sp__]['swing_plege']['low'] = array();
*/

#$_SESSION[$sp__]['swings_low'] = $this->aaSwings[$sp__]['swing_low']['y']['low'];
#$_SESSION[$sp__]['swings_high'] = $this->aaSwings[$sp__]['swing_high']['y']['high'];
# cand se reseteaza inainte sa intre if low mai mic ca low_aa

/*
if price_high > y_high
y_high = new
swing_low_set = false
*/ 
    if (
      bccomp($aPrices['high'], $this->aaSwings[$sp__]['swing_high']['y']['high']) >= 0
    )
    {
      #$this->aaSwings[$sp__]['swing_low']['y'];
      $this->aaSwings[$sp__]['swing_high']['y'] = $aPrices;
      $this->aaSwings[$sp__]['swing_high']['x'] = 0;
      $this->aaSwings[$sp__]['swing_high']['set'] = true;
      $this->aaSwings[$sp__]['swing_higher_high'] = $aPrices['high'];
      $aPrices['swings_high'] = $aPrices['high'];
    }
/*
if price_low < y_low
y_low = new
swing_high_set = false
*/
    elseif (
      bccomp($aPrices['low'], $this->aaSwings[$sp__]['swing_low']['y']['low']) <= 0
    )
    {
      #$this->aaSwings[$sp__]['swing_high']['y'];
      $this->aaSwings[$sp__]['swing_low']['y'] = $aPrices;
      $this->aaSwings[$sp__]['swing_low']['x'] = 0;
      $this->aaSwings[$sp__]['swing_low']['set'] = true;
      $this->aaSwings[$sp__]['swing_lower_low'] = $aPrices['low'];
      $aPrices['swings_low'] = $aPrices['low'];
    }
/*
if price_high < y_high
&& swing_low_set = false
y_high = y_high
y_low = new
swing_low_set = true
*/
    if (
      bccomp($aPrices['high'], $this->aaSwings[$sp__]['swing_low']['y']['high']) > 0
      && $this->aaSwings[$sp__]['swing_low']['set'] == true
    )
    {
      #$this->aaSwings[$sp__]['swing_low']['y'];
      $this->aaSwings[$sp__]['swing_high']['y'] = $aPrices;
      $this->aaSwings[$sp__]['swing_high']['x'] = 0;
      $this->aaSwings[$sp__]['swing_low']['set'] = false;
    }
/*
if price_low > y_low
&& swing_low_set = false
y_low = y_low
y_high = new
swing_high_set = true
*/
    elseif (
      bccomp($aPrices['low'], $this->aaSwings[$sp__]['swing_high']['y']['low']) < 0
      && $this->aaSwings[$sp__]['swing_high']['set'] == true
    )
    {
      #$this->aaSwings[$sp__]['swing_high']['y'];
      $this->aaSwings[$sp__]['swing_low']['y'] = $aPrices;
      $this->aaSwings[$sp__]['swing_low']['x'] = 0;
      $this->aaSwings[$sp__]['swing_high']['set'] = false;
    }


    $aPrices['swing_low'] = $this->aaSwings[$sp__]['swing_low'];
    $aPrices['swing_high'] = $this->aaSwings[$sp__]['swing_high'];
/*
    $aPrices['swings_low'] = $_SESSION[$sp__]['swings_low'];
    $aPrices['swings_high'] = $_SESSION[$sp__]['swings_high'];
*/
    $this->aaSwings[$sp__]['swing_high']['x'] = $this->aaSwings[$sp__]['swing_high']['x'] + 1;
    $this->aaSwings[$sp__]['swing_low']['x'] = $this->aaSwings[$sp__]['swing_low']['x'] + 1;


# confirmations
/*
    if (isset($aPrices['swing_high']))
    {
      if (!empty($_SESSION[$sp__]['swing_plege']['high'])) 
      {
        if (!empty($_SESSION[$sp__]['swing_plege']['low'])) 
        {        
          $aPrices['swingl_prew']['y'] = min($_SESSION[$sp__]['swing_plege']['low']);
          $aPrices['swingl_prew']['x'] = $this->aaSwings[$sp__]['swing_high']['x'];
        }
        $aPrices['swingl_next']['y'] = max($_SESSION[$sp__]['swing_plege']['high']);
        $aPrices['swingl_next']['x'] = 1;
        $_SESSION[$sp__]['swing_plege']['high'] = array();

      }
      array_push($_SESSION[$sp__]['swing_plege']['high'], $aPrices['swing_high']['y']['high']);
    }

    if (isset($aPrices['swing_low']))
    {
      if (!empty($_SESSION[$sp__]['swing_plege']['low'])) 
      {
        if (!empty($_SESSION[$sp__]['swing_plege']['high'])) 
        {        
          $aPrices['swingl_prew']['y'] = max($_SESSION[$sp__]['swing_plege']['high']);
          $aPrices['swingl_prew']['x'] = $this->aaSwings[$sp__]['swing_low']['x'];
        }
        $aPrices['swingl_next']['y'] = min($_SESSION[$sp__]['swing_plege']['low']);
        $aPrices['swingl_next']['x'] = 1;
        $_SESSION[$sp__]['swing_plege']['low'] = array();

      }
      array_push($_SESSION[$sp__]['swing_plege']['low'], $aPrices['swing_low']['y']['low']);
    }
*/

    return true;
  }


  protected function swing_low(&$aPrices)
  {
    $sp__ = $this->sPair;	

    return true;
  }

  protected function swing_high(&$aPrices)
  {
    $sp__ = $this->sPair;	

    return true;
  }


  protected function set_class_variables(&$aPrices)
  {
    $sp__ = $this->sPair;

    if (!isset($this->aaSwings)) $this->aaSwings = array();
    if (!isset($this->aaSwings[$sp__])) $this->aaSwings[$sp__] = array();

    # set session for swing
    #if (!isset($_SESSION[$sp__])) $_SESSION[$sp__] = array();
    #if (!isset($_SESSION[$sp__]['swing_low'])) $_SESSION[$sp__]['swing_low'] = array();
    #if (!isset($_SESSION[$sp__]['swing_high'])) $_SESSION[$sp__]['swing_high'] = array();


    return true;
  }

  public function __destruct()
  {
    #session_destroy();
  }
}