<?php	/*	>php -q server.php	*/

error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

$address = "127.0.0.1";
$port = "1222";
GLOBAL $clients;
GLOBAL $client_list;

// socket creation
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

if (!is_resource($socket))
	console("socket_create() failed: ".socket_strerror(socket_last_error()), true);

if (!socket_bind($socket, $address, $port))
	console("socket_bind() failed: ".socket_strerror(socket_last_error()), true);

if(!socket_listen($socket, 20))
	console("socket_listen() failed: ".socket_strerror(socket_last_error()), true);

console("Server started on $address : $port\n\n");
$master = $socket;
$sockets = array($socket);

while(true){
	$changed = $sockets;
	foreach($changed as $socket){

		if($socket==$master){

			// new client will enter in this case and connect with server

			socket_select($changed,$write=NULL,$except=NULL,NULL);
			console("Master Socket Changed.\n\n");
			$client=socket_accept($master);
			if($client<0)
				{ console("socket_accept() failed\n\n"); continue; }
			else
				{ console("Connecting socket.\n\n"); fnConnectacceptedSocket($socket,$client); $master=null; }
		}else{

			// clients who are connected with server will enter into this case
			// first client will handshake with server and then exchange data with server
$client = getClientBySocket($socket);
			if($client) {

				if ($clients[$socket]["handshake"] == false){
					$bytes = @socket_recv($client, $data, 2048, MSG_DONTWAIT);
					if ((int)$bytes == 0)
						continue;

					console("Handshaking headers from client:".$data);
					if (handshake($client, $data, $socket))
						$clients[$socket]["handshake"] = true;
				} else if ($clients[$socket]["handshake"] == true){
					$bytes = @socket_recv($client, $data, 2048, MSG_DONTWAIT);
					if ($data != ""){
						$decoded_data = unmask($data);
						socket_write($client, encode("You have entered: ".$decoded_data));
						console("Data from client:".$decoded_data);
						socket_close($socket);
					}
				}
}
		}
	}
}

# Close the master sockets
socket_close($socket);

function unmask($payload) {
	$length = ord($payload[1]) & 127;

	if($length == 126) {
		$masks = substr($payload, 4, 4);
		$data = substr($payload, 8);
	}
	elseif($length == 127) {
		$masks = substr($payload, 10, 4);
		$data = substr($payload, 14);
	}
	else {
		$masks = substr($payload, 2, 4);
		$data = substr($payload, 6);
	}

	$text = '';
for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $masks[$i%4];
	}
	return $text;
}

function encode($text)
{
	// 0x1 text frame (FIN + opcode)
	$b1 = 0x80 | (0x1 & 0x0f);
	$length = strlen($text);

	if($length <= 125) 		$header = pack('CC', $b1, $length); 	elseif($length > 125 && $length < 65536) 		$header = pack('CCS', $b1, 126, $length); 	elseif($length >= 65536)
		$header = pack('CCN', $b1, 127, $length);

	return $header.$text;
}

function fnConnectacceptedSocket($socket,$client) {

	GLOBAL $clients;
	GLOBAL $client_list;
$clients[$socket]["id"] = uniqid();
	$clients[$socket]["socket"] = $socket;
	$clients[$socket]["handshake"] = false;
	console("Accepted client \n\n");

	$client_list[$socket] = $client;
}

function getClientBySocket($socket) {
	GLOBAL $client_list;
	return $client_list[$socket];
}

function handshake($client, $headers, $socket) {

	if(preg_match("/Sec-WebSocket-Version: (.*)\r\n/", $headers, $match))
		$version = $match[1];
	else {
		console("The client doesn't support WebSocket");
		return false;
	}

	if($version == 13) {
		// Extract header variables
if(preg_match("/GET (.*) HTTP/", $headers, $match))
			$root = $match[1];
		if(preg_match("/Host: (.*)\r\n/", $headers, $match))
			$host = $match[1];
		if(preg_match("/Origin: (.*)\r\n/", $headers, $match))
			$origin = $match[1];
		if(preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $headers, $match))
			$key = $match[1];

		$acceptKey = $key.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
		$acceptKey = base64_encode(sha1($acceptKey, true));

		$upgrade = "HTTP/1.1 101 Switching Protocols\r\n".
				   "Upgrade: websocket\r\n".
				   "Connection: Upgrade\r\n".
				   "Sec-WebSocket-Accept: $acceptKey".
				   "\r\n\r\n";

		socket_write($client, $upgrade);
		return true;
	}
	else {
		console("WebSocket version 13 required (the client supports version {$version})");
		return false;
}
}

function console($text){
	$File = "log.txt"; 
	$Handle = fopen($File, 'a');
	fwrite($Handle, $text); 
	fclose($Handle);
}

?>
Copy above code and create PHP file socket_server.php

Test socket server with HTML5 client
<html>
<head>
<title>WebSocket</title>

<style>
 html,body{font:normal 0.9em arial,helvetica;}
 #log {width:440px; height:200px; border:1px solid #7F9DB9; overflow:auto;}
 #msg {width:330px;}
</style>

<script>
var socket;

function init(){
	var host = "ws://127.0.0.1:1222";
  try{
    socket = new WebSocket(host);
    log('WebSocket - status '+socket.readyState);

    socket.onopen    = function(msg){ log("Welcome - status "+this.readyState); };
    socket.onmessage = function(msg){ log("Received: "+msg.data); };
    socket.onclose   = function(msg){ log("Disconnected - status "+this.readyState); };
  }
  catch(ex){ log(ex); }
  $("msg").focus();
}

function send(){
  var txt,msg;
  txt = $("msg");
  msg = txt.value;
  if(!msg){ alert("Message can not be empty"); return; }
  txt.value="";
  txt.focus();
  try{ socket.send(msg); log('Sent: '+msg); } catch(ex){ log(ex); }
}
function quit(){
  log("Goodbye!");
  socket.close();
  socket=null;
}

// Utilities
function $(id){ return document.getElementById(id); }
function log(msg){ $("log").innerHTML+="\n"+msg; }
function onkey(event){ if(event.keyCode==13){ send(); } }
</script>

</head>
<body onload="init()">
<h3>WebSocket v2.00</h3>
 <div id="log"></div>
 <input id="msg" type="textbox" onkeypress="onkey(event)"/>
 <button onclick="send()">Send</button>
 <button onclick="quit()">Quit</button>
 <div>Commands: hello, hi, name, age, date, time, thanks, bye</div>
</body>
</html>