<?php

namespace App\Suiteziel\Providers\Oanda\Command;

use App\Suiteziel\Framework\Command;
use SQLite3;
#--#


class Command_sqlite extends Command
{
	#one
  public $sNamePrefix = null; #
  public $iBytes = 5098576000; #1098576000; #524288000;  # 1 GB, 500 MB
  public $iRetry = 60; #seconds
  
  public $sFolder = '20220922'; # *

  public function __construct()
  { 

  }

/** Configure */
  public function configure()
  {
    $aFilePath = glob(VAR_DIR .'_prices/'. $this->sFolder .'/*_prices_*');
 
    if (empty($aFilePath)) $this->sleep_and_retry($aFilePath);

    if (is_null($this->sNamePrefix)) $this->sNamePrefix = array_reverse(explode('/', $aFilePath[0]))[1];

    $iNoSimilarFiles = count(glob(VAR_DIR .'_databases/'.  $this->sNamePrefix .'_*.db'));
    $aJournalsTemp = glob(VAR_DIR .'_databases/'.  $this->sNamePrefix .'_*.db-journal');#liquidity_00000003.db-journal
    if(!empty($aJournalsTemp)) foreach ($aJournalsTemp as $sJournal) unlink($sJournal);

    if ($iNoSimilarFiles !== 0) $iNoSimilarFiles = ($iNoSimilarFiles - 1);
    $this->sName = $this->sNamePrefix .'_' . str_pad($iNoSimilarFiles, 8, '0', STR_PAD_LEFT) .'.db';

    if (filesize(VAR_DIR .'_databases/'.  $this->sName) >= $this->iBytes) $iNoSimilarFiles = ($iNoSimilarFiles + 1); #2
    #else $iNoSimilarFiles = bcsub($iNoSimilarFiles, 1);
    $this->sName = $this->sNamePrefix .'_' . str_pad($iNoSimilarFiles, 8, '0', STR_PAD_LEFT) .'.db';
    if (!file_exists(VAR_DIR .'_databases/'.  $this->sName)) touch(VAR_DIR .'_databases/'.  $this->sName);
    var_dump($this->sName."\n______________file");
    
    $aCopyDatabases = glob(VAR_DIR .'_databases/_*');

/*
    foreach ($aCopyDatabases as $sCopy) {
      $sInWork = array_reverse(explode('/', $sCopy))[0];
      if ($sInWork == '_'.$this->sName) continue; 
      else unlink($sCopy);
    }
*/

    $this->db = new SQLite3(VAR_DIR .'_databases/'.  $this->sName, SQLITE3_OPEN_READWRITE); 
    if (!$this->db) throw new Exception("Error Processing new SQLite3 Request", 1);
    $this->db->busyTimeout(0);
    #execute
    $this->execute($aFilePath);
    return true;

  }

  protected function execute($aFilePath = false)
  {
    var_dump('_______________________________________ execute');

    foreach ($aFilePath as $sSetupPath) 
    {
      #$sBuffer = file_get_contents($sFile);
      #if (empty($sBuffer)) continue;
      #$oSetup = json_decode($sBuffer);
      #if (empty($oSetup)) continue;
      #$sSetupPath = dirname(__DIR__) .'/Store/'. ltrim($oSetup->temp, '/');

      if (!file_exists($sSetupPath)) continue;

var_dump(filesize($sSetupPath)."______________ filesize($)");

      $this->db->exec("CREATE TABLE IF NOT EXISTS streams (stream text)");
      $this->db->exec("INSERT INTO streams VALUES (zeroblob(". filesize($sSetupPath) ."))");
      $iRow_id = $this->db->querySingle(" select last_insert_rowid();");
      if ($iRow_id == 0) $iRow_id = 1;

      $this->rStreamHandler = $this->db->openBlob('streams', 'stream', ($iRow_id), 'main', SQLITE3_OPEN_READWRITE);
      if (!$this->rStreamHandler) throw new Exception("Error Processing openBlob Request", 1);

      var_dump($this->sName."\n______________fread");

var_dump(
  array_reverse(explode('/', $sSetupPath))[0]
);
#exit();
      $bWritted = fwrite($this->rStreamHandler, file_get_contents($sSetupPath), filesize($sSetupPath));
      #if (!is_dir(VAR_DIR .'_tmp/'. $this->sNamePrefix)) mkdir(VAR_DIR .'_tmp/'. $this->sNamePrefix);

      fclose($this->rStreamHandler);
      #if ($bWritted) unlink($sSetupPath);
/*
      if (copy(
        VAR_DIR .'_databases/'.  $this->sName,
        VAR_DIR .'_databases/_'. $this->sName
      )) var_dump('Copy '. VAR_DIR .'/_databases/_'. $this->sName);
      else exit('copy '. $this->sName);
*/
/*
      if (!is_dir(VAR_DIR .'_store/'. date('Ymd'))) mkdir(VAR_DIR .'_store/'. date('Ymd'));
      if (@rename(
        $sSetupPath,
        VAR_DIR .'_store/'. date('Ymd') .'/'. array_reverse(explode('/', $sSetupPath))[0]
      )) var_dump('Copy done: '. VAR_DIR .'/_databases/_'. $this->sName);
      #else var_dump('unlink '. $sSetupPath);
*/
      if (filesize(VAR_DIR .'_databases/'.  $this->sName) >= $this->iBytes) break;
    }
    
   
    var_dump($this->sName."______________close");
    
    print_r("\nTask done! Retry in: ". $this->iRetry ."seconds.\n\n");

    $this->db->close();

    #$this->configure();

  }

  public function sleep_and_retry($aFilePath)
  {
    for($i = 0; $i <= $this->iRetry; $i++)
    {
      print_r('#'.$i .' ');
      sleep(1);
      if (!empty(glob(VAR_DIR .'_prices/*/*_prices_*'))) $this->configure();
    }
    sleep(1);
    print_r('#restart ');
    $this->sleep_and_retry($aFilePath);
  }

  public function __destruct()
  {
    if (isset($this->db)) $this->db->close();
    if (isset($this->rStreamHandler)) if (is_resource($this->rStreamHandler)) fclose($this->rStreamHandler);
  }
}

?>