<?php

namespace Ziel\Framework;

class Model
{
  public function __construct()
  {
    # memory
    ini_set('error_reporting', E_ALL);
    ini_set('memory_limit', -1);
    ini_set('ignore_user_abort', true);
    ini_set('max_execution_time', 0);
    # trader
    ini_set('trader.real_precision', 8);
    # xdebug
    ini_set('xdebug.max_nesting_level', 14000);
    ini_set('xdebug.var_display_max_depth', '10');
    ini_set('xdebug.var_display_max_children', '256');
    ini_set('xdebug.var_display_max_data', '1024');
    # bc math scale
    call_user_func('bcscale', 0);
    # session
    if (session_status() !== 2) session_start();
    #var_dump(session_save_path());
    #var_dump($_SESSION);
  }

  public function __destruct()
  {
    #session_destroy();
  }
}

