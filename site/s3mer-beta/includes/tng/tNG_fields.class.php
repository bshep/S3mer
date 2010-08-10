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
 * This class adds the "field" concept to the tNG base class.
 * Thus, the SQL query will be dinamically-built.
 * @abstract
 * @access public
 */
class tNG_fields extends tNG {
	/**
	 * List of columns to use when generating the transaction SQL query
	 * @see addColumn()
	 * @var array
	 * @access protected
	 */
	var $columns = array();

	/**
	 * Primary Key field
	 * @var string
	 * @access protected
	 */
	var $primaryKey = '';

	/**
	 * Primary Key details
	 * Value, Type, etc
	 * @var array
	 * @access protected
	 */
	var $primaryKeyColumn = array();

	/**
	 * The Primary Key value that is submitted on update
	 * @var string
	 * @access protected
	 */
	var $pkName = 'kt_pk';

	/**
	 * The transaction saved data (before the actual execution)
	 */
	var $savedData=array();

	/**
	 * Table to use when generating the transaction SQL query
	 * @var string
	 * @access protected
	 */
	var $table = '';

	/**
	 * Evaluates the columns values then executes the Transaction
	 * @access public
	 */
	function executeTransaction() {
		$this->compileColumnsValues();
		parent::executeTransaction();
	}

	/**
	 * Adds a column to the transaction
	 * @see $columns
	 * @param string $colName The column name
	 * @param string $type The column type (NUMERIC_TYPE, STRING_TYPE, etc)
	 * @param string $method The request method (GET, POST, FILE, COOKIE, SESSION)
	 * @param string $reference The submitted variable name (if method=GET and reference=test, value=$_GET['test'])
	 * @access public
	 */
	function addColumn($colName, $type, $method, $reference) {
		if ($this->started && ($method != 'VALUE' && $method != 'EXPRESSION')) {
			die("You can only add supplemental columns by value or by expression once the transaction is started.");
		}
		if (!isset($this->columns[$colName])) {
			$this->columns[$colName] = array();
		}
		$this->columns[$colName]['type'] = $type;
		$this->columns[$colName]['method'] = $method;
		$this->columns[$colName]['reference'] = $reference;

		if ($method == 'VALUE') {
			$this->columns[$colName]['value'] = $reference;
		}
		if ($method == 'EXPRESSION') {
			$this->columns[$colName]['method'] = "VALUE";
			$this->columns[$colName]['reference'] = KT_DynamicData($reference, $this, '', $this->getTransactionType() == "_delete");
			if ($type == "NUMERIC_TYPE" || $type == "DOUBLE_TYPE") {
				$this->columns[$colName]['reference'] = $this->evaluateNumeric($this->columns[$colName]['reference']);
			}
		}
		if ($this->started) {
			tNG_prepareValues($this->columns[$colName]);
		}
	}
	
	/**
	 * Evaluates a numeric expression
	 * @param string $expr - the expression to be evaluated (must contain only: numbers (0 to 9) and the follwing characters: + - * / . ( )
	 * @return float - the result of the evaluated expresison. 
	 * @access public
	 */
	function evaluateNumeric($expr) {
		$retVal = null;
		if ( preg_match("/\d+/",$expr) && preg_match("/^[\d\s\*\-\+\/\.\(\)]+$/",$expr) ) {
			$ok = false;
			$evalExpr = "\$retVal=".$expr.";\$ok=true;";
			@eval($evalExpr);
			if ($ok !== true) {
				$retVal = null;
				$this->setError(new tNG_error('FIELDS_EVAL_EXPR_FAILED', array() ,array($expr)));
			}
		} else {
			if ($GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT') {
				$this->setError(new tNG_error('FIELDS_EVAL_EXPR_INVALID', array(), array($expr)));
			}
		}
		return $retVal;
	}
	/**
	 * Sets a value for a given column
	 * @param string $colName - the column to alter
	 * @param object unknown $colValue - the new value
	 * @access public
	 */
	function setColumnValue($colName, $colValue) {
		if (isset($this->columns[$colName])) {
			$this->columns[$colName]['value'] = $colValue;
			$this->columns[$colName]['reference'] = $colValue;
			$this->columns[$colName]['method'] = 'VALUE';
		} else {
			die('tNG_fields.setColumnValue:<br />Column ' . $colName . ' is not part of the current transaction.');
		}
	}

	/**
	 * Sets a value for a column directly
	 * @param string $colName - the column to alter
	 * @param object unknown $colValue - the new value
	 * @access public
	 */
	function setRawColumnValue($colName, $colValue) {
		if (isset($this->columns[$colName])) {
			$this->columns[$colName]['value'] = $colValue;
		} else {
			die('tNG_fields.setColumnValue:<br />Column ' . $colName . ' is not part of the current transaction.');
		}
	}

	/**
	 * Gets the value of a column
	 * @param string $colName
	 * @return object unknown
	 * @access public
	 */
	function getColumnValue($colName) {
		if (isset($this->columns[$colName])) {
			return $this->columns[$colName]['value'];
		} elseif ($colName == $this->getPrimaryKey()) {
			return $this->getPrimaryKeyValue();
		} else {
			die('tNG_fields.getColumnValue:<br />Column ' . $colName . ' is not part of the current transaction.');
		}
	}

