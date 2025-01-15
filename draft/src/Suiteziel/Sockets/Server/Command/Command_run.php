<?php

namespace App\Suiteziel\Sockets\Server\Command;

use App\Suiteziel\Framework\Command;
use App\Suiteziel\Sockets\Server\Controller\Controller_tasks;

/*
 * Command to run the server:
 * php ./bin/suiteziel http:server:run
 */
class Command_run extends Command
{
  public $oTasks;

  public function __construct()
  {
  }

  public function configure($aArgs)
  {
    $oTasks = new Controller_tasks();
    $oTasks->run($aArgs);
  }

}