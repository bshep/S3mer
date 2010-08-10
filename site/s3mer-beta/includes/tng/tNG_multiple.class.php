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
 * This class adds a "multiple" layer to the tNG_fields class.
 * To be used for multiple insert/update and delete, this class instantiates a dynamic number of "simple" insert/update/delete transactions and then executes them in a loop.
 * @abstract
 */
class tNG_multiple extends tNG_fields {
	/**
	 * List of transactions to be executed in the loop
	 * @var array
	 */
	var $multTNGs;

	/**
	 * List of triggers that are to be linked to the $multTNGs
	 * @var array
	 */
	var $multTriggers;

	/**
	 * Number of transactions that executed successfully
	 * @var integer
	 */
	var $noSuccess;
	/**
	 * If it was checked for errors is setted to true;
	 * @var boolean
	 * @access private
	 */
	var $errorWasCompiled;
	
	/**
	 * Register a trigger to the current transaction
	 * Or to the multiple transactions. The STARTER and the END triggers are registered to self, and the other triggers are sent to the multiple transactions.
	 * @param string $triggerType the trigger type (STARTER, AFTER, BEFORE, ERROR or END)
	 * @param string $triggerName the function name to callback when the trigger is executed
	 * @param integer $priority the trigger priority
	 * @return boolean
	 *         true if the trigger was registered, 
	 *         false if there is an error (unknown trigger type)
	 * @access public
	 */
	function registerTrigger($triggerType, $callBackFunction, $priority) {
		$ret = true;
		// check if the trigger type is valid
		if (in_array($triggerType,array("STARTER","END"))) {
			// copy from parent class
			$params = func_get_args();
			array_unshift($params,true);
			$ret = call_user_func_array(array(&$this, "registerConditionalTrigger"), $params);
			// end copy from parent class
		} elseif (in_array($triggerType,array("AFTER","BEFORE","ERROR"))) {
			$tmp = func_get_args();
			array_unshift($tmp,true);
			$this->multTriggers[] = $tmp;
		} else {
			$this->setError(new tNG_error('UNKNOWN_TRIGGER', array(), array($triggerType)));
			$ret = false;
		}
		return $ret;
	}
	
	/**
	 * Register a trigger to the current transaction
	 * Or to the multiple transactions. The STARTER and the END triggers are registered to self, and the other triggers are sent to the multiple transactions.
	 * @param string $condition 
	 * @param string $triggerType the trigger type (STARTER, AFTER, BEFORE, ERROR or END)
	 * @param string $callBackFunction the function name to callback when the trigger is executed
	 * @param integer $priority the trigger priority
	 * @return boolean
	 *         true if the trigger was registered, 
	 *         false if there is an error (unknown trigger type)
	 * @access public
	 */
	function registerConditionalTrigger($condition, $triggerType, $callBackFunction, $priority) {
		$ret = true;
		// check if the trigger type is valid
		if (in_array($triggerType,array("STARTER","END"))) {
			// copy from parent class
			$params = array();
			if (func_num_args() > 4) {
				$params = array_slice(func_get_args(),4);
			}
			$this->triggers[$triggerType][] = array($condition, $callBackFunction, $priority, $params);
			// end copy from parent class
		} elseif (in_array($triggerType,array("AFTER","BEFORE","ERROR"))) {
			$tmp = func_get_args();
			$this->multTriggers[] = $tmp;
			if ($callBackFunction == "Trigger_Default_FormValidation" && isset($this->dispatcher->UnivalProps) && $condition !== true) {
				$this->dispatcher->UnivalProps = array();
			}
		} else {
			$this->setError(new tNG_error('UNKNOWN_TRIGGER', array(), array($triggerType)));
			$ret = false;
		}
		return $ret;
	}
  /**
	 * Check for errors in each transaction; if found then the error is added to the multiple transaction error object
	 * @return nothing
	 * @access private
	 */	
	function compileError() {
		if (!isset($this->errorWasCompiled)) {
			$errObj = &$this->getError();
			for ($i=0;$i<sizeof($this->multTNGs);$i++) {
				if ($this->multTNGs[$i]->getError()) {
					$tmp = &$this->multTNGs[$i]->getError();
					$errObj->addDetails('%s', array($tmp->getDetails()), array($tmp->getDeveloperDetails()));
				}
			}
			$this->errorWasCompiled = true;
		}
	}
	