	/**
	 * Gets the reference of a column
	 * @param string $colName
	 * @return string
	 * @access public
	 */
	function getColumnReference($colName) {
		if (isset($this->columns[$colName])) {
			return $this->columns[$colName]['reference'];
		} else {
			die('tNG_fields.getColumnReference:<br />Column ' . $colName . ' is not part of the current transaction.');
		}
	}

	/**
	 * Gets the type of a column
	 * @param string $colName
	 * @return string
	 *         The column type
	 * @access public
	 */
	function getColumnType($colName) {
		if (isset($this->columns[$colName])) {
			return $this->columns[$colName]['type'];
		} else {
			if ($colName == $this->primaryKey) {
				return $this->primaryKeyColumn['type'];
			}
			die('tNG_fields.getColumnType:<br />Column ' . $colName . ' is not part of the current transaction.');
		}
	}

	/**
	 * Setter for the transaction SQL table
	 * @see $table
	 * @param string $tableName The table name
	 * @access public
   */
	function setTable($tableName) {
		if ($this->table == '') {
			$this->table = $tableName;
			$this->pkName .= "_" . preg_replace("/[^\w]/", "_", $this->table);
		} else {
			die('tNG_fields.setTable:<br />The table has already been set.');
		}
	}

	/**
	 * Getter for the transaction SQL table
	 * @see $table
	 * @return string
	 *         The table name
	 * @access public
   */
	function getTable() {
		return $this->table;
	}

	/**
	 * Sets the primary key and its details
	 * @see $primaryKey, $primaryKeyColumn
	 * @param string $colName The column name
	 * @param string $type The column type (NUMERIC_TYPE, STRING_TYPE, etc)
	 * @param string $method The request method (GET, POST, FILE, COOKIE, SESSION)
	 * @param string $reference The submitted variable name (if method=GET and reference=test, value=$_GET['test'])
	 * @access public
   */
	function setPrimaryKey($colName, $type, $method='VALUE', $reference=NULL) {
		$this->primaryKey = $colName;
		$this->primaryKeyColumn['type'] = $type;
		$this->primaryKeyColumn['method'] = $method;
		$this->primaryKeyColumn['reference'] = $reference;
	}
	
	/**
	 * Gets the primary key column
	 * @return string
	 * @access public
	 */
	function getPrimaryKey() {
		return $this->primaryKey;
	}

	/**
	 * Gets the primary key value
	 * @return object unknown
	 * @access public
	 */
	function getPrimaryKeyValue() {
		if (isset($this->primaryKeyColumn['value'])) {
			return $this->primaryKeyColumn['value'];
		} else {
			return null;
		}
	}
	
	/**
	 * Sets for each column the value correspondeing to the reference/method
	 * @access public
   */
	function compileColumnsValues() {
		// Use multiple values in kt_pk or from get
		$savedPK = $this->primaryKeyColumn;
		$this->primaryKeyColumn['method'] = 'POST';
		$this->primaryKeyColumn['reference'] = $this->pkName;
		tNG_prepareValues($this->primaryKeyColumn);
		if (!isset($this->primaryKeyColumn['value'])) {
			$this->primaryKeyColumn = $savedPK;
			tNG_prepareValues($this->primaryKeyColumn);
		}
		foreach($this->columns as $colName=>$colDetails) {
			tNG_prepareValues($this->columns[$colName]);
		}
	}
	
	/**
	 * Get the local recordset associated to this transaction
	 * This function is to be implemented in subclasses
	 * @abstract
	 * @return object resource Recordset resource
	 */
	function getLocalRecordset() {
		$this->setError(new tNG_error('FIELDS_LOCAL_RS', array(), array()));
	}
	
	/**
	 * Creates a fake recordset from the given columns associative array
	 * This function is called on error or for the insert default values.
	 * @param array $fakeArr The associative array
	 * @return object resource Recordset resource
	 * @access protected
	 */
	function getFakeRecordset($fakeArr) {
		tNG_log::log('tNG' . $this->transactionType, "getFakeRecordset");
		
		$fakeRs = new KT_FakeRecordset($this->connection);
		$KT_fakeRs = $fakeRs->getFakeRecordset($fakeArr);
		
		if ($fakeRs->hasError) {
			tNG_log::log('KT_ERROR');
			$this->setError(new tNG_error('FIELDS_FAKE_RS_ERROR', array(), array($fakeRs->getError())));
			$disp = $this->getDispatcher();
			die($disp->getErrorMsg());
		}
		return $KT_fakeRs;
	}
	
	/**
	 * Get the recordset associated to this transaction
	 * @return object resource Recordset resource
	 *         The fake recordset on error or the local recordset
	 * @access public
	 */
	function getRecordset() {
		tNG_log::log('tNG' . $this->transactionType, "getRecordset");
		if ($this->getError()) {
			$ret = $this->getFakeRecordset($this->getFakeRsArr());
		} else {
			$ret = $this->getLocalRecordset();
		}
		return $ret;
	}

