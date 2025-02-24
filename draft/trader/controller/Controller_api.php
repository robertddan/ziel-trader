<?php

namespace Ziel\Providers\Oanda\Controller;

use Ziel\Framework\Controller;
use Ziel\Providers\Oanda\Controller\Controller_settings;

class Controller_api extends Controller
{


/* configure */
  public function __construct()
  {
    $this->set_variables();
  }

  public function configure($bCloseTrades = true)
  {
    # set variables pairs 
    #$this->set_variables();
    #execute
    #$this->stream();
    #$this->close_all_trades();
  }
/* end configure */

/* cURL setup */

  public function stream_handler_errors($ch)
  {

    # get function name from error and {} execute on again
    if (curl_errno($ch) !== 0)
    {
      var_dump(curl_errno($ch));
      sleep(5);
      var_dump(curl_errno($ch));
      call_user_func_array(array($this->aCallback[0], 'configure'), array(false, true));
    }
/*
    print_r(array([
      '$stream_handler_errors',
      #$ch,
      curl_getinfo($ch, CURLINFO_HTTP_CODE),
      #curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD),
      #curl_errno($ch),
      #curl_error($ch),
      #in_array(curl_errno($ch), array(CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED)),
      #CURLE_OPERATION_TIMEDOUT,
      #curl_getinfo($ch)
    ]));
*/
    return true;
  }
	
	
  public function stream_handler($ch, $sJson)
  {
    #call_user_func(array($this, 'stream_handler_errors'), $ch);
    try
    {

/*
  $a = file_get_contents(__DIR__ . '/stream.txt');
  $rStream = fopen(__DIR__ . '/stream.txt', 'r');
  $a = fgets($rStream);
  fclose($rStream);
  var_dump([$a, $sJson]);
  return strlen($sJson);
*/

      call_user_func(array($this, 'stream_handler_response'), $sJson);
      return strlen($sJson);
    } 
    catch (\Error $e) {
      var_dump($e->getMessage(), $e->getFile(), $e->getLine(), "\n\n", __FILE__. __FUNCTION__ . __LINE__);
      return strlen($sJson);
    }
    catch (\Throwable $e)
    {
      var_dump($e->getMessage(), $e->getFile(), $e->getLine(), "\n\n", __FILE__. __FUNCTION__ . __LINE__);
      return strlen($sJson);
    }
  }

  public function stream_handler_response($sJson)
  {
    return call_user_func(array($this->aCallback[0], $this->aCallback[1]), $sJson);
  }

  public function stream($aCallback = array())
  {
    $sParameters = array(
      'instruments' => strtoupper(implode(',', array_keys($this->aPairs))),
    );
    $sUrl = "$this->sUrlStream/accounts/$this->sAcc/pricing/stream";
    $sUrl = sprintf("%s?%s", $sUrl, http_build_query($sParameters));

    //if (empty($aCallback)) exit('stream');
    $this->aCallback = array(new $aCallback[0], $aCallback[1]);
    $this->rStream = fopen(__DIR__ . '/stream.txt', 'w');

/*
$callback = function ($ch, $sJson) {
	var_dump ('$sJson');
	var_dump ($sJson);
	//call_user_func(array($this->aCallback[0], $this->aCallback[1]), $sJson);
	//$this->stream_handler($ch, $sJson);
	return strlen($sJson);
};
*/

    $aDefaults = array(
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER => true,
      CURLOPT_HTTPHEADER => $this->aHeaders,
      CURLOPT_URL => $sUrl,

      CURLOPT_FILE => $this->rStream,
      CURLOPT_WRITEFUNCTION => array($this, 'stream_handler'),
      CURLOPT_CONNECTTIMEOUT => 20,
      #CURLOPT_COOKIESESSION => true,
      #CURLOPT_COOKIE => "Ziel",
      #CURLOPT_COOKIELIST => "",
      #CURLOPT_VERBOSE => true,
      #CURLOPT_STDERR => $this->rVerbose,
      CURLOPT_SSL_VERIFYPEER => false,
      #CURLOPT_TIMEOUT => 5,
      CURLOPT_BUFFERSIZE => 256,
    );

    $ch = curl_init();
    curl_setopt_array($ch, $aDefaults); 
    curl_exec($ch);

    //var_dump('stream_api');

    if (curl_errno($ch) !== 0)
    {
      sleep(2);
      var_dump(curl_errno($ch));
      call_user_func_array(array($this->aCallback[0], 'configure'), array(false, true));
    }

    curl_close($ch);
    fclose($this->rStream);

    return false;
  }

