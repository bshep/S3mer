<?php
/*
 * ADOBE SYSTEMS INCORPORATED
 * Copyright 2007 Adobe Systems Incorporated
 * All Rights Reserved
 * 
 * NOTICE:  Adobe permits you to use, modify, and distribute this file in accordance with the 
 * terms of the Adobe license agreement accompanying it. If you have received this file from a 
 * source other than Adobe, then your use, modification, or distribution of it requires the prior 
 * written permission of Adobe.
 */

/*
	Copyright (c) InterAKT Online 2000-2006. All rights reserved.
*/

/** 
*	Execute commands;
**/
class KT_shell {
	/**
	 * array containing the arguments for the shell command
	 * @var array
	 * @access private
	 */
	var $arguments;
	
	/**
	 * error message to be displayed as User Error
	 * @var array
	 * @access private
	 */
	var $errorType = array();
	
	/**
	 * error message to be displayed as Developer Error
	 * @var string
	 * @access private
	 */
	var $develErrorMessage = array();	
	
	/**
	 * path of the succesfully executed command
	 * @var string
	 * @access private
	 */
	var $executedCommand;
	
	/**
	 * Constructor. Doing nothing.
	 */
	function KT_shell() {
		$this->executedCommand = '';
	}
	
	/**
	 * execute the command
	 * @var array $command containing the shell command to be executed (just the name of the program, without the full path, just filename)
	 * @param array $arguments containing the arguments for the shell command
	 * @return string the output of the execute command
	 * @access public
	 */
	function execute($commands, $arguments) {
		session_write_close();
		$this->setArguments($arguments);
		
		$this->checkSystem();
		if  ($this->hasError()) {
			return;
		}
		
		$tmp_e = '';
		
		foreach ($commands as $key => $command) {
			$command = $this->prepareCommand($command);
			$cmd = $command;
			if (strpos($command, ' ') !== false) {
				$cmd = '"' . $command . '"';
				if (strtolower(substr(PHP_OS, 0, 1)) == 'w') {
					$cmd = 'CALL ' . $cmd;
				}
			}
			$cmd = $cmd .' '. $this->getArguments();
			ob_start();
			$exit_code = 1;
			@passthru($cmd, $exit_code);
			//@system($cmd, $exit_code); 
			$output = ob_get_contents();
			ob_end_clean();
			
			if ($exit_code==0) {
				$this->setError('', array(), array());
				$tmp_e = '';
				$this->executedCommand = $command;
				break;
			} else {
				$tmp_e = 'PHP_SHELL_EXEC_ERROR'; 
			}
		}
		@session_start();
		
		if ($exit_code!=0) {
			$this->setError($tmp_e, array(), array($command . ' '. $this->getArguments(), $exit_code, $output));
			
			/* safe mode */
			if (ini_get("safe_mode")) {
				$this->setError('PHP_SHELL_ERR_SAFE_MODE', array(), array());
			}
			return;
		} else {
			return $output;
		}
	}
	
 	/**
	 * check if can be run shell commands;
	 * @return nothing
	 * @access public
	 */
	function checkSystem() {
		if (!function_exists('system')) {
			$this->setError('PHP_SHELL_ERR_SYSTEM_DISABLED', array(), array());
		}	
		if (!function_exists('ob_start')) {
			$this->setError('PHP_SHELL_ERR_OB_S_DISABLED', array(), array());
		}
		if (!function_exists('ob_get_contents')) {
			$this->setError('PHP_SHELL_ERR_OB_G_DISABLED', array(), array());
		}
		if (!function_exists('ob_end_clean')) {
			$this->setError('PHP_SHELL_ERR_OB_E_DISABLED', array(), array());
		}	
	}
	
	/**
	 * prepare the command to be executed;
	 * @var string $command string with the command
	 * @return string the prepared command
	 * @access public
	 */
	function prepareCommand($command) {
		if (strtolower(substr(PHP_OS, 0, 1))=='w') {
			return $command;
		} else {
			return escapeshellcmd($command);	
		}
	}
	
	/**
	 * getter. return the arguments separated by space
	 * @return string
	 * @access public
	 */
	function getArguments() {
		if (count($this->arguments) > 0) {
			return implode(" ", $this->arguments);
		} else {
			return "";
		}
	}
	
	/**
	 * setter. 
	 * @var array $arguments the arguments of the command
	 * @return nothing
	 * @access public
	 */
	function setArguments(&$arguments) {
		$test = escapeshellarg('aaaa');
		if (strtolower(substr(PHP_OS, 0, 1))!='w' || (strtolower(substr(PHP_OS, 0, 1))=='w' && substr($test, 0, 1)!="'")) {
			foreach ($arguments as $key => $val) {
				if ($val == '>' || $val == '<') {
					$this->arguments[] = $val;
				} else {
					$this->arguments[] = escapeshellarg($val);
				}
			}
		} else {
			foreach ($arguments as $key => $val) {
				$this->arguments[] = $this->escapeshellarg($val);
			}
		}
	}
	
	/**
	 * 	escapeshellarg for windows OS.
	 * @var string $arg 
	 * @return string 
	 * @access private
	 */
	function escapeshellarg($arg) {
		if ($arg != '<' && $arg != '>') {
			$arg = preg_replace("/^\"/", "", $arg);
			$arg = preg_replace("/\"$/", "", $arg);
			$arg = preg_replace("/([^\\\])\"/ims", "$1\\\"", $arg);
			return '"'. $arg .'"';
		} else {
			return $arg;
		}
	}
	
	/**
	 * getter. return the executed command's path
	 * @return string
	 * @access public
	 */
	function getExecutedCommand() {
		return $this->executedCommand;
	}
	
	/**
	 * Setter. set error for developper and user.
	 * @var string $errorCode error message code;
	 * @var array $arrArgsUsr  array with optional parameters for sprintf functions;
	 * @var array $arrArgsDev array with optional parameters for sprintf functions.
	 * @return nothing;
	 * @access private
	 */
	function setError($errorCode, $arrArgsUsr, $arrArgsDev)	{
		$errorCodeDev = $errorCode;
		if ( !in_array($errorCodeDev, array('', '%s')) ) {
			$errorCodeDev .= '_D';
		}
		if ($errorCode!='') {
			$this->errorType[] = KT_getResource($errorCode, 'Shell', $arrArgsUsr);
		} else {
			$this->errorType = array();
		}
		if ($errorCodeDev!='') {
			$this->develErrorMessage[] = KT_getResource($errorCodeDev, 'Shell', $arrArgsDev);
		} else {
			$this->develErrorMessage = array();
		}
	}
	
	/**
	 * check if an error was setted.
	 * @return boolean true if error is set or false if not;
	 * @access public
	 */
	function hasError() {
		if (count($this->errorType)>0 || count($this->develErrorMessage)>0) {
			return 1;
		}
		return 0;
	}
		
	/**
	 * Getter. 	return the errors setted.
	 * @return array  array - 0=>error for user, 1=>error for developer;
	 * @access public
	 */
	function getError() {
		return array(implode('<br />', $this->errorType), implode('<br />', $this->develErrorMessage));	
	}

}
?>