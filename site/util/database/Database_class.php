<?php

require_once 'DB.php';

class Database {
	var $DatabaseBackend;
	var $type;
	var $dsn;
	var $options;
	var $connected = false;
	var $last_err = null;
	
	function Database($DBType) {
		$this->options = array('portability' => DB_PORTABILITY_ALL ^DB_PORTABILITY_LOWERCASE,
							   'debug'		 => 5);

		$this->type = $DBType;
		
	}
	
	function connect( $server, $database, $username, $password ) {
		$this->dsn = $this->type.'://'.$username.':'.$password.'@'.$server.'/'.$database;
		
		$this->DatabaseBackend =& DB::connect($this->dsn,$this->options);
		
		
		if (PEAR::isError($this->DatabaseBackend)) {
			$this->last_err = $this->DatabaseBackend;
			$this->connected = false; 
		} else {
			$this->connected =  true;	
		}
	}
	
	function disconnect() {
		$this->DatabaseBackend->disconnect();	
	}
	
	function execute_query( $query ) {
		$ret = $this->DatabaseBackend->query( $query );
		if (PEAR::isError($ret)) {
			$this->last_err = $ret;
			$ret = false;
		}
		
		return $ret;
	}
	
	function fetch_assoc($result) {
		return $result->fetchRow(DB_FETCHMODE_ASSOC);
	}
	
	function is_connected() {
		return $this->connected;
	}
	
	function escape_string($str) {
		return $this->DatabaseBackend->escapeSimple($str);	
	}
	
	function error() {
		if($this->last_err != null) {
			return $this->last_err->getMessage();
		} else {
			return "";
		}
	}

}

?>