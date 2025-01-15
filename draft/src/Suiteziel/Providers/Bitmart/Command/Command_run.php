<?php

namespace App\Suiteziel\Providers\Bitmart\Command;

use App\Suiteziel\Framework\Command;
use App\Suiteziel\Providers\Bitmart\Controller\Controller_tasks;


class Command_run extends Command
{
  public $oTasks;

  public function __construct()
  {
  }

  public function configure($bCloseTrades = true)
  {

    $oTasks = new Controller_tasks();
    $oTasks->run();
  }

}