  public function request_get($sEndpoint, $sParameters = array())
  {
    $sEndpoint = ltrim($sEndpoint, '/');
    $sUrl =  "$this->sUrl/accounts/$this->sAcc/$sEndpoint";
    $sUrl = sprintf("%s?%s", $sUrl, http_build_query($sParameters));
    $sUrl = rtrim($sUrl, '?');
        
    $aDefaults = array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $this->aHeaders,
      CURLOPT_URL => $sUrl,
      CURLOPT_CONNECTTIMEOUT => 20,
      CURLOPT_COOKIESESSION => true,
      #CURLOPT_COOKIE => "Ziel",
      #CURLOPT_COOKIELIST => "",
      #CURLOPT_TIMEOUT => 5,
    );

    $ch = curl_init();
    curl_setopt_array($ch, $aDefaults); 
    $sOutput = json_decode(curl_exec($ch), true);
    if (curl_errno($ch) !== 0)
    {
      sleep(5);
      var_dump(curl_errno($ch));
      #call_user_func_array(array($this->aCallback[0], 'configure'), array(false, true));
    }
    curl_close($ch);
    return $sOutput;
  }

  public function request_put($sEndpoint, $aParameters = array())
  {
    $sEndpoint = ltrim($sEndpoint, '/');
    $sUrl =  "$this->sUrl/accounts/$this->sAcc/$sEndpoint";
    #$sUrl = sprintf("%s?%s", $sUrl, http_build_query($aParameters));
    #$sUrl = rtrim($sUrl, '?');

    $sVerbose = null;

    $aDefaults = array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "PUT",
      CURLOPT_HTTPHEADER => $this->aHeaders,
      CURLOPT_URL => $sUrl,

      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => json_encode($aParameters,  JSON_FORCE_OBJECT),
      #CURLOPT_VERBOSE => true,
      #CURLOPT_STDERR => $sVerbose,

      CURLOPT_CONNECTTIMEOUT => 20,
      CURLOPT_COOKIESESSION => true,
      #CURLOPT_COOKIE => "Ziel",
      #CURLOPT_COOKIELIST => "",
      #CURLOPT_TIMEOUT => 5,
    );

    $ch = curl_init();
    curl_setopt_array($ch, $aDefaults); 
    $sOutput = json_decode(curl_exec($ch), true);

    if (curl_errno($ch) !== 0)
    {
      sleep(5);
      var_dump(curl_errno($ch));
      #call_user_func_array(array($this->aCallback[0], 'configure'), array(false, true));
    }
    curl_close($ch);
    return $sOutput;
  }

  public function request_post($sEndpoint, $aParameters = array())
  {
    $sEndpoint = ltrim($sEndpoint, '/');
    $sUrl =  "$this->sUrl/accounts/$this->sAcc/$sEndpoint";

    $aDefaults = array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER => false,
      CURLOPT_HTTPHEADER => $this->aHeaders,
      CURLOPT_URL => $sUrl,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => json_encode($aParameters,  JSON_FORCE_OBJECT),
      CURLOPT_CONNECTTIMEOUT => 20,
      CURLOPT_COOKIESESSION => true,
      #CURLOPT_COOKIE => "Ziel",
      #CURLOPT_COOKIELIST => "",
      #CURLOPT_TIMEOUT => 5,
    );

    $ch = curl_init();
    curl_setopt_array($ch, $aDefaults);
    $sOutput = json_decode(curl_exec($ch), true);
    if (curl_errno($ch) !== 0)
    {
      sleep(5);
      var_dump(curl_errno($ch));
      #call_user_func_array(array($this->aCallback[0], 'configure'), array(false, true));
    }
    curl_close($ch);
    return $sOutput;
  }

  public function set_variables()
  {
    # class
    $this->aCallback = array();
    #$this->sJson = null;
    #$this->sChunk = null;
    # market
    $this->sUrl = "https://api-fxpractice.oanda.com/v3"; #fxpractice
    $this->sUrlStream = "https://stream-fxpractice.oanda.com/v3"; #https://stream-fxpractice.oanda.com/ 
    $this->sAcc = "101-004-12277127-002"; #Roberto 11R
    # protected $sTok = "f91a1be4ee89514fd47e13fe106f2121-4c806952d758a164db0a7dc5cbbac546"; #live
    $this->sTok = "f571be0c778df5e45c4bd94f6da8f7f2-fe403dd2e024b07107d15993d2d93a17"; #fxpractice
    $this->aHeaders = array(
      'Content-Type: application/json',
      'Accept: application/json',
      'User-Agent: Ziel',
      'Connection: Keep-Alive',
      "Authorization: Bearer $this->sTok",
      'X-Accept-Datetime-Format: UNIX',
    );

    $this->oSettings = new Controller_settings();
    $this->aPairs = $this->oSettings->aPairs;
    /*
    $this->aPairs = array(
      #'USD_ZAR' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'EUR_GBP' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_SEK' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'GBP_SGD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'GBP_HKD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'EUR_JPY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'NZD_CAD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'NZD_CHF' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'NZD_HKD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'EUR_ZAR' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'EUR_PLN' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_CNH' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'EUR_HUF' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'SGD_HKD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'AUD_NZD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'NZD_JPY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'CAD_JPY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'GBP_CAD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_SAR' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'CHF_HKD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'EUR_NZD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 7, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_THB' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'GBP_CHF' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'TRY_JPY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_PLN' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'GBP_AUD' => array('ma_function' =>'trader_wma', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 5.20000000),
      #'EUR_SGD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'CHF_JPY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_INR' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'EUR_SEK' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'USD_SGD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_DKK' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'EUR_TRY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_JPY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'GBP_PLN' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_HKD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'NZD_SGD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'GBP_ZAR' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'ZAR_JPY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'EUR_AUD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'HKD_JPY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'CAD_SGD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_NOK' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'USD_CZK' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'GBP_NZD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'EUR_DKK' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'AUD_JPY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_CAD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_HUF' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'EUR_CZK' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'AUD_HKD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'CHF_ZAR' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'EUR_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'USD_TRY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'GBP_JPY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'EUR_CAD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'CAD_HKD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_MXN' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'GBP_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'SGD_CHF' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'CAD_CHF' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'USD_CHF' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'AUD_SGD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'EUR_NOK' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'EUR_CHF' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'EUR_HKD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'NZD_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 15.90000000),
      'AUD_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      #'SGD_JPY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'AUD_CAD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),
      'AUD_CHF' => array('ma_function' =>'trader_linearreg_angle', 'units' => 5000, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.20000000),

      'XAU_HKD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.00000000),
      'XAU_AUD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.00000000),
      'XAU_JPY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.00000000),
      'XAU_CHF' => array('ma_function' =>'trader_linearreg', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.319000000),
      'XAU_EUR' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.00000000),
      'XAU_CAD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.00000000),
      'XAU_GBP' => array('ma_function' =>'trader_linearreg', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.410000000),
      'XAU_NZD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.00000000),
      #'XAU_XAG' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 3.00000000),
      'XAU_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.00000000),
      'XAU_SGD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.00000000),
      'XAG_AUD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'XAG_HKD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'XAG_EUR' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'XAG_CHF' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'XAG_GBP' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'XAG_JPY' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'XAG_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'XAG_NZD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'XAG_SGD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'XAG_CAD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'XPD_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'XPT_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'XCU_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'TWIX_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),

      'AU200_AUD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'CN50_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'DE10YB_EUR' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'DE30_EUR' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'EU50_EUR' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'FR40_EUR' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'HK33_HKD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'IN50_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'JP225_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'NAS100_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'NL25_EUR' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'SG30_SGD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'SPX500_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'UK100_GBP' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'US2000_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000), # [risefall3methods open/bear - bull/close homingpigeon 1,1,3,2.2]
      'US30_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'UK10YB_GBP' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),

      'BCO_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'NATGAS_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'CORN_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'WHEAT_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'USB05Y_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'USB10Y_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'USB02Y_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'USB30Y_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'WTICO_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 1, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 2.20000000),
      'SOYBN_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 10, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
      'SUGAR_USD' => array('ma_function' =>'trader_linearreg_angle', 'units' => 100, 'iTimeChart' => 5, 'iTimeframes' => 30, 'aBbyPip' => [0.49, 4.9, 49], 'fRatio' => 0.90000000),
    );
    */
  }

