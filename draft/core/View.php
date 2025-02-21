<?php

namespace Ziel\Framework;

class View
{
  protected $aUri; # boo

  public function __construct()
  {
    # memory
    
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);
    ini_set('memory_limit', -1);
    ini_set('ignore_user_abort', false);
    ini_set('max_execution_time', 3600);
    # trader
    // ini_set('trader.real_precision', 8);
    # xdebug
    // ini_set('xdebug.max_nesting_level', 14000);
    // ini_set('xdebug.var_display_max_depth', '10');
    // ini_set('xdebug.var_display_max_children', '256');
    // ini_set('xdebug.var_display_max_data', '1024');
    # bc math scale
    #call_user_func('bcscale', 0);
    # session
    #if (session_status() !== 2) session_start();

    if (!$this->run()) exit();

  }

  public function run()
  {
    $this->aUri = array_filter(explode('/', $_SERVER['REQUEST_URI']));
    if (empty($this->aUri)) return print "🌱🌱🌱";
/*
echo '<pre>';
var_dump([
  $this->aUri,
  $this->aUri[2]
  //$sNamespace
]);
echo '</pre>';
exit();
*/
		if ($this->aUri[1] == "favicon.ico") {
			header('Content-Type: image/x-icon');
			return print file_get_contents(ROOT . 'public/favicon.ico');
		} 
		elseif (!isset($this->aUri[2])) {
		    exit('View -> uri');
		}
		elseif ($this->aUri[3] == 'assets') {
			$sAssetFile = ROOT . 'draft/providers/oanda/Template/';#. $this->aUri[3] .'/'. $this->aUri[4] .'/'. $this->aUri[5] .'/'. $this->aUri[6];
			$sAssetFile .= implode("/", array_slice($this->aUri, 2));
			$sExtension = pathinfo($sAssetFile, PATHINFO_EXTENSION);
			return var_dump([$sAssetFile, $sExtension]);
			$oHandleAssets = fopen($sAssetFile, "r");
			$sContents = fread($oHandleAssets, filesize($sAssetFile));
			fclose($oHandleAssets);
			if ($sExtension == "css") header('Content-Type: text/css');	
			elseif ($sExtension == "js") header('Content-Type: application/javascript');
			elseif ($sExtension == "svg") header('Content-Type: image/svg+xml');
			elseif ($sExtension == "png") header('Content-Type: image/png');
			else header('Content-Type: text/html; charset=utf-8');
			return print $sContents;
		}
		elseif ($this->aUri[3] == 'api') {
			$sNamespace = implode("\\", array(
				"App\Suiteziel",
				ucfirst($this->aUri[1]),
				ucfirst($this->aUri[2]),
				"View\View_".
				lcfirst($this->aUri[2])
			));
			
			header('Content-Type: application/json');
			if (!class_exists($sNamespace)) exit(json_encode(array($sNamespace, __FILE__)));
			$oNamespace = new $sNamespace($this->aUri);
			return print $oNamespace->api();
		}
		else {
			// $this->aUri = must add GET parameters to send in the contraoller
			// FIX URI shortcut
			$sNamespace = implode("\\", array(
				"Ziel",
				ucfirst($this->aUri[1]),
				ucfirst($this->aUri[2]),
				"View\View_".
				lcfirst($this->aUri[2])
			));
			if (!class_exists($sNamespace)) exit($sNamespace . ' in: <b>'. __FILE__ .'</b>');
			$oNamespace = new $sNamespace($this->aUri);
			#$sViewFunction = $this->aUri[3];
			$sViewFunction = 'index';
			return $oNamespace->$sViewFunction();
		}
    }
}

