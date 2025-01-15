<?php

namespace App\Suiteziel\Sockets\Server\Controller;

class Controller_udp_client {
	
	function udp_client () {
		$address = '127.0.0.1';
		$port = 20010;
		$beat_period = 1;
		
		
		$fp = stream_socket_client("udp://$address:$port", $errno, $errstr);
		if (!$fp) {
			die("ERROR: $errno - $errstr");
		}

		while (true) {
			$message = sprintf(
				'%s send: %s'. PHP_EOL,
				date('c'),
				rand(0, 1000000)
			);
			fwrite($fp, $message);
			echo $message;

			sleep($beat_period);
			
		}
	}
}

?>