/** end cURL setup */

/** client calls */
  public function resume_all_trades()
  {
    $aaOpen = $this->get_open_trades();
    if (empty($aaOpen)) {var_dump('Empty - No trades'); return false;}
    foreach($aaOpen as $aTrade)
    {
      $sp__ = $aTrade['instrument'];
      if (!isset($_SESSION[$sp__])) $_SESSION[$sp__] = array();
      if (!isset($_SESSION[$sp__]['market_orders'])) $_SESSION[$sp__]['market_orders'] = array();

      $_SESSION[$sp__]['market_orders'] = array(
        'units' => $aTrade['currentUnits'],
        'sell' => ($aTrade['currentUnits'] > 0 ? false: true),
        'buy' => ($aTrade['currentUnits'] > 0 ? true: false),
        'aPrice' => $aTrade['price'],
        'fulfilled' => 1,
      );

      print_r(implode(' - ', array(
        'market_orders',
        str_pad($aTrade['instrument'], 14),
        str_pad(
          ($aTrade['unrealizedPL'] > 0 ? "\e[34m +". $aTrade['unrealizedPL'] ." \e[0m": "\e[91m ". $aTrade['unrealizedPL'] ." \e[0m")
        , 30, " ",  STR_PAD_RIGHT),
        str_pad($aTrade['state'], 10),
        str_pad($_SESSION[$sp__]['market_orders']['units'], 10),
      )) .PHP_EOL);

    }
/*
array(14) {
  'id' =>
  string(7) "2708290"
  'instrument' =>
  string(8) "DE30_EUR"
  'price' =>
  string(7) "12664.0"
  'openTime' =>
  string(30) "2020-10-23T19:58:14.450131766Z"
  'initialUnits' =>
  string(1) "1"
  'initialMarginRequired' =>
  string(8) "633.1400"
  'state' =>
  string(4) "OPEN"
  'currentUnits' =>
  string(1) "1"
  'realizedPL' =>
  string(6) "0.0000"
  'financing' =>
  string(7) "-2.0817"
  'dividendAdjustment' =>
  string(6) "0.0000"
  'clientExtensions' =>
  array(2) {
    'id' =>
    string(9) "256371511"
    'tag' =>
    string(1) "0"
  }
  'unrealizedPL' =>
  string(8) "-90.9000"
  'marginUsed' =>
  string(8) "628.7200"
}*/
    return true;
  }

  public function get_open_trades()
  {
    $sEndpoint = "/openTrades";
    $aResponse = $this->request_get($sEndpoint);

    if (empty($aResponse)) return false;
    if (!isset($aResponse["trades"])) {var_dump($aResponse); return false;}
    return $aResponse["trades"];
  }
  
  public function close_all_trades()
  {
    #$aaResponse = array();
    $aOpen = $this->get_open_trades();
    #if (empty($aaOpen)) return false;
    
    foreach($aOpen as $aTrade)
    {
      $aResponse = $this->put_close_trade($aTrade['id']);
      print_r(implode(' - ', [
        'close',
        str_pad($aTrade['instrument'], 14),
        str_pad($aTrade['unrealizedPL'], 10),
        (isset($aResponse['orderCancelTransaction']['reason']) ? $aResponse['orderCancelTransaction']['reason'] : 'None')
      ]) .PHP_EOL);
      #$aaResponse[] = $aResponse;
    }
    #return $aaResponse;
    return true;
  }

  public function put_close_trade($iId, $iUnits = 'ALL')
  {
    $sEndpoint = "/trades/". $iId ."/close";
    $aResponse = $this->request_put($sEndpoint, array("units" => $iUnits), 'PUT');
    return $aResponse;
  }

  public function post_market_order($sInstrument, $iUnits, $fPrice)
  {
    if (is_null($iUnits)) return false;
    $sEndpoint = "/orders";
    $aOrder = array(
      "order" => array(
        "units" => $iUnits,
        "instrument" => $sInstrument,
        "timeInForce" => "FOK",
        "type" => "MARKET",
        "positionFill" => "DEFAULT"
      )
    );
    return $this->request_post($sEndpoint, $aOrder);
  }

  public function put_close_positions($sInstrument, $aUnits = array("units" => 'ALL'))
  {
      $sEndpoint = "/positions/". $sInstrument ."/close";
      $aResponse = $this->request_put($sEndpoint, $aUnits);
      return $aResponse;
  }

