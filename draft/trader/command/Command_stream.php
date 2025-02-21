<?php

namespace Ziel\Providers\Oanda\Command;

use Ziel\Framework\Command;

use Ziel\Providers\Oanda\Controller\Controller_settings;
use Ziel\Providers\Oanda\Controller\Controller_sticks;
use Ziel\Providers\Oanda\Controller\Controller_expo;
use Ziel\Providers\Oanda\Controller\Controller_cache;
use Ziel\Providers\Oanda\Controller\Controller_pivots;
use Ziel\Providers\Oanda\Controller\Controller_swings;
use Ziel\Providers\Oanda\Controller\Controller_trade;
use Ziel\Providers\Oanda\Controller\Controller_api;

#
#use SQLite3;

/*
- Screen to use scroll:
screen ls
screen -r stream
CTRL + a; and after Esc; UP/DOWN and scroll will work.
Esc to exit

- screen to Detach:
CTRL + a + d;





/opt/local/lib/php73/extensions/no-debug-non-zts-20180731/
/usr/local/Cellar/php/7.4.11/pecl/20190902/


1:36
2:41

#03 #83

*/




/*
Todo:
  # files handler 
  # market handler 
  # stream handler 
  # error handler 

  - If units not in order, check out and close submit.
  - sessings.json
  - file handler
  - last 80 candlesticks for resume
  - setup.json similar to 00002 rename

*/

class Command_stream extends Command
{

  public function __construct()
  {
    $this->set_variables();
  }


  public function set_variables()
  {

    # class
    $this->sChunk = null;
    $this->iBytes = 3242880; #10000000; #3242880; #524288; 1048576; 2048000; 
    $this->sTempHandler = null;
    $this->sFilesPath = null;
    $this->rStreamHandler = null;


    # models
    $this->oApi = new Controller_api();
    //$this->oCache = new Controller_cache('stream');
    $this->oSettings = new Controller_settings();
    $this->oSticks = new Controller_sticks();
    $this->oExpo = new Controller_expo();
    $this->oSwings = new Controller_swings();
    $this->oPivots = new Controller_pivots();
    $this->oTrader = new Controller_trade();
    //register_shutdown_function(array($this, 'shutdown')); 
    bcscale(0);

  }

  public function shutdown() 
  {
    var_dump('shutdown');
    $this->configure(false, true);
  }

  public function set_prices_scale($aPrice) 
  {
    $aPrice = (array) $aPrice;
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

    return max(array($iScaleAsk, $iScaleBid));
  }

  public function configure($bCloseTrades = false, $bServer = true)
  {
    #configure
    var_dump('Start configure!');
    var_dump('start_stream - '. date('Y/m/d H:i:s'));

    # File handler
    if (!$this->files_handler()) exit('files_handler');

    # stream_server
    if ($bServer)
    {
      $this->stream_server($bCloseTrades, $bServer, true);
    }
    exit();
    var_dump('end_stream');
    return true;
  }
  
/** Configure */
  protected function stream_server($bCloseTrades = false, $bServer = true, $bResumeSession = false)
  {

    var_dump('start_stream_server');

    var_dump([
			get_class(),
			$bServer,
      'iElementsNo' => $this->oSticks->iElementsNo,
      #'iTimeChart' => $this->oSticks->iTimeChart
    ]);
/*
    if ($bCloseTrades)
    {
      var_dump('close_all_trades');
      if (!$this->oApi->close_all_trades()) var_dump('Error - close_all_trades');
    }

    if ($bResumeSession)
    {
      var_dump('resume_all_trades');
      if (!$this->oApi->resume_all_trades()) var_dump('Error - resume_all_trades');

      var_dump('file_session_resume'); # session.json fill
      $this->file_session_resume();
      print_r(PHP_EOL. json_encode(['count_session', count($_SESSION)]) .PHP_EOL);
    }
*/
    if ($bServer)
    {
      #if (!$this->oApi->stream()) {sleep(2); $this->configure(false, true);}
			$sJson = null;
      if (!$this->oApi->stream(array(get_class(), 'stream_client'))) {sleep(2); $this->configure(false, true);}
      //if (!$this->oApi->stream($sJson)) {sleep(2); $this->configure(false, true);}
    }

    #sleep(1);
    var_dump('end_stream_server');
    $this->configure(false, true);
    return false;
  }


/*
To do, Sofort.: 
  - ExpoMA liness starting Thursday - on Friday.
  - On weekends, the stream loop stops and opens late on Sundays.
*/
#,.,.,


