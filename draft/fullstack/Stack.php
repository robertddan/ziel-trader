<?php

/*
stack
*/

namespace App\Draft\Fullstack;

class Stack {
	
	private $aQuery;
	
	function __construct() {
		$this->aQuery = $_GET;
		var_dump('-Stack');
		$this->start_childrens_of_main_process();
	}
	
	function configure() {
		var_dump('--invoking-configure');
		var_dump($this->aQuery);
		$this->start_childrens_of_main_process();
		return true;
	}
	
	function start_childrens_of_main_process() {
		var_dump('--invoking-start_childrens_of_main_process');
		var_dump($this->aQuery);
		
		$pid = pcntl_fork();
		
		if ($pid == -1) {
			die('could not fork');
		} else if ($pid) {
			// We are the parent
			pcntl_wait($status); //Protect against Zombie children
			
			// (on a system with the "whoami" executable in the path)
			$output = null;
			$retval = null;
			exec('ps -aux', $output, $retval);
			echo "Returned with status $retval and output:\n";
			print_r($output);
			
		} else {
			// We are the child
			$descriptorspec = array(
				0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
				1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
				2 => array("file", "/tmp/error-output.txt", "a") // stderr is a file to write to
			);
			
			$cwd = '/tmp';
			$env = array('some_option' => 'aeiou');
			
			$proc=proc_open("echo foo",
				array(
					array("pipe","r"),
					array("pipe","w"),
					array("pipe","w")
				),
			$pipes);
			
			print stream_get_contents($pipes[1]);
			
		}
		return true;
	}
}

?>