/** end client calls */

}


/*

acci_pivot_s4":12082.492733333334,"fibonacci_retracement_s5":12078.173933333333,"fibonacci_retracement_s6":12071.183333333334,"fibonacci_retracement_s7":12059.873933333334,"fibonacci_retracement_r0":12112.102133333332,"fibonacci_retracement_r1":12114.773933333332,"fibonacci_retracement_r2":12119.092733333333,"fibonacci_retracement_r3":12126.083333333332,"fibonacci_retracement_r4":12133.073933333331,"fibonacci_retracement_r5":12137.392733333332,"fibonacci_retracement_r6":12144.383333333331,"fibonacci_retracement_r7":12155.692733333331}}}}}]"
PHP Notice:  Undefined offset: 0 in /Users/robert-dan/Development/sticks/src/Suiteziel/Providers/Oanda/Model/Model_api.php on line 191

Notice: Undefined offset: 0 in /Users/robert-dan/Development/sticks/src/Suiteziel/Providers/Oanda/Model/Model_api.php on line 191
PHP Warning:  call_user_func_array() expects parameter 1 to be a valid callback, first array member is not a valid class name or object in /Users/robert-dan/Development/sticks/src/Suiteziel/Providers/Oanda/Model/Model_api.php on line 191

Warning: call_user_func_array() expects parameter 1 to be a valid callback, first array member is not a valid class name or object in /Users/robert-dan/Development/sticks/src/Suiteziel/Providers/Oanda/Model/Model_api.php on line 191
string(7) "$aOrder"
string(4) "null"
string(3850) "[null,"orderCreateTransaction ________________________________________________________________orderCreateTransaction ________________________________________________________________orderCreateTransaction ________________________________________________________________orderCreateTransaction ________________________________________________________________orderCreateTransaction ________________________________________________________________orderCreateTransaction ________________________________________________________________orderC

*/