  public function stream_client($sJson = '')
  {

    print_r(':');

    try {
      #print_r(','); #reading
      $aaRowForFile = array();
      #sChunk append
      $sJson = $this->sChunk.$sJson;
      $this->sChunk = '';
      $aJson = explode(PHP_EOL, $sJson);
      $this->sChunk = array_pop($aJson);
      $aaPrices = array_map(function ($sJson) { return json_decode($sJson, true); }, $aJson); 
      #prices
      $aaPrices = array_filter($aaPrices, function ($aPrices) {
        #if (!is_array($aPrices)) return false;
        if (!isset($aPrices['instrument'])) return false;
        #if (!isset($aPrices['time'])) return false;
        #if (empty($aPrices['closeoutAsk'])) return false;
        #if (empty($aPrices['closeoutBid'])) return false;
        return true;
      });
			
      if (empty($aaPrices)) print_r('_');
/*

return print_r ($aaPrices). PHP_EOL;
var_dump(array(
	'$aaPrices',
	$aaPrices
));
*/
      foreach ($aaPrices as $aPrice)
      {
        $sp__ = $this->sPair = $aPrice['instrument'];        
        $aStream = array(
          'sPair' => $sp__,
          'iScale' => $this->set_prices_scale($aPrice),
          'aPair' => $this->oSettings->aPairs[$sp__]
        );

        $aaPrice = array(
          'closeoutAsk' => $aPrice['closeoutAsk'],
          'closeoutBid' => $aPrice['closeoutBid'],
          'instrument' => $sp__,
          'time' => $aPrice['time'],
          'note' => array() 
        );

        #if (!$this->oSticks->sticks_stream_client($aaPrice, $aStream)) exit('sticks_stream_client');
        #if (!$this->oExpo->expo_stream_client($aaPrice, $aStream, false)) exit('expo_stream_client');
        #if (!$this->oPivots->pivots_stream_client($aaPrice, $aStream)) exit('pivots_stream_client');
        #if (!$this->oTrader->trade_stream_client($aaPrice, $aStream)) exit('trade_stream_client');

/*
.,:................................................,:......,:.........,:..........,:......,string(10) "stream_api"
string(17) "end_stream_server"
string(16) "Start configure!"
string(12) "start_stream"
string(19) "start_stream_server"
array(1) {
  ["iElementsNo"]=>
  int(80)
}
string(17) "resume_all_trades"
string(25) "Error - resume_all_trades"
string(19) "file_session_resume"

["count_session",105]
*/

        # note sticks for the view comparison
        $aaPrice['note'] = array_filter(array_merge(
          (isset($aaPrice['sticks']) ? (!empty($aaPrice['sticks']) ? array('sticks' => $aaPrice['sticks']) : array()): array()),
          #(isset($aaPrice['key']) ? array('key' => $aaPrice['key']): array() ),
          (isset($aaPrice['note']['reset']) ? array('reset' => $aaPrice['note']['reset']): array() ), # does not takew the market loop/ delete
          array()
        ));

        if (isset($aaPrice['market_order']))
        {
          var_dump(json_encode([
            str_repeat("write_market_order----", 2),
            $aaPrice,
          ]));

          #market
          $aOrder = array();
          #if ($aaPrice['market_order']['units'] > 0) $sMarketPrice = $aPrice['closeoutAsk']

          if(!$this->write_market_order($sp__, $aaPrice['market_order'], $aOrder, false)) exit('write_market_order');

          var_dump(
            '$aOrder',
            json_encode($aOrder)
          );  

          if (isset($aOrder["orderCreateTransaction"]))
          {

            $aaPrice['note'] = array_merge(
              array('o' => 1),
              array('iTimeChart' => $this->oSticks->iTimeChart),
              array('order' => $aOrder),
              array('market_order' => $aaPrice['market_order']),
              (isset($aaPrice['note']['sticks']) ? (!empty($aaPrice['note']['sticks']) ? array('sticks' => $aaPrice['note']['sticks']) : array()): array())
            );

            var_dump(
              'orderCreateTransaction',
              'note',
              $aaPrice['note']['order']["orderCreateTransaction"]
            ); 

            #$_SESSION[$sp__]['market_orders']['fulfilled'] = 1;

            if (!$this->update_session($aaPrice)) exit('update_session');
          }
          else
          {
            var_dump(json_encode([
              $aOrder,
              str_repeat("orderCreateTransaction ________________________________________________________________", 20),
              $aaPrice,
            ]));
          }
        } #end market_order

        # database
        unset($aaPrice['pivots']);
        unset($aaPrice['expo_line_0']);
        array_push($aaRowForFile, $aaPrice);
        # if (!$this->write_stream_prices($aaPrice)) exit('write_stream_prices');

        print_r('.');
      } # foreach ($aaPrices as $aPrice)
      
      if (!empty($aaRowForFile))
      {
        $asRowForFile = array_map('json_encode', $aaRowForFile);
        if (!$this->write_stream_prices(implode(PHP_EOL, $asRowForFile))) exit('write_stream_prices');
        print_r(',');
      }
      else
      {
        print_r('|');
      }
      #print_r('!');

/*
      $start_time = microtime(true); 
      if ((microtime(true) - $start_time) > 15)
      {
        $this->stream_server();
      }
   
      #print_r('-esc-');
*/
      return true;

    }
    catch (\Error $e) {
      var_dump($e->getMessage(), $e->getFile(), $e->getLine(), "\n\n", __FILE__. __FUNCTION__ . __LINE__);
      return $this->stream_server();
    }
    catch (\Throwable $e)
    {
      var_dump($e->getMessage(), $e->getFile(), $e->getLine(), "\n\n", __FILE__. __FUNCTION__ . __LINE__);
      return $this->stream_server();
    }

    return true;
  }
  # stream_client


