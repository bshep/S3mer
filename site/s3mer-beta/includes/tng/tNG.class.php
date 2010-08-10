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
 * This class contains the tNG core (triggers, transactions, errors, etc)
 * but does not handle field-based transactions.
 * @access public
 */
class tNG {
	/**
	 * The connection that will execute the transaction
	 * @var object KT_Connection
	 * @access public
	 */
	var $connection = null;

	/**
	 * The transaction SQL statement
	 * @var string
	 * @access private
	 */
	var $sql = "";

	/**
	 * Registered triggers
	 * First dimension is the type of the trigger (STARTER, AFTER, BEFORE, ERROR or END), second dimension is an associative array with (key = callBackFunction,value = priority)
	 * @var array
	 * @access protected
	 */
	var $triggers = array();

	/**
	 * True if the transaction was started (if the starter triggers returned something)
	 * @var boolean
	 * @access private
	 */
	var $started = false;

	/**
	 * The transaction type
	 * Depending on the class that extends the base class, this changes.
	 * @var string
	 * @access protected
	 */
	var $transactionType = "UNKNOWN";

	/**
	 * Wether the current implementation exports a recordset
	 * @var boolean
	 * @access protected
	 */
	var $exportRecordset = false;
	/**
	 * The transaction result
	 * @var object resource
	 * @access private
	 */
	var $transactionResult = null;


	/**
	 * The error object
	 * @var object tNG_error
	 * @access private
	 */
	var $errObj = null;

	/**
	 * The tNG_dispatcher object
	 * @var object tNG_dispatcher
	 * @access public
	 */
	var $dispatcher = null;


	/**
	 * Constructor. Sets the connection.
	 * @param object KT_Connection &$connection the connection object
	 * @access public
	 */
	function tNG(&$connection) {
		$this->connection = &$connection;
		KT_setDbType($connection);
	}
	
	/**
	 * Set dispatcher object for the transaction.
	 * @param object tNG_dispatcher &$dispatcher the dispatcher object
	 * @access public
	 */
	function setDispatcher(&$dispatcher) {
		$this->dispatcher = &$dispatcher;
	}
	
	/**
	 * Gets the transaction dispatcher
	 * @return tNG_dispatcher type
	 * @access public
	 */
	function &getDispatcher() {
		return $this->dispatcher;
	}
	
	/**
	 * Getter for the exportsRecordset property
	 * @return boolean
	 *         true if the current implementation exports a recordset
	 *         false otherwise
	 * @access public
	 */
	function exportsRecordset() {
		return $this->exportRecordset;
	}

	/**
	 * register a trigger to the current transaction
	 * @param string $triggerType the trigger type (STARTER, AFTER, BEFORE, ERROR or END)
	 * @param string $callBackFunction the function name to callback when the trigger is executed
	 * @param integer $priority the trigger priority
	 * @access private
	 */
	function registerConditionalTrigger($condition, $triggerType, $callBackFunction, $priority) {
		// check if the trigger type is valid
		if (in_array($triggerType,array("STARTER","AFTER","BEFORE","END","ERROR"))) {
			$params = array();
			if (func_num_args() > 4) {
				$params = array_slice(func_get_args(),4);
			}
			$this->triggers[$triggerType][] = array($condition, $callBackFunction,$priority,$params);
			if ($callBackFunction == "Trigger_Default_FormValidation" && isset($this->dispatcher->UnivalProps) && $condition !== true) {
				$this->dispatcher->UnivalProps = array();
			}
		} else {
			$this->setError(new tNG_error('UNKNOWN_TRIGGER', array(), array($triggerType)));
		}
	}
	/**
	 * Register the trigger to transaction; 
	 * In case of error, the error object is setted;
	 * @param string trigger type
	 * @param string callback function name
	 * @param integer priority of the trigger
	 * @return nothing 
	 * @access public
	 */
	function registerTrigger($triggerType,$callBackFunction,$priority) {
			$params = func_get_args();
			array_unshift($params,true);
			return call_user_func_array(array(&$this, "registerConditionalTrigger"), $params);
	}
	
	/**
	 * Prepares the SQL query to be executed
	 * To be implemented in derived class
	 * @access protected
	 */
	function prepareSQL() {
	}

