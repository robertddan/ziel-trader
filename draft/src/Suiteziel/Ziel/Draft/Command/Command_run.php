<?php

namespace App\Suiteziel\Ziel\Command;

use App\Suiteziel\Framework\Command;
use App\Suiteziel\Ziel\Controller\Controller_tasks;

/*
 * Command to run the server:
 * php ./bin/suiteziel http:server:run
 * 7:30PM CEST7:30PM CEST node draft/src/Suiteziel/Nodejs/ziel_draft_exchange.js
 *
 */
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