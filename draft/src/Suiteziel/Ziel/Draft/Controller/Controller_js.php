<?php

namespace App\Suiteziel\Ziel\Draft\Controller;

use App\Suiteziel\Framework\Controller;


class Controller_js extends Controller
{

  public function __construct()
  {

  }
	
	public function curl_request_wallet ($sUri, $sJsonData)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);		
		curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:3000/". $sUri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $sJsonData);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}

	public function curl_request_exchange ($sUri, $sJsonData)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);		
		curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:3001/". $sUri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $sJsonData);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
}