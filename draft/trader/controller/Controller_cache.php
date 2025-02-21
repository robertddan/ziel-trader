<?php

namespace Ziel\Providers\Oanda\Controller;


use Ziel\Framework\Controller;
use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;

class Controller_cache extends Controller
{
  public $cm__;

	public function __construct($sType)
	{
    // https://www.phpfastcache.com/
    
    // Setup File Path on your config files
    // Please note that as of the V6.1 the "path" config 
    // can also be used for Unix sockets (Redis, Memcache, etc)


    if ($sType == 'view')
    {
      CacheManager::setDefaultConfig(new ConfigurationOption([
        'path' => '/var/www/phpfastcache.com/dev/tmp/view', // or in windows "C:/tmp/"
      ]));
  
      // In your class, function, you can call the Cache
      $this->cm__ = CacheManager::getInstance('files');
    }
    elseif($sType = 'stream')
    {
      CacheManager::setDefaultConfig(new ConfigurationOption([
        'path' => '/var/www/phpfastcache.com/dev/tmp/stream', // or in windows "C:/tmp/"
      ]));
  
      // In your class, function, you can call the Cache
      $this->cm__ = CacheManager::getInstance('files');
    }
    elseif($sType = 'prices')
    {
      CacheManager::setDefaultConfig(new ConfigurationOption([
        'path' => '/var/www/phpfastcache.com/dev/tmp/prices', // or in windows "C:/tmp/"
      ]));
  
      // In your class, function, you can call the Cache
      $this->cm__ = CacheManager::getInstance('files');
    }

  }

}

?>