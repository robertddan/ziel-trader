<?php

/*

*/

namespace App\Core\db;

class Dataziel {
	
	public $sPath;
	public $sTable;
	public $iBytes;
	
	private $sRows;
	private $sColumn;
	
	
	function __construct($sPath, $sTable) {
		$this->sPath = $sPath;
		$this->sTable = $sTable;
		var_dump('-Sockets');
	}
	
	function invoking_callouts($sPrices) {
		$aPrices = json_decode($sPrices);
		
		
		# Path
		if (is_null($this->sRows)) $this->sRows = date('His');
		if (is_null($this->sColumn)) $this->sColumn = date('Ymd');
		
		# Open files
		$sFile = $this->sPath .'/'. $this->sTable .'/'. $this->sColumn .'/'. $this->sRows;
		$this->rStreamHandler = fopen($sFile, 'w');
		$bWrite = fwrite($this->rStreamHandler, $sPrices);
		fflush($this->rStreamHandler);
		if (!$bWrite) return false;
		
		# Rename
		$aFstat = fstat($this->rStreamHandler);
		if ($aFstat['size'] < $this->iBytes) $this->sRows = null;
		if ($this->sColumn < date('Ymd')) $this->sColumn = null;
		#fclose($this->rStreamHandler);
		
		return true;
	}
}

?>