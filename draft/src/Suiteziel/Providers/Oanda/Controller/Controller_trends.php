<?php

namespace App\Suiteziel\Providers\Oanda\Controller;

use App\Suiteziel\Framework\Controller;


/*

uptrend
downtrend
sideways trend
complex pullback

3.3.1 – Strength and Weakness
momentum up swing
momentum  down swing


slow - faster
accelerating - deccelerating

Projection & Depth

*/
class Controller_trends extends Controller
{
	public function trends_client(&$aPrice, $aView)
  {
    foreach ($aView as $k => $a) $this->{$k} = $a;
    bcscale($this->iScale);
    if (!$this->set_variables($aPrice)) exit('set_class_variables');
    if (!$this->trend_direction($aPrice)) exit('trend_direction');
    return true;
  }
  

  protected function trend_direction(&$aPrices)
  {
    $sp__ = $this->sPair;

    if ( # uptrend
      bccomp($aPrices['low'], $aPrices['swing_higher_high']) > 0
    )
    {
      $this->aaTrends[$sp__]['uptrend'] = true;
      #$this->aaTrends[$sp__]['downtrend'] = false;
      #$this->aaTrends[$sp__]['sidewaystrend'] = false;
      #exit('uptrend');
    }

/*
    if ( # downtrend
      bccomp($aPrices['swing_lower_low'], $aPrices['swing_low']['y']['low']) < 0
    )
    {
      #$this->aaTrends[$sp__]['uptrend'] = false;
      $this->aaTrends[$sp__]['downtrend'] = true;
      #$this->aaTrends[$sp__]['sidewaystrend'] = false;
      #exit('downtrend');
    }

    if ( # sidewaystrend
      bccomp($aPrices['swing_higher_high'], $aPrices['swing_high']['y']['high']) < 0 &&
      bccomp($aPrices['swing_lower_low'], $aPrices['swing_low']['y']['low']) > 0
    )
    {
      #$this->aaTrends[$sp__]['uptrend'] = false;
      #$this->aaTrends[$sp__]['downtrend'] = false;
      $this->aaTrends[$sp__]['sidewaystrend'] = true;
      #exit('sidewaystrend');
    }

    else
    {
      $this->aaTrends[$sp__]['uptrend'] = false;
      $this->aaTrends[$sp__]['downtrend'] = false;
      $this->aaTrends[$sp__]['sidewaystrend'] = false;
    }
*/
    $aPrices['trends'] = $this->aaTrends[$sp__];

    return true;
  }

  protected function set_variables(&$aPrices)
  {
    $sp__ = $this->sPair;
    if (!isset($this->aaTrends)) $this->aaTrends = array();
    if (!isset($this->aaTrends[$sp__])) $this->aaTrends[$sp__] = array();
    if (!isset($this->aaTrends[$sp__]['uptrend'])) $this->aaTrends[$sp__]['uptrend'] = false;
    if (!isset($this->aaTrends[$sp__]['downtrend'])) $this->aaTrends[$sp__]['downtrend'] = false;
    if (!isset($this->aaTrends[$sp__]['sidewaystrend'])) $this->aaTrends[$sp__]['sidewaystrend'] = false;
    return true;
  }

}

?>