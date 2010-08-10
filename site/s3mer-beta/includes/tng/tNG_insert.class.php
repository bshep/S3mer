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
 * This class is the "insert" implementation of the tNG_fields class.
 * @access public
 */
class tNG_insert extends tNG_fields {

	/**
	 * Constructor. Sets the connection, the database name and other default values.
	 * Also sets the transaction type.
	 * @param object KT_Connection &$connection the connection object
	 * @access public
	 */
	function tNG_insert(&$connection) {
		parent::tNG_fields($connection);
		$this->transactionType = '_insert';
		$this->exportRecordset = true;
		$this->registerTrigger("ERROR", "Trigger_Default_Insert_RollBack", 99);
	}

	/**
	 * Overwrites tNG method in order to register some triggers specific to Register Transaction
	 * @access protected
	 */
	function doTransaction() {
		$table = $this->getTable();
		if (isset($GLOBALS['tNG_login_config']['table']) && $GLOBALS['tNG_login_config']['table'] == $table ) {
			// BEFORE triggers
			$this->registerTrigger("BEFORE", "Trigger_Registration_CheckUniqueUsername", 200);
			$this->registerTrigger("BEFORE", "Trigger_Registration_CheckPassword", 210);
			if ($GLOBALS['tNG_login_config']['password_encrypt'] == "true") {
				$this->registerTrigger("BEFORE", "Trigger_Registration_EncryptPassword", 220);
			}
			if ($GLOBALS['tNG_login_config']['activation_field']  != "" ) {
				$this->registerTrigger("BEFORE", "Trigger_Registration_PrepareActivation", 230);
			}
			if ((isset($GLOBALS['tNG_login_config']['registration_date_field']) && $GLOBALS['tNG_login_config']['registration_date_field']!='') || (isset($GLOBALS['tNG_login_config']['max_tries_field']) && $GLOBALS['tNG_login_config']['max_tries_field']!='') || (isset($GLOBALS['tNG_login_config']['max_tries_disabledate_field']) && $GLOBALS['tNG_login_config']['max_tries_disabledate_field']!='')) {
				$this->registerTrigger("BEFORE", "Trigger_Registration_PrepareRegExtrFields", 240);
			}
			// AFTER triggers
			if ($GLOBALS['tNG_login_config']['password_encrypt'] == "true") {
				$this->registerTrigger("AFTER", "Trigger_Registration_RestorePassword", -10);
			}
			$this->registerTrigger("AFTER", "Trigger_Registration_AddDynamicFields", -5);
		}
		parent::doTransaction();
	}
	
	/**
	 * Prepares the insert SQL query to be executed
	 * @access protected
	 */
	function prepareSQL() {
		tNG_log::log('tNG_insert', 'prepareSQL', 'begin');
		parent::prepareSQL();
		// check the columns number
		$sql = 'INSERT INTO ' . $this->table;
		$tmColStr  = $tmValStr = '';
		$KT_sp = false;
		//generate the column and the value strings
		foreach($this->columns as $colName => $colDetail) {
			$colType = $colDetail['type'];
			$colValue = $colDetail['value'];
			$colMethod = $colDetail['method'];
			if ($colMethod != 'HIDDEN') {
				// if we handle a hidden field, we should not use it in the update SQL.
				$sep = ($KT_sp) ? ', ' : '';// set the separator ',' (first time will be none)
				$KT_sp = true;
				//build the nameList and valueList
				$tmColStr = $tmColStr . $sep . KT_escapeFieldName($colName);
				if ($colType == "FILE_TYPE") {
					// if we handle a file upload, the file name will be set afterwards.
					$tmValStr = $tmValStr . $sep . "''";
				} else {
					$tmValStr = $tmValStr . $sep . KT_escapeForSql($colValue, $colType);
				}
			}
		}
		if (!$KT_sp) {
			// no column was actually added
			die('tNG_insert.prepareSQL:<br />Please specify some fields to insert.');
		}
		// build the final SQL
		$sql .= ' (' . $tmColStr . ') values (' . $tmValStr . ')';
		$this->setSQL($sql);
		tNG_log::log('tNG_insert', 'prepareSQL', 'end');
		return null;
	}
	
	/**
	 * Get the local recordset associated to this transaction
	 * @return object resource Recordset resource
	 * @access protected
	 */
	function getLocalRecordset() {
		tNG_log::log('tNG_insert', 'getLocalRecordset');
		$fakeArr = array();
		$tmpArr = $this->columns;
		if (!isset($tmpArr[$this->primaryKey])) {
			$tmpArr[$this->primaryKey] = $this->primaryKeyColumn;
			$tmpArr[$this->primaryKey]['default'] = NULL;
		}
		foreach($tmpArr as $colName=>$colDetails) {
			$tmpVal = KT_escapeForSql($colDetails['default'], $colDetails['type'], true);
			$fakeArr[$colName] = $tmpVal;
		}
		return $this->getFakeRecordset($fakeArr);
	}

	/**
	 * Adds a column to the transaction
	 * Calls the parent addColumn method then sets the default value.
	 * @param string $colName The column name
	 * @param string $type The column type (NUMERYC_TYPE, STRING_TYPE, etc)
	 * @param string $method The request method (GET, POST, FILE, COOKIE, SESSION)
	 * @param string $reference The submitted variable name (if method=GET and reference=test, value=$_GET['test'])
	 * @param string $defaultValue The default value for the current column
	 * @access public
	 */
	function addColumn($colName, $type, $method, $reference, $defaultValue = '') {
		parent::addColumn($colName, $type, $method, $reference);
		if ($method == "VALUE") {
			$this->columns[$colName]['default'] = $reference;
		} else {
			$this->columns[$colName]['default'] = $defaultValue;
		}
	}

	/**
	 * This function is called once the transaction SQL was actually executed
	 * And only if the execution was succesffully. On insert, it sets the primary key value if it is not set.
	 * @access protected
	 */
	function postExecuteSql() {
		tNG_log::log('tNG_insert', 'postExecuteSql');
		if (isset($this->columns[$this->primaryKey])) {
			$this->primaryKeyColumn['value'] = $this->getColumnValue($this->primaryKey);
		} else {
			$this->primaryKeyColumn['value'] = $this->connection->Insert_ID($this->table, $this->primaryKey);
		}
		return null;
	}
	
	/**
	 * if at least one value was submited return true;
	 * @return boolean
	 * @access protected
	 */
	function wereValuesSubmitted() {
		$ret = false;
		foreach ($this->columns as $colName=>$colDetails) {
			if ($colDetails['method'] == 'POST' || $colDetails['method'] == 'FILES') {
				if ($colDetails['default'] != $colDetails['value']) {
					$ret = true;
					break;
				}
			}
		}
		return $ret;
	}
}
?>