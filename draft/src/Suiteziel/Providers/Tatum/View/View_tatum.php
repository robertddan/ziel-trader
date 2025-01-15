<?php

namespace App\Suiteziel\Providers\Tatum\View;


use App\Suiteziel\Framework\View;
#
use App\Suiteziel\Providers\Tatum\Controller\Controller_wallet;
use Tatum\Tatum;
#
use Twig\Loader\ArrayLoader;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
#


class View_tatum extends View
{

  public function __construct($aUri = false)
  {
    $this->aUri = $aUri;
    if (!$this->set_variables()) exit('set_variables');
  }

  public function set_variables()
  {
    # template
    $this->oTwig = new Environment(new FilesystemLoader(dirname(__DIR__) .'/Template/'), ['debug' => true]);
    $this->oTwig->addExtension(new \Twig\Extension\DebugExtension());
    
    # setup
    #$this->oCache = new Controller_cache('view');
    $this->oWallet = new Controller_wallet();
    
    #$this->oTrade = new Model_trade();
    #$this->oApi = new Model_api();
    return true;
  }

  public function index()
	{




    $Tatum = new Tatum();
$coin = "BTC";
$wallet = $Tatum->generateWallet($coin); 
echo $wallet;

$walletX = json_decode($wallet);

echo "<hr/>";
echo $Tatum->generatePrivateKey($coin, $walletX->mnemonic, '0');

echo "<hr/>";
echo $Tatum->generateAddressFromXPub($coin, $walletX->xpub, '0');

echo "<hr/>";



exit();
    $_start = microtime(true);

    
echo '<pre>';
#var_dump(($this->aaView));




echo '</pre>';

    # debugs
    $_debug = array(
      #$_cache,
      $this->convert(memory_get_usage(true)), 
      microtime(true) - $_start
    );
    foreach ($_debug as $i => $v) print "[$i] $v  //  ";
    #

    return print $this->oTwig->render('index.html.twig', array(

    ));

  }

  public function convert($size)
  {
    // https://www.php.net/manual/en/function.memory-get-usage.php
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
  }

  public function __destruct()
  {
    #session_destroy();
    #$this->db->close();
  }
}


?>