	/**
	 * Gets the error message
	 * @return string transaction error message (formatted)
	 * @access public
	 */
	function getErrorMsg() {
		$ret_warning = '';
		if (isset($this->noSuccess) && $this->noSuccess!=0) {
			$ret_warning = KT_getResource('MULTIPLE_OPERATIONS_SUCCEDED','tNG', array($this->noSuccess));
		}
		if (!$this->getError()) {
			return array($ret_warning, '', '');
		}
		$this->compileError();
		
		$ret = parent::getErrorMsg();
		$ret[0] .= $ret_warning;
		return $ret;
	}
	
	/**
	 * Gets the error message for a specific field, if it exists.
	 * @param string $fName the field name
	 * @param integer $cnt the transaction number
	 * @return string error message
	 * @access public
	 */
	function getFieldError($fName, $cnt) {
		if (isset($this->multTNGs[$cnt-1])) {
			$tmp = $this->multTNGs[$cnt-1]->getError();
			if (isset($tmp)) {
				return $tmp->getFieldError($fName);
			}
		}
		return '';
	}
	
	/**
	 * Creates a fake recordset from the given columns associative array
	 * This function is called ONLY on error
	 * @param array $fakeArr The associative array (it has multiple rows)
	 * @return object resource Recordset resource
	 * @access private
	 */
	function getFakeRecordset($fakeArr) {
		tNG_log::log('tNG' . $this->transactionType, "getFakeRecordset");
		
		$localFakeArr = array();
		$i = 0;
		foreach ($fakeArr as $fakeKey => $fakeA) {
			foreach ($fakeA as $key => $value) {
				if (!isset($localFakeArr[$key])) {
					$localFakeArr[$key] = array();
				}
				$localFakeArr[$key][$i] = $value;
			}
			$i++;
		}
		$fakeRs = new KT_FakeRecordset($this->connection);
		$KT_fakeRs = $fakeRs->getFakeRecordset($localFakeArr);

		if ($fakeRs->hasError) {
			tNG_log::log('KT_ERROR');
			$this->setError(new tNG_error('MULTIPLE_FAKE_RS_ERROR', array(), array($fakeRs->getError())));
			$disp = $this->getDispatcher();
			die($disp->getErrorMsg());
		}
		return $KT_fakeRs;
	}
	
	/**
	 * Get the recordset associated to this transaction
	 * The fake recordset on error or the local recordset
	 * @params none
	 * @return object resource Recordset resource
	 */
	function getRecordset() {
		tNG_log::log('tNG' . $this->transactionType, "getRecordset");
		if ($this->getError()) {
			$fakeArr = array();
			for ($i=0;$i<sizeof($this->multTNGs);$i++) {
				if ($this->multTNGs[$i]->getError()) {
					$fakeArr[$i] = $this->multTNGs[$i]->getFakeRsArr();
				} else {
					for ($j=$i+1;$j<sizeof($this->multTNGs);$j++) {
						$this->multTNGs[$j-1] = &$this->multTNGs[$j];
					}
					array_pop($this->multTNGs);
					$i--;
				}
			}
			if (sizeof($fakeArr) > 0) {
				return $this->getFakeRecordset($fakeArr);
			}
		}
		return $this->getLocalRecordset();
	}
	
	/**
	 * Wrapper for getSavedValue method of the executed transactions;
	 * @params string column name;
	 * @return 
	 */
	function getSavedValue($colName) {
		return $this->multTNGs[0]->getSavedValue($colName);
	}
	
	/**
	 * Abstract method; must be implemented by the inherited classes;
	 * @params none
	 * @return nothing
	 */
	function getLocalRecordset() {
		die('tNG_multiple.getLocalRecordset:<br />Method must be implemented in inherited class.');
	}
}
?>