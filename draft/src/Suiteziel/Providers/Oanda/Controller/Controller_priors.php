<?php

namespace App\Suiteziel\Providers\Oanda\Controller;

use App\Suiteziel\Framework\Controller;
use App\Suiteziel\Providers\Oanda\Controller\Controller_sticks;

# prior sticks controller
class Controller_priors extends Controller
{

  public $bFocused = false;

	public function prior_sticks_client($aaView, $aView)
  {
    $this->aaView = $aaView;
    $this->aView = $aView;
    foreach ($aView as $k => $a) $this->{$k} = $a;

    $this->oSticks = new Controller_sticks();

    if (!$this->fast_transform()) exit('fast_transform');

    return true;
  }

	public function fast_transform()
  {
    echo '<pre>';
    $i = 0;

/*
    $arr = array('A','B','C');
    array_splice($arr, 2, 0, array('X','Y'));
    var_dump($arr);
    exit();
*/

/*
- loop aaView k as aprice
  + If (iTime + 5min + 59sec) < iGap
    + Log last time
    + iGap / 5min = time, merge pattern-price,
    Splice array 
      & stream = log, if past 5min, …
  Else
  Sticks c
-end loop aaView
*/

    $sp__ = $this->sPair;
    if (!isset($this->aiLastKey[$sp__])) $this->aiLastKey[$sp__] = 0;
    if (!isset($this->aiLastTime[$sp__])) $this->aiLastTime[$sp__] = 0;
    if (!isset($this->aaLastPrice[$sp__])) $this->aaLastPrice[$sp__] = array();


/*
iDateFirstPrice
fix :00 or s = 0
add 60*iChart
iDateLastPrice
*/

    $e = 0;
    foreach($this->aaView as $k__ => &$aPrice) 
		{

/*
- verifica data, daca e mai mare ca ultima cu x-minute.
- gatp / charttime * 60


*/


if (
  #$k__ < 110 ||
  $k__ > 1500
  #false
)
{
  unset($this->aaView[$k__]);
  continue;
}


$aTimeSplit = explode(".", str_replace('Z', '', $aPrice['time']));
$iTimeDate = strtotime(str_replace('T', ' ', $aTimeSplit[0]));


if (!$this->bFocused) if (($this->isMultipleof(date('i', $iTimeDate), $this->iTimeChart)) || (intval(date('i', $iTimeDate)) == 0 )) 
{
  # for update fix: (intval(date('s', $iTimeDate)) == 0 )) -- wait
  $this->bFocused = true;
  $this->aiTimeKey[$sp__] = strtotime(date('Y-m-d H:i:00', $iTimeDate));
}
else 
{
  continue;
}


$this->aiTimeKey[$sp__] = strtotime(date('Y-m-d H:i:00', $iTimeDate));

$iGapTime = $iGap = $aSplice = 0;
if ($this->aiLastTime[$sp__] == 0) $iGapTime = $iGap = $aSplice = 0;
else $iGapTime = bcsub(strtotime(date('Y-m-d H:i:s', $iTimeDate)), $this->aiLastKey[$sp__], 0);

      if (bccomp(bcadd(bcmul($this->iTimeChart, 60, 0), 59, 0), $iGapTime, 0) <= 0)
      {
        # generate empty sticks (-) until the queue
        #+ iGap / 5min = time, merge pattern-price,
        # $this->aiTimeKey[$sp__]

        $iGap = bcdiv($iGapTime, 60, 0);
/*
        $aSplice = array();
        for ($i = 0; $i <= $iGap; $i++)
        {
          $this->aiTimeKey[$sp__] = bcadd($this->aiLastKey[$sp__], bcmul($this->iTimeChart, 60, 0), 0);
          array_push(
            $aSplice,
            array(
              'open' => $this->aaLastPrice[$sp__]['open'],
              'high' => $this->aaLastPrice[$sp__]['high'],
              'low' => $this->aaLastPrice[$sp__]['low'],
              'close' => $this->aaLastPrice[$sp__]['close'],
              'note' => array(date('Y-m-d H:i:s', $this->aiTimeKey[$sp__])),
              'asks' => $this->aaLastPrice[$sp__]['closeoutAsk'],
              'bids' => $this->aaLastPrice[$sp__]['closeoutBid'],
              'mids' => bcdiv(bcadd($this->aaLastPrice[$sp__]['closeoutAsk'], $this->aaLastPrice[$sp__]['closeoutBid'], $this->iScale), 2, $this->iScale),
              'key' => $this->aiTimeKey[$sp__],
              'closeoutAsk' => $this->aaLastPrice[$sp__]['closeoutAsk'],
              'closeoutBid' => $this->aaLastPrice[$sp__]['closeoutBid'],
            )
          );
        }
*/
        #array_splice($this->aaView, $k__, 0, $aSplice);
        var_dump([$iGapTime, $iGap]);

        # update iTimeDate to current time
        # continue;
        $iGapTime = $iGap = $aSplice = 0;

      }
      else
      {
        # 1. sticks
        if (!$this->oSticks->sticks_view_client($aPrice, $this->aView, false, $k__)) exit('sticks_view_client');

      }

$this->aiLastKey[$sp__] = $this->aiTimeKey[$sp__];
$this->aiLastTime[$sp__] = $iTimeDate;
$this->aaLastPrice[$sp__] = $aPrice;

/*
      var_dump([
        date('Y-m-d H:i:s', $this->aiLastTime[$sp__]),
        date('Y-m-d H:i:s', $iTimeDate),
        $aPrice,
        $iGapTime,
        $iGap,
        $aSplice
      ]);
*/
#exit();
      



      # !important
      #if (isset($aPrice["close"])) if ($aPrice["close"] == 0) {unset($this->aaView[$k__]); continue;} #{unset($aPrice); continue;} #
      if (!isset($aPrice["close"])) {unset($this->aaView[$k__]); continue;} #{unset($aPrice); continue;} #
      #if (is_null($aPrice["close"])) {unset($this->aaView[$k__]); continue;} #{unset($aPrice); continue;} #

      #if (!$this->oExpo->expo_view_client($aPrice, $this->aView, false)) exit('expo_view_client');
      #if (!$this->oTrade->trade_view_client($aPrice, $aView, false)) exit('trade_view_client');

      # chart
      #if (!$this->set_chart_prices($aPrice, $this->aView)) exit('set_chart_prices');
      #if ($i == 5) break;

/*
      var_dump([
        date('Y-m-d H:i:s', $this->aiLastTime[$sp__]),
        date('Y-m-d H:i:s', $iTimeDate),
        $aPrice['time'],
        $iGapTime,
        $k__
      ]);
*/


      $i++;

    }
echo '</pre>';
#

    return true;
  }

	public function time_gaps(&$aPrice, $aView)
  {
    return true;
  }

  function isMultipleof ($iM, $iF)
  {
    while ( $iM > 0 ) $iM = $iM - $iF;
    if ( $iM == 0 ) return true;
    return false;
  }

}

?>