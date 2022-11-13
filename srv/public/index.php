<?php

require __DIR__.'/../../config/bootstrap.php';

use App\Core\Framework\Dispatcher;

function event_dispatch() {
	#router
print '<pre>';
	$oDispatcher = new Dispatcher();
	$oDispatcher->invoking_callouts();
	$oDispatcher->forwards_response();
print '</pre>';
	return true;
}

if (!event_dispatch()) die('event_dispatch'); 
?>