	/**
	 * Executes the Transaction
	 * Tests the STARTER triggers, executes the BEFORE triggers, the transaction SQL, the AFTER triggers and the END triggers. If error occurs, it executes the ERROR triggers and exits.
	 * @access public
	 */
	function executeTransaction() {
		tNG_log::log('tNG' . $this->transactionType, 'executeTransaction', 'begin');
		if ($this->started) {
			tNG_log::log('tNG' . $this->transactionType, 'executeTransaction', 'end');
			return false;
		}

		//calling the starter triggers and terminate execution if we had an error
		$ret = $this->executeTriggers("STARTER");
		if (is_object($ret)) {
			$this->setError($ret);
			$this->executeTriggers("ERROR");
			tNG_log::log('tNG' . $this->transactionType, 'executeTransaction', 'end');
			return false;
		}

		if (!$this->started) {
			tNG_log::log('tNG' . $this->transactionType, 'executeTransaction', 'end');
			return false;
		}

		$ret = $this->doTransaction();
		tNG_log::log('tNG' . $this->transactionType, 'executeTransaction', 'end');

		return $ret;
	}
	
	/**
	 * Executes the registered triggers that matches the specified type
	 * @param string $triggerType (STARTER, AFTER, BEFORE, ERROR and END)
	 * @access protected
	 */
	function executeTriggers($triggerType) {
		if (isset($this->triggers[$triggerType]) && is_array($this->triggers[$triggerType])) {
			uasort($this->triggers[$triggerType],array('tNG','compareTriggers'));
			foreach ($this->triggers[$triggerType] as $key => $trigger) {
				$run = KT_DynamicData($trigger[0], $this, 'expression');
				$runTrigger = false;
				$ok = false;
				@eval('$runTrigger = ('.$run.');$ok = true;');
				if ($ok !== true) {
					die('Internal Error.Invalid boolean expression: '.$run);
				}
				if ($runTrigger) {
					$callBackFunction = $trigger[1];
					$tempParam = array_reverse($trigger[3]);
					$tempParam[] = &$this;
					$tempParam = array_reverse($tempParam, true);
					tNG_log::log($triggerType, $callBackFunction, 'begin');
					if (is_string($callBackFunction) && function_exists($callBackFunction)) {
					$ret = call_user_func_array($callBackFunction,$tempParam);
					} else {
						die('Internal Error. Trigger "'.$callBackFunction.'" does not exist.');
					}
					// call all triggers for ERROR and STARTER tiggers
					if ($triggerType != "ERROR") {
						if (is_object($ret)) {
							tNG_log::log('KT_ERROR');
							tNG_log::log($triggerType, $callBackFunction, 'end');
							return $ret;
						}
					}
					tNG_log::log($triggerType, $callBackFunction, 'end');
				}
			}
		}
		return null;
	}

	/**
	 * compares two trigger objects (by their priority)
	 * @param array $tr1 first trigger
	 * @param array $tr2 second trigger
	 * @return integer
	 *         0  if same priority
	 *         -1 if tr1.priority < tr2.priority
	 *         1  if tr1.priority > tr2.priority
	 * @access private
	 */
	function compareTriggers($tr1 , $tr2) {
		if ($tr1[2] == $tr2[2]) {
			return 0;
		}
		return ($tr1[2] < $tr2[2]) ? -1 : 1;
	}
	
	/**
	 * Gets the transaction type
	 * @return string transaction type
	 * @access public
	 */
	function getTransactionType() {
		return $this->transactionType;
	}

	/**
	 * Gets the started property
	 * @return boolean
	 *         true if the transaction was started
	 *         false otherwise
	 * @access public
	 */
	function isStarted() {
		return $this->started;
	}

	/**
	 * Sets the started property (the starter triggers are the ones that call this function)
	 * @param boolean $started
	 * @access public
	 */
	function setStarted($started) {
		$this->started = $started;
	}

	/**
	 * Sets the transaction SQL statement
	 * @param string $sql
	 * @access public
	 */
	function setSQL($sql) {
		$this->sql = $sql;
	}

	/**
	 * This function is called once the transaction SQL was actually executed
	 * And only if the execution was succesffully
	 */
	function postExecuteSql() {
		return null;
	}

	/**
	 * Parses the SQL error
	 * Called when the transaction SQL throws an error. It sets the error object message
	 * @param string $sql the SQL statement that throwed the error message
	 * @param string $errorMsg the error text message
	 * @access protected
	 */
	function &parseSQLError($sql, $errorMsg) {
		$sql = KT_escapeAttribute($sql);
		$errObj = new tNG_error('SQL_ERROR', array($errorMsg), array($sql));
		return $errObj;
	}
	
