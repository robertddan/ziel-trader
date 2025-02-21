<?php

namespace Ziel\Providers\Oanda\Controller;


use Ziel\Framework\Controller;
use Ziel\Providers\Oanda\Controller\Controller_cache;
use SQLite3;

class Controller_prices extends Controller
{
  # Class
  public $aUri;
  public $sPair;
  public $aPrices = array();
  public $aPairs;

  #Database
  public $sDbName = '20220922_00000000.db';
  # 
  public $sFrom = '2020-11-19 19:19:00';
  public $sTo = '2020-11-20 20:20:00';
  #view
  public $sUriFrom;
  public $sUriTo;
  #
  public $bWriteCache = 0; # delete cache
  public $bReadFile = 1; #enabled if bWriteCache
  public $bWriteFile = 0; #delete old tmp file

/*
TODO
- save from database to cache directly
*/

  public function __construct($aUri = false)
  {
    $this->aUri = $aUri;
    $this->oCache = new Controller_cache('prices');
    $this->set_variables();
    $this->get_prices();
  }

  public function set_variables()
  {
    # URI
    if (!empty($this->aUri[3])) $this->sPair = strtoupper($this->aUri[3]);
    if (!empty($this->aUri[4])) $this->sUriFrom = date('Y-m-d H:i:s', strtotime($this->aUri[4]));
    if (!empty($this->aUri[5])) $this->sUriTo = date('Y-m-d H:i:S', strtotime($this->aUri[5]));
  }

/** View */

  public function get_prices()
  {

    $sp__ = $this->sPair;
    $CachedString = $this->oCache->cm__->getItem($sp__);
    if ($this->bWriteCache) var_dump(array('delete cache', $this->oCache->cm__->deleteItem($sp__)));
    #var_dump(($sPathTempFile));
    #exit(__FILE__);
    if (!$CachedString->isHit()) {
        # db_prices
        $this->aPrices[$sp__] = $this->db_prices($this->sPair);
        # save cache
        $CachedString->set($this->aPrices[$sp__])->expiresAfter(2592000);//in seconds, also accepts Datetime
        $this->oCache->cm__->save($CachedString); // Save the cache item just like you do with doctrine and entities
        $_cache = 'database';

    } else {
        $_cache =  'cache';
        $this->aPrices[$sp__] = $CachedString->get(); // Will print 'First product'
        #if (is_null($this->aPrices[$sp__]))

    }

    #$aPrices = $this->db_prices($this->sPair);
    #$this->aPrices = $aPrices; #array($this->sPair => $aPrices);
  }

