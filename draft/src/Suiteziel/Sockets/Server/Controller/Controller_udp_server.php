<?php

namespace App\Suiteziel\Sockets\Server\Controller;

class Controller_udp_server {

	function udp_server () {
		
		var_dump(net_get_interfaces());
		return true;
		$address = '127.0.0.1';
		$port = 20010;
		$beat_period = 1;
		$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
		socket_bind($socket, $address, $port);

		if ($socket === false) {
		 throw new \RuntimeException(
			 sprintf(
					'could not connect to socket address %s on port %s. Error: %s %s',
					$address,
					$port,
					socket_last_error(),
					socket_strerror(socket_last_error())
				)
			);
		}

		while (true){
			echo socket_read ($socket, 1024);
		}
	}

} 

/*
$udp_server = udp_server();
print $udp_server->udp_server();
*/

?>