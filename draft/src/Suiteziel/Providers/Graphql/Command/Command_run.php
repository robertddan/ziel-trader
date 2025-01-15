<?php

namespace App\Suiteziel\Providers\Graphql\Command;

use App\Suiteziel\Framework\Command;
use App\Suiteziel\Providers\Graphql\Controller\Controller_tasks;


class Command_run extends Command
{
  public $oTasks;

  public function __construct()
  {
  }

  public function configure($aArguments = array())
  {
    $oTasks = new Controller_tasks();
    $oTasks->run();
  }

}