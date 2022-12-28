<?php

/*

1. PHP read files with sockets child
- read var folder index
- build a queue depending of the memory size
- get directory name or will be the date the prices are in
- fopen files one by one 
- flush the already retrived data to the client


2. SQL query
- split the main requests in litle child requests
- multiple threads to database
- with multiple answers


3.
$pid = pcntl_rfork(RFNOWAIT|RFTSIGZMB, SIGUSR1);										#socket c
if ($pid > 0) {
  // This is the parent process.
	#where the request came in
	
  var_dump($pid);
} else {
  // This is the child process.
	# this is the loop to open and read files
	
  var_dump($pid);
  sleep(2); // as the child does not wait, so we see its "pid"
}




*/


?>