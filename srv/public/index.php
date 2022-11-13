<?php

require __DIR__.'/../../config/bootstrap.php';

use App\Core\Http\Router;

function event_dispatch(){
	#router
print '<pre>';
	$oRouter = new Router();
	$oRouter->invoking_callouts();
print '</pre>';
	return true;
}

if (!event_dispatch()) die('event_dispatch'); 
?>