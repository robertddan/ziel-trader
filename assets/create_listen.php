<?php
#$socket = socket_create_listen("80");
#socket_getsockname($socket, $addr, $port);
#print "Server Listening on $addr:$port\n"; 
#return var_dump( getprotobyname('icmp') );

$addr = "0.0.0.0";
$port = 80;

#$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
#socket_connect($socket, '127.0.0.1', 80);

$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_connect($socket, $addr, $port);
socket_getsockname($socket, $addr, $port);

/*
$socket  = socket_create(AF_INET, SOCK_RAW, 1);
socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));
socket_connect($socket, $host, null);
$ts = microtime(true);
socket_send($socket, $package, strLen($package), 0);
if (socket_read($socket, 255))
*/


	if (!$socket) {
		print "Failed to create socket!\n";
		exit;
	}

	while (true) {
		$client = socket_accept($socket);
		$input = trim(socket_read ($client, 4096));
		$input = explode(" ", $input);
		$input = $input[1];
		$fileinfo = pathinfo($input);
		
		var_dump($fileinfo = pathinfo($input));
		
		switch ($fileinfo['extension']) {
			case "png";
				$mime = "image/png";
			break;
			default:
				$mime = "text/html";
		}
		
		if ($input == "/") {
			$input = "/index.html";
		}
		
		$input = ".$input";
		
		if (file_exists($input) && is_readable($input)) {
			print "Serving $input\n";
			$contents = file_get_contents($input);
			$output = "HTTP/1.0 200 OK\r\nServer: APatchyServer\r\nConnection: close\r\nContent-Type: $mime\r\n\r\n$contents";
		} else {
			$contents = "The file you requested does not exist. Sorry!";
			$output = "HTTP/1.0 404 OBJECT NOT FOUND\r\nServer: APatchyServer\r\nConnection: close\r\nContent-Type: text/html\r\n\r\n$contents";
		}
		
		sleep(1);
		
		socket_write($client, $output);
		socket_close ($client);
	}

	socket_close ($socket);
?>