	/**
	 * Prepares the SQL query to be executed
	 * To be implemented in derived class
	 * @access protected
	 */
	function prepareSQL() {
		if ($this->table == '') {
			return new tNG_error('FIELDS_NO_TABLE', array(), array());
		}
	}

	/**
	 * Creates a fake recordset array from the current $columns
	 * This function is called ONLY on error
	 * @return array associative array with the current values.
	 * @access protected
	 */
	function getFakeRsArr() {
		tNG_log::log('tNG' . $this->transactionType, "getFakeRsArr");
		$localRs = $this->getLocalRecordset();
		if (is_resource($localRs)) {
			$localRs = new KT_Recordset($localRs); 
		}
		$fakeArr = array();
		$tmpArr = $this->columns;
		if (!isset($tmpArr[$this->primaryKey]) && trim($this->primaryKey) != '') {
			$tmpArr[$this->primaryKey] = $this->primaryKeyColumn;
		}
		// Transaction was executed and failed, create the recordset from the submitted values
		foreach($tmpArr as $colName=>$colDetails) {
			if ($colDetails['method'] == "CURRVAL") {
				$value = KT_escapeForSql($localRs->Fields($colName), "STRING_TYPE", true);
			} else {
				$value = KT_escapeForSql($colDetails['value'], "STRING_TYPE", true);
			}
			$fakeArr[$colName] = $value;
		}

		$savedPK = $this->getSavedValue($this->primaryKey);
		if (!is_null($savedPK)) {
			$fakeArr[$this->pkName]=KT_escapeForSql($savedPK, "STRING_TYPE", true);
		} else {
			$fakeArr[$this->pkName]="";
		}
		return $fakeArr;
	}
	
	/**
	 * Parses the SQL error
	 * Calls the parent function then sets the error to a specific column if possible.
	 * @param string $sql the SQL statement that throwed the error message
	 * @param string $errorMsg the error text message
	 * @access protected
	 */
	function &parseSQLError($sql, $error) {
		$errObj = parent::parseSQLError($sql, $error);
		if ($errObj != NULL) {
			foreach ($this->columns as $colName => $colDetail) {
				if (preg_match('/^.*[^a-z]+' . preg_quote($colName,'/') . '[^a-z]+.*$/i', $error)) {
					$errObj->setFieldError($colName, '%s', array($error));
					break;
				}
			}
		}
		return $errObj;
	}
	
	/**
	 * Updates a field after the actual transaction was executed
	 * @param string $fieldName The field name
	 * @param string $fieldValue The field value
	 * @return boolean
	 *         true  on success
	 *         false on error
	 * @access public
	 */
	function afterUpdateField($fieldName, $fieldValue) {
		tNG_log::log('tNG' . $this->transactionType, "afterUpdateField", "$fieldName, $fieldValue");
		$keyName = $this->primaryKey;
		$keyValue = $this->primaryKeyColumn['value'];
		$sql = 'UPDATE '. $this->table . ' SET '.KT_escapeFieldName($fieldName).' = '.KT_escapeForSql($fieldValue,'STRING_TYPE').' WHERE '.KT_escapeFieldName($keyName).' = ' . KT_escapeForSql($keyValue, $this->primaryKeyColumn['type']);
		$success = $this->connection->Execute($sql);
		if($success === false) {
			return new tNG_error('FIELDS_AFTER_UPDATE_ERROR', array(), array($this->connection->ErrorMsg()));
		}
		return null;
	}

	/**
	 * Gets the error message for a specific field, if it exists.
	 * @param string $fName the field name
	 * @return string error message
	 * @access public
	 */
	function getFieldError($fName) {
		$tmp = $this->getError();
		if (isset($tmp)) {
			return $tmp->getFieldError($fName);
		}
	}
	
	/**
	 * Gets the value saved for the given column name
	 * @param string $colName the name of the column
	 * @return string 
	 * @access public
	 */
	function getSavedValue($colName) {
		if (isset($this->savedData[$colName])) {
			return $this->savedData[$colName];
		}
		return null;
	}
  
	/**
	 * Retrieve and store the saved values from database;
	 * @return string 
	 * @access public
	 */
	function saveData() {
		tNG_log::log('tNG' . $this->transactionType, "saveData");
		$keyName  = $this->getPrimaryKey();
		$keyValue = $this->getPrimaryKeyValue();
		$keyType  = $this->getColumnType($keyName);
		$escapedKeyValue = KT_escapeForSql($keyValue, $keyType);

		$sql = 'SELECT * FROM ' . $this->getTable() . ' WHERE ' . KT_escapeFieldName($keyName) . ' = ' . $escapedKeyValue;
		$rs = $this->connection->Execute($sql);
		if ($rs === false) {
			return new tNG_error('FIELDS_SAVEDATA_ERROR', array(), array($sql, $this->connection->ErrorMsg()));
		}
		$this->savedData = $rs->fields;
		return null;
	}
}
?>