	/**
	 * Setter for the transaction error object
	 * @param object tNG_error $errorObj the error object
	 * @access public
	 */
	function setError($errorObj) {
		$this->errorObj = $errorObj;
	}
	
	/**
	 * Getter for the transaction error object
	 * @return object tNG_error the error object
	 * @access public
	 */
	function &getError() {
		$ret = null;
		if(isset($this->errorObj)){
			$ret = &$this->errorObj;
		}
		return $ret;
	}
	
	/**
	 * Gets the error message
	 * @return string transaction error message (formatted)
	 * @access public
	 */
	function getErrorMsg() {
		$ret_warning = '';
		$ret_user = '';
		$ret_devel = '';
		$errObj = &$this->getError();
		if ($errObj) {
			$ret_user = $errObj->getDetails();
			$ret_devel = $errObj->getDeveloperDetails();
		}
		return array($ret_warning, $ret_user, $ret_devel);
	}
  
	/**
	 * executing the transaction (triggers, prepare SQL)
	 * @access protected
	 */
	function doTransaction() {
		tNG_log::log('tNG' . $this->transactionType, 'doTransaction', 'begin');
		
		$tmp = $this->getError();
		if (is_object($tmp)) {
			$this->setError($tmp);
			$this->executeTriggers("ERROR");
			tNG_log::log('tNG' . $this->transactionType, 'doTransaction', 'end');
			return false;
		}
		
		//calling the before triggers and terminate execution if we had an error
		$ret = $this->executeTriggers("BEFORE");
		if (is_object($ret)) {
			$this->setError($ret);
			$this->executeTriggers("ERROR");
			tNG_log::log('tNG' . $this->transactionType, 'doTransaction', 'end');
			return false;
		}
		
		//process the SQL for eventual auto-generation
		$ret = $this->prepareSQL();
		if (is_object($ret)) {
			tNG_log::log('KT_ERROR');			
			$this->setError($ret);
			$this->executeTriggers("ERROR");
			tNG_log::log('tNG' . $this->transactionType, 'doTransaction', 'end');
			return false;
		}

		$ret = $this->getError();
		if (is_object($ret)) {
			$this->executeTriggers("ERROR");
			tNG_log::log('tNG' . $this->transactionType, 'doTransaction', 'end');
			return false;
		}
		
		//executing the transaction
		if ($this->sql != '') {
			tNG_log::log('tNG' . $this->transactionType, 'executeTransaction', 'execute sql');
			if (!is_array($this->sql)) {
				$this->transactionResult = $this->connection->Execute($this->sql);
			} else {
				for ($i=0;$i<sizeof($this->sql);$i++) {
					$this->transactionResult = $this->connection->Execute($this->sql[$i], $this->connection);
					if ($this->transactionResult === false) {
						break;
					}
				}
			}

			//check if the transaction has been done OK
			if (!$this->transactionResult) {
				$ret = $this->parseSQLError($this->sql, $this->connection->ErrorMsg());
				$this->setError($ret);
				tNG_log::log('KT_ERROR');
				$this->executeTriggers("ERROR");
				tNG_log::log('tNG' . $this->transactionType, 'doTransaction', 'end');
				return false;
			}
		}

		$ret = $this->postExecuteSql();
		if (is_object($ret)) {
			$this->setError($ret);
			$this->executeTriggers("ERROR");
			tNG_log::log('tNG' . $this->transactionType, 'doTransaction', 'end');
			return false;
		}
				
		//if the SQL is a SELECT statement
		if (is_object($this->transactionResult)) {
			if ($this->transactionResult->RecordCount() == 0) {
				$this->transactionResult = null;
			}
		}
					
		//calling the after triggers
		$ret = $this->executeTriggers("AFTER");
		if (is_object($ret)) {
			$this->setError($ret);
			$this->executeTriggers("ERROR");
			tNG_log::log('tNG' . $this->transactionType, 'doTransaction', 'end');
			return false;
		}
		
		$ret = $this->executeTriggers("END");
		if (is_object($ret)) {
			$this->setError($ret);
			$this->executeTriggers("ERROR");
			tNG_log::log('tNG' . $this->transactionType, 'doTransaction', 'end');
			return false;
		}
		tNG_log::log('tNG' . $this->transactionType, 'doTransaction', 'end');
		return true;
	}
	
	/**
	 * Executes the error triggers and set the error object;
	 * @param object $errorObj error object
	 * @access protected
	 */
	function rollBackTransaction(&$errorObj) {
		$this->setError($errorObj);
		$this->executeTriggers('ERROR');
	}
	
}
?>