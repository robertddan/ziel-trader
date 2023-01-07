<?php

require __DIR__.'/../../config/bootstrap.php';


use App\Core\Http\Router;

function event_route() {
	#router
	print '<pre>';
		$oDispatcher = new Router();
		$oDispatcher->invoking_callouts();
		$oDispatcher->forwards_response();
	print '</pre>';
	return true;
}

if (!event_route()) die('event_route'); 
?>