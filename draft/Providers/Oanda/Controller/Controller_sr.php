<?php

namespace App\Suiteziel\Providers\Oanda\Controller;

use App\Suiteziel\Framework\Controller;


class Controller_sr extends Controller
{

	public function sr_client(&$aPrice, $aView)
  {
    foreach ($aView as $k => $a) $this->{$k} = $a;
    bcscale($this->iScale);

    if (!$this->set_variables($aPrice)) exit('set_class_variables');
    if (!$this->sr_framework($aPrice)) exit('sr_framework');

    return true;
  }
  

  protected function sr_framework(&$aPrices)
  {
    $sp__ = $this->sPair;
#var_dump($aPrices);
#exit();
    return true;
  }

  protected function set_variables(&$aPrices)
  {
    $sp__ = $this->sPair;
    if (!isset($this->aaSr)) $this->aaSr = array();
    if (!isset($this->aaSr[$sp__])) $this->aaSr[$sp__] = array();
    return true;
  }
}

?>