/*
CURLOPT_AUTOREFERER    => true,     // set referer on redirect
CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
CURLOPT_TIMEOUT        => 120,      // timeout on response
CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
*/

/*


    public function stream_server($aInstrument = array())
    {
        if (empty($aInstrument)) exit(__FILE__ ." start_stream function has no instruments.");
        $sEndpoint = "/accounts/". $this->sAcc ."/pricing/stream";
        $aParameters = array('instruments' => implode(",", $aInstrument));
        $this->aResponse = $this->http_request($sEndpoint, $aParameters, 'GET', true);
        return $this->aResponse;
    }

  public function close_if_price($sPrice, $sComparativ)
  {
      $this->aResponse = array();
      $aOpen = $this->get_open_trades();
      foreach($aOpen["trades"] as $aTrade)
      {
          if($sComparativ == '<') 
              if($aTrade["unrealizedPL"] < $sPrice (-$aTrade["marginUsed"] - $sPrice)) $this->aResponse[] = $this->put_close_trade($aTrade["id"]);
              if($sComparativ == '>')
              if($aTrade["unrealizedPL"] > $sPrice (+$aTrade["marginUsed"] + $sPrice)) $this->aResponse[] = $this->put_close_trade($aTrade["id"]);
      }
      #return $aTrade["unrealizedPL"];
      return $this->aResponse;
  }

  public function replace_entry_order($sInstrument = null, $sPrice = null, $iUnits = null)
  {
  $aOrders = $this->get_orders($sInstrument);
      foreach($aOrders as $aOrder) 
          if (@isset($aOrder["instrument"]))
          if ($aOrder["instrument"] == $sInstrument) $sOrderId = $aOrder["id"];
      if(!is_null($sOrderId))
      $this->put_replace_entry_order($sInstrument, $sPrice, $iUnits, $sOrderId);
  }

  public function put_replace_entry_order($sInstrument = null, $sPrice = null, $iUnits = null, $sOrderId = null)
  {
      if (is_null($sInstrument) || is_null($iUnits) || is_null($sPrice) || is_null($sOrderId)) return false;
      $aOrder = array(
          "order" => array(
              "price" => $sPrice,
              "timeInForce" => "GTC",
              "instrument" => "$sInstrument",
              "units" => "$iUnits",
              "type" => "MARKET_IF_TOUCHED",
              "positionFill" => "DEFAULT",
          )
      );
      $sEndpoint = "/accounts/". $this->sAcc ."/orders/". $sOrderId;
      $this->aResponse = $this->http_request($sEndpoint, $aOrder, 'PUT');
      return $this->aResponse;
  }

  public function post_client_extension_order($sInstrument = null, $sPrice = null, $iUnits = null)
  {
      if (is_null($sInstrument) || is_null($iUnits) || is_null($sPrice)) return false;
      $aOrder = array(
          "order" => array(
              "price" => "$sPrice",
              "timeInForce" => "GTC",
              "instrument" => "$sInstrument",
              "units" => "$iUnits",
              "type" => "MARKET_IF_TOUCHED",
              "positionFill" => "DEFAULT",
              "clientExtensions" => array(
                  "comment" => strtolower(str_replace('/', '_', $sInstrument ."_MARKET_IF_TOUCHED")),
                  "tag" => "",
                  "id" => strtolower(str_replace('/', '_', $sInstrument))
              ),
          )
      );
      $sEndpoint = "/accounts/". $this->sAcc ."/orders";
      $this->aResponse = $this->http_request($sEndpoint, $aOrder, 'POST');
      return $this->aResponse;
  }

  public function post_market_order($sInstrument, $iUnits = null)
  {
      if (is_null($iUnits)) return false;
      $aOrder = array(
          "order" => array(
              "units" => $iUnits,
              "instrument" => $sInstrument,
              "timeInForce" => "FOK",
              "type" => "MARKET",
              "positionFill" => "DEFAULT"
          )
      );
      $sEndpoint = "/accounts/". $this->sAcc ."/orders";
      $this->aResponse = $this->http_request($sEndpoint, $aOrder, 'POST');
      return $this->aResponse;
  }


    public function close_pending_orders()
    {
        $aPendings = $this->get_pending_orders();
        foreach($aPendings["orders"] as $aPending) $this->put_cancel_order($aPending["id"]);
    }


    public function get_open_trades($bRaw = false)
    {
        $sEndpoint = "/accounts/". $this->sAcc ."/openTrades";
        $this->aResponse = $this->http_request($sEndpoint, array(), 'GET');

        if (!$bRaw) return $this->aResponse;
        $aResponse = array();

        foreach($this->aResponse["trades"] as $aTrade) $aResponse[] = $aTrade["instrument"];
        return $aResponse;
    }
    
    public function close_all_trades()
    {
        $this->aResponse = array();
        $aOpen = $this->get_open_trades();

        foreach($aOpen["trades"] as $aTrade) {
            var_dump([
                'close',
                $aTrade->id
            ]);
            $this->aResponse[] = $this->put_close_trade($aTrade->id);
        }
        return $this->aResponse;
    }


    public function close_all_take_profit()
    {
        $this->aResponse = array();
        $aOpen = $this->get_open_trades();



        foreach($aOpen["trades"] as $aTrade)
        {
            if($aTrade["unrealizedPL"] > 0) $this->aResponse[] = $this->put_close_trade($aTrade["id"]);
        }
        return $this->aResponse;
    }

    public function get_account_details()
    {
        $sEndpoint = "/accounts/". $this->sAcc;
        $this->aResponse = $this->http_request($sEndpoint, array(), 'GET');
        return $this->aResponse;
    }

    public function put_close_positions($sInstrument, $iUnits = 'ALL')
    {
        $sEndpoint = "/accounts/". $this->sAcc ."/positions/". $sInstrument ."/close";
        $this->aResponse = $this->http_request($sEndpoint, array("units" => $iUnits), 'PUT');
        return $this->aResponse;
    }

    public function put_close_trade($iId, $iUnits = 'ALL')
    {
        $sEndpoint = "/accounts/". $this->sAcc ."/trades/". $iId ."/close";
        $this->aResponse = $this->http_request($sEndpoint, array("units" => $iUnits), 'PUT');
        return $this->aResponse;
    }

    public function get_trades($sIns = false)
    {
        if(!$sIns) return false; 
        $sEndpoint = "/accounts/". $this->sAcc ."/trades?instrument=". $sIns;
        $this->aResponse = $this->http_request($sEndpoint, array(), 'GET');
        return $this->aResponse;
    }


    public function put_cancel_order($iId = null)
    {
        if (is_null($iId)) return 'False order id!.';
        $sEndpoint = "/accounts/". $this->sAcc ."/orders/". $iId ."/cancel";
        $this->aResponse = $this->http_request($sEndpoint, array(), 'PUT');
        return $this->aResponse;
    }

    public function get_pending_orders()
    {
        $sEndpoint = "/accounts/". $this->sAcc ."/pendingOrders";
        $this->aResponse = $this->http_request($sEndpoint);
        return $this->aResponse;
    }

    public function get_orders($sInstrument = '')
    {
        if (!empty($sInstrument)) $sInstrument = "?instrument=$sInstrument";
        $sEndpoint = "/accounts/". $this->sAcc ."/orders" . $sInstrument;
        $this->aResponse = $this->http_request($sEndpoint);
        return $this->aResponse;
    }

    public function get_candles($bInstruments = false, $bCount = true, $bWrap = true, $sGranularity = false) 
    {
        $aQuery = array(
            'granularity' => (is_string($sGranularity) ? $sGranularity : $this->sGranularity),
            'price' => $this->sPrice,
        );

        $aDates = array(
            'from' => date('Y-m-d\TH:i:s.vu\Z', strtotime($this->sFrom)),
            'to' => date('Y-m-d\TH:i:s.vu\Z', strtotime($this->sTo))
        );

        $aCount = array(
            'count' => (is_int($bCount) ? $bCount : $this->iCount)
        );

        if ($bCount) $aQuery = array_merge($aQuery, $aCount);
        else $aQuery = array_merge($aQuery, $aDates);

        $instruments = $this->set_instruments($bInstruments);

        foreach ($instruments as $sInstrument)
        {
            $sEndpoint = "/instruments/". strtoupper($sInstrument) ."/candles";
            var_dump($sEndpoint, $aQuery);
            $aTmp = $this->http_request($sEndpoint, $aQuery);
            if(!empty($aTmp)) {
                if($bWrap) $this->aResponse[] = $aTmp;
                else $this->aResponse = $aTmp;
            }
        }
        return $this->aResponse;
    }

    public function get_instruments() 
    {
        $this->aInstruments = array();
        $sEndpoint = "/accounts/". $this->sAcc ."/instruments";
        $aInstruments = $this->http_request($sEndpoint);
        foreach ($aInstruments["instruments"] as $aValue) $this->aInstruments[] = $aValue['name'];
        return $this->aInstruments;
    }

*/