  public function db_prices()
  {
    try 
    {
      $sp__ = $this->sPair;
      # get 
			
			//$sDirName = explode(".", $this->sDbName)[0];
      $sPathTempDir = VAR_DIR .'_tmp/'.  explode(".",$this->sDbName)[0]; //$this->sDbName;

var_dump($this->sDbName);
			
      if (!is_dir($sPathTempDir)) mkdir ($sPathTempDir); #explode('.', $sPathTempDir)[0]
      #$sPathTempFile = glob($sPathTempDir .'/'. $this->sPair .'*.tmp');
      $sPathTempFile = $sPathTempDir .'/'. $this->sPair .'.tmp'; 

      $aPrices = array();

      if (file_exists($sPathTempFile)) if ($this->bReadFile) if (filesize($sPathTempFile) <= 0) unlink($sPathTempFile);
      if (file_exists($sPathTempFile)) if ($this->bWriteFile) unlink($sPathTempFile);

      if (!file_exists($sPathTempFile))
      {

        $this->db = new SQLite3(VAR_DIR .'_databases/'.  $this->sDbName);
        #$this->db->querySingle("VACUUM;"); # clear I guess; after review
        $lastInsertRowID = $this->db->querySingle("SELECT COUNT(*) as rows_count FROM streams;");

        for ($i = 1; $i <= $lastInsertRowID; $i++) ####
        {
          $stream = $this->db->openBlob('streams', 'stream', $i, 'main', SQLITE3_OPEN_READONLY);
          $s_tmp_ = '';
          #while (!feof($stream)) $s_tmp_ .= fgets($stream, 8196);
          #foreach(explode(PHP_EOL, $s_tmp_) as $streamline)
          #foreach(explode(PHP_EOL, stream_get_contents($stream)) as $streamline)
          #foreach(explode(PHP_EOL, fgets($stream, 4096)) as $streamline)

          while ($s_tmp_ = fgets($stream))
          {
            $atemp = json_decode($s_tmp_, true);

            if (empty($atemp)) continue; #{var_dump($streamline); exit();}
            if ($atemp["instrument"] !== $this->sPair) continue;
            # between, time
            $aTimeSplit = explode(".", str_replace('Z', '', $atemp['time']));
            $iTimeDate = strtotime(str_replace('T', ' ', $aTimeSplit[0]));
            //if ($iTimeDate < strtotime($this->sFrom)) continue;
            //if ($iTimeDate > strtotime($this->sTo)) break;

/*
echo '<pre>';

var_dump($lastInsertRowID);
var_dump(array(
  $i, 
  $s_tmp_
));


# remote prev added stuff
unset($atemp["note"]);
unset($atemp["open"]);
unset($atemp["high"]);
unset($atemp["low"]);
unset($atemp["close"]);
unset($atemp["asks"]);
unset($atemp["bids"]);
unset($atemp["mids"]);
unset($atemp["swings_high"]);
unset($atemp["swing_low"]);
unset($atemp["swing_high"]);
unset($atemp["swing_high"]);
unset($atemp["swing_lower_low"]);
unset($atemp["swing_higher_high"]);
# remote prev added stuff
*/

            array_push(
              $aPrices, 
              array(
                'time' => $atemp['time'],
                'instrument' => $atemp['instrument'],
                'closeoutAsk' => $atemp['closeoutAsk'],
                'closeoutBid' => $atemp['closeoutBid']
            ));

          }
        }
        fclose($stream);
        $this->db->close();
      }

      var_dump(count($aPrices));
      if (!empty($aPrices))
      {
        $rTempFile = fopen($sPathTempFile, 'a+');
        rewind($rTempFile); 
        ftruncate($rTempFile, 0);
        fwrite($rTempFile, serialize($aPrices));
        fclose($rTempFile); 
        var_dump('DATABASE');

				
        # _session for option bellow
        #$_SESSION[$sp__]['aPrices'] = $aPrices;
        #$_SESSION[$sp__]['sDbName'] = $this->sDbName;
        # return
        #$this->aPrices = $aPrices;
      }

      elseif (empty($aPrices)) 
      {
        /*
        if (isset($_SESSION[$sp__]['sDbName'])) 
        if ($_SESSION[$sp__]['sDbName'] !== $this->sDbName)
        {
          $_SESSION[$sp__]['aPrices'] = array();
          $_SESSION[$sp__]['sDbName'] = $this->sDbName;
        }

        if (!empty($_SESSION[$sp__]['aPrices'])) 
        {
          $aPrices = $_SESSION[$sp__]['aPrices'];
          $_SESSION[$sp__]['sDbName'] = $this->sDbName;
        }
        else
        {
        */

#var_dump(['$sPathTempFile' => $sPathTempFile]);

				
          $rTempFile = fopen($sPathTempFile, 'a+');
          $sTmpFile = '';
          while (!feof($rTempFile)) $sTmpFile .= fgets($rTempFile, 8196);

          #$sTmpFile = file_get_contents($sPathTempFile); 
          #$sTmpFile = fgets($rTempFile); 
          $aPrices = unserialize($sTmpFile);
          fclose($rTempFile); 

          #$_SESSION[$sp__]['aPrices'] = $aPrices;
          #$_SESSION[$sp__]['sDbName'] = $this->sDbName;
        #}

        var_dump('_SESSION');
      }
#var_dump(['$aPrices' => $aPrices]);
      if (empty($aPrices)) exit('db_prices');

      return $aPrices;

    }
    catch (\Error $e) {
      var_dump('Error-catch');
      var_dump($e->getMessage());
      exit('db_prices');
    }
  }

}