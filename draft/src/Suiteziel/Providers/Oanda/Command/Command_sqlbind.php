<?php

namespace App\Suiteziel\Providers\Oanda\Command;

use App\Suiteziel\Framework\Command;
use SQLite3;
#--#


class Command_sqlbind extends Command
{
	#one
  public $db;
  public $sName; 
  public $sNamePrefix = '202011'; 
  public $rTempHandler;
  
  public function __construct()
  { 

  }

/** Configure */
  public function configure()
  {


    $this->sName = $this->sNamePrefix .'_binded.db';

#if (!is_dir(VAR_DIR .'_prices/'. date('Ym'))) mkdir(VAR_DIR .'_prices/'. date('Ym'));
#$b = tempnam(VAR_DIR .'/_prices/'. date('Ym'), date('dHi') .'_prices_');
#var_dump((date('dHi') .'_prices_'));
#var_dump(glob(VAR_DIR .'_prices/*/*_prices_*'));
#exit();

    if (!file_exists(VAR_DIR .'_databases/'.  $this->sName)) touch(VAR_DIR .'_databases/'.  $this->sName);
    $this->db = new SQLite3(VAR_DIR .'_databases/'.  $this->sName, SQLITE3_OPEN_READWRITE); 
    $this->db->exec("CREATE TABLE IF NOT EXISTS streams (stream text)");
    $this->db->querySingle("VACUUM;");
    $this->db->busyTimeout(0);

    #execute
    $this->execute();
    return true;

  }

  protected function execute()
  {
    try
    {   
      var_dump('_______________________________________ execute');

      $iNoSimilarFiles = glob(VAR_DIR .'_databases/'.  $this->sNamePrefix .'_*.db');

      foreach ($iNoSimilarFiles as $i => $sPathFile)
      {
        #if ($i >= 3) break;
        var_dump($sPathFile);
        $this->rSetupHandler = new SQLite3($sPathFile, SQLITE3_OPEN_READONLY); 
        #$this->rSetupHandler->openBlob('streams', 'stream', 1, 'main', SQLITE3_OPEN_READWRITE);
        $result = $this->rSetupHandler->query('SELECT * FROM streams');

        while($row = $result->fetchArray()){
      

          $this->db->exec("INSERT INTO streams VALUES (zeroblob(". strlen($row['stream']) ."))");
          $lastInsertRowID = $this->db->querySingle("select last_insert_rowid()");
    #var_dump($lastInsertRowID);
          $this->rStreamHandler = $this->db->openBlob('streams', 'stream', $lastInsertRowID, 'main', SQLITE3_OPEN_READWRITE);
          if (!$this->rStreamHandler) throw new Exception("Error Processing openBlob Request", 1);
    #exit();
          #var_dump(gettype($row['stream']));
          if (fwrite($this->rStreamHandler, $row['stream'])) print_r('.');
          else var_dump(PHP_EOL. "______________ PHP_EOL ". $row['stream']);
        }

        fclose($this->rStreamHandler);
        $this->rSetupHandler->close();
        #var_dump($this->rSetupHandler);

        var_dump(PHP_EOL);
      }

      $this->db->close();
      var_dump('_______________________________________ close');
      return true;
    }
    catch (Exception $e)
    {
      var_dump($e->getMessage());
    }
  }
/** Configure */



  public function __destruct()
  {
    #file handler
    @fclose($this->rSetupHandler);
  }
}

?>