  protected function write_market_order($sp__, $aMarketOrder, &$aOrder, $fPrice = 0)
  {
    $sp__ = $this->sPair;
/*
    $this->aTrade[$sp__]['single']['units'] = 0;
    $this->aTrade[$sp__]['single']['sell'] = false;
    $this->aTrade[$sp__]['single']['buy'] = false;
    $this->aTrade[$sp__]['single']['aPrice'] = $aPrice;
    $this->aTrade[$sp__]['single']['close'] = true;
    $this->aSignal[$sp__] = true;
*/
    if ($aMarketOrder['close'] == true)
    {
      #close_param
      $aOrder = $this->oApi->put_close_positions($sp__, $aMarketOrder['close_param']);
      return true;
    }

    if ($aMarketOrder['units'] == 0)
    {
      var_dump(json_encode([
        str_repeat("Units = 0 ________________________________________________________________", 20),
        $aMarketOrder['units'],
      ]));
      return true;
    }

    $aOrder = $this->oApi->post_market_order($sp__, $aMarketOrder['units'], $fPrice);

    return true;
  }

  protected function files_handler()
  {
    $this->sFilesPath = dirname(__DIR__). '/Store/';
    # Open files

    if (!isset($this->rSessionHandler))
    $this->rSessionHandler = fopen($this->sFilesPath .'/session.json', 'a+');

    if (!isset($this->rStreamHandler))
    $this->rStreamHandler = fopen(VAR_DIR .'/_store/stream.json', 'a+');

    return true;
  }

  protected function file_archive_temp_move()
  {

    # new temp file
    if (!is_dir(VAR_DIR .'_prices/'. date('Ymd'))) mkdir(VAR_DIR .'_prices/'. date('Ymd'));
    $this->sTempHandler = tempnam(VAR_DIR .'/_prices/'. date('Ymd'), date('His') .'_prices_');
    # Rename streams to temp file
    $bRename = rename((VAR_DIR .'/_store/stream.json'), $this->sTempHandler);
    # new stream file
    $this->rStreamHandler = fopen(VAR_DIR .'/_store/stream.json', 'a+');
    # if no stream data return true
    if (!file_exists(VAR_DIR .'/_store/stream.json')) return true;
    # error
    if (!$bRename) var_dump('bRename');
    else var_dump($this->sTempHandler);
  
    return true;

  }

  protected function write_stream_prices($ssPrice = null)
  {
    if (is_null($this->sFilesPath)) if (!$this->files_handler()) exit('files_handler');
    #$this->rStreamHandler = fopen(VAR_DIR .'/_store/stream.json', 'a+');
    $bWrite = fwrite($this->rStreamHandler, $ssPrice .PHP_EOL);
    fflush($this->rStreamHandler);
    if (!$bWrite) print_r('?????????????????');

    # clear the cache
    # clearstatcache();

    $aFstat = fstat($this->rStreamHandler);
    #fclose($this->rStreamHandler);
    if ($aFstat['size'] < $this->iBytes) return true;
    if (!$this->file_archive_temp_move()) exit('file_archive_temp_move');
    
    return true;
  }

  protected function update_session()
  {
    #if (empty($_SESSION)) return true;
    #if (!isset($this->rSessionHandler))
    #$this->rSessionHandler = fopen($this->sFilesPath .'/session.json', 'a+');

    fflush($this->rSessionHandler);
    $aFstat = fstat($this->rSessionHandler);
    $bTruncate = ftruncate($this->rSessionHandler, 0);
    if (!$bTruncate) exit('bTruncate_session');
    fwrite(
      $this->rSessionHandler,
      json_encode($_SESSION)
    );
    fflush($this->rSessionHandler);
    #fclose($this->rSessionHandler);

    return true;
  }

  protected function file_session_resume()
  {
    if (empty($_SESSION)) return $this->update_session();

    #if (!isset($this->rSessionHandler))
    #$this->rSessionHandler = fopen($this->sFilesPath .'/session.json', 'a+');

    $aFstat = fstat($this->rSessionHandler);
    if ($aFstat['size'] == 0) {/*fclose($this->rSessionHandler);*/ return $this->update_session();}

    rewind($this->rSessionHandler);
    $sSession = fread($this->rSessionHandler, $aFstat['size']);
    $aSession = json_decode($sSession, true);
    fflush($this->rSessionHandler);
    #fclose($this->rSessionHandler);

    foreach ($_SESSION as $sInstruments => &$aMarket)
    {
      if (!isset($aMarket['market_orders']['aPrice'])) continue;
      if (!isset($aSession[$sInstruments]['market_orders']['aPrice'])) continue;

      # write array with prices for trader instead of the close price
      $aMarket['market_orders']['aPrice'] = $aSession[$sInstruments]['market_orders']['aPrice'];

/*
      if ($aMarket['market_orders']['fulfilled'] == 0)
      {
        #if ($_SESSION[$sp__]['market_orders']['fulfilled'] == 1) {$this->update_session(); continue;}
        print_r(implode(' - ', [
          'fulfilled == 0',
          str_pad($sInstruments, 14),
          str_pad('buy'. $aMarket[$sp__]['market_orders']['buy'], 20),
          str_pad('sell'. $aMarket[$sp__]['market_orders']['sell'], 20),
          str_pad('units'. $aMarket[$sp__]['market_orders']['units'], 20),
        ]) .PHP_EOL);
      }
*/
    }

    $this->update_session();

    return true;
  }

  public function __destruct()
  {
    if (isset($this->rStreamHandler)) if (is_resource($this->rStreamHandler)) fclose($this->rStreamHandler);
    if (isset($this->rSessionHandler)) if (is_resource($this->rSessionHandler)) fclose($this->rSessionHandler);

    #if (isset($this->rVerbose)) if (is_resource($this->rVerbose)) fclose($this->rVerbose);
  }
}



?>