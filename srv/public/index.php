<?php

require __DIR__.'/../../config/bootstrap.php';

use App\Core\Framework\Dispatcher;

function event_routing() {
	#router
print '<pre>';
	$oRouter = new Dispatcher();
	$oRouter->invoking_callouts();
print '</pre>';
	return true;
}

if (!event_routing()) die('event_routing'); 
?>