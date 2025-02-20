<?php

namespace App\Suiteziel\Http\Server\Command;

use App\Suiteziel\Framework\Command;
use App\Suiteziel\Http\Server\Controller\Controller_tasks;

/*
 * Command to run the server:
 * php ./bin/suiteziel http:server:run
 *
 */
class Command_run extends Command
{
  public $oTasks;

  public function __construct()
  {
  }

  public function configure()
  {

    $oTasks = new Controller_tasks();
    $oTasks->run();
  }

}