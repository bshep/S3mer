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
 * The FakeRecordset class. 
 */
class KT_FakeRecordset {

	/**
	 * The connection
	 * @var object Connection
	 * @access private
	 */
	var $connection = null;
	
	/**
	 * The fields on wich we create a fake recordset
	 * @var array fields
	 * @access private
	 */
	var $fields = null;
	
	/**
	 * The name of the temporary table
	 * @var array fields
	 * @access private
	 */
	var $tmpTableName = 'KT_fakeRS';
	
	/**
	 * If there were errors
	 * @var bool hasError
	 * @access public
	 */
	var $hasError = false;
	
	/**
	 * The error
	 * @var string error
	 * @access private
	 */
	var $error = '';

	/**
	 * The constructor
	 * @param object Connection &$connection - either a ADODB Connection or a KT_Connection object
	 * @access public
	 */
	function KT_FakeRecordset(&$connection) {
		$this->connection = &$connection;
		if (!isset($GLOBALS['KT_serverModel'])) {
			KT_setDbType($this->connection);
		}
	}
	
	/**
	 * Gets a fake recordset make from the array received. Depending on the server model could be a MySQL recordset or
	 * an ADODB one.
	 * @param array The fields on wich we create a fake recordset
	 * @return mixt resource id for MySQL server model or object recordset.
	 * @access public
	 */
	function getFakeRecordset(&$fields) {
		$this->fields = $fields;
		
		if (!is_array($this->fields)) {
			$this->error = KT_getResource('PHP_DB_ARG_NOT_ARRAY_D','DB');
			$this->hasError = true;
			return null;
		}
		
		if ($GLOBALS['KT_serverModel'] == 'mysql') {
			return $this->getMySQLfakeRS();
		} else {
			return $this->getADODBfakeRS();
		}
	}
	
	/**
	 * Gets a MySQL recordset (resource id). Used when mysql version is older than 4.
	 * @return integer
	 * @access private
	 */
	function getMySQLfakeRS() {
		$this->tmpTableName = $this->tmpTableName . '_' . date('Ymd');
		$test_create_sql = 'CREATE TEMPORARY TABLE ' . $this->tmpTableName . ' (kt_test TEXT)';
		$delete_sql = 'DROP TABLE ' . $this->tmpTableName;
		$create_sql = 'CREATE TEMPORARY TABLE ' . $this->tmpTableName . ' (';
		$insert_sql = 'INSERT INTO ' . $this->tmpTableName . ' (';
		$select_sql = 'SELECT * FROM ' . $this->tmpTableName;
		$insert_values = '';
		$result = '';
		$doInsert = true;
		$multiple = false;
		
		if (count($this->fields) == 0) {
			// empty fake rs
			$doInsert = false;
		}
		
		$this->connection->Execute($delete_sql);
		$response = $this->connection->Execute($test_create_sql);
		if ($response === false) {
			return $this->getMySQL4fakeRS();
		}
		if (count($this->fields) == 0) {
			$this->fields['KT_fakeField'] = '';
		}
		foreach ($this->fields as $key => $value) {
			$create_sql .= $key . ' TEXT, ';
			$insert_sql .= $key . ', ';
			if (!is_array($value)) {
				$insert_values .= "'" . mysql_escape_string($value) . "', ";
			} else {
				$multiple = true;
			}
		}
		
		$create_sql = substr(trim($create_sql), 0, -1) . ')';
		$insert_sql = substr(trim($insert_sql), 0, -1) . ') VALUES (';
		if ($multiple) {
			$multiple_values = array();
			foreach ($this->fields as $key => $values) {
				$i = 0;
				foreach ($values as $key => $value) {
					if (!isset($multiple_values[$i])) {
						$multiple_values[$i] = '';
					}
					$multiple_values[$i] .= "'" . mysql_escape_string($value) . "', ";
					$i++;
				}
			}
			for ($i = 0; $i < count($multiple_values); $i++) {
				$multiple_values[$i] = substr(trim($multiple_values[$i]), 0, -1);
			}
			$insert_values = implode('), (', $multiple_values);
		} else {
			$insert_values = substr(trim($insert_values), 0, -1);
		}
		$insert_sql .=  $insert_values . ')';
		
		$this->connection->Execute($delete_sql);
		$response = $this->connection->Execute($create_sql);
		if ($response === false) {
			$this->error = KT_getResource('PHP_DB_CREATE_TMP_D','DB',array($this->connection->ErrorMsg(), $create_sql));
			$this->hasError = true;
			return null;
		}
		if ($doInsert) {
			$response = $this->connection->Execute($insert_sql);
			if ($response === false) {
				$this->error = KT_getResource('PHP_DB_INSERT_TMP_D','DB',array($this->connection->ErrorMsg(), $insert_sql));
				$this->hasError = true;
				return null;
			}
		}
		$result = $this->connection->MySQL_Execute($select_sql);
		$response = $this->connection->Execute($delete_sql);
		if ($response === false) {
			$this->error = KT_getResource('PHP_DB_DROP_TMP_D','DB',array($this->connection->ErrorMsg(), $delete_sql));
			$this->hasError = true;
			return null;
		}
		return $result;
	}
	
	/**
	 * Gets a MySQL recordset (resource id). Optimized for mysql version 4 or higher.
	 * @return integer
	 * @access private
	 */
	function getMySQL4fakeRS() {
		$fields = array();
		$i = 0;
		$select_sql = '';
		
		foreach ($this->fields as $key => $value) {
			$i = 0;
			if (!is_array($value)) {
				$fields[$i][$key] = $value;
			} else {
				foreach ($value as $key2 => $val) {
					$fields[$i][$key] = $val;
					$i++;
				}
			}
		}
		
		for ($i = 0; $i < count($fields); $i++) {
			$row = $fields[$i];
			if ($i > 0) {
				$select_sql .= ' UNION ALL ';
			}
			$select_sql .= 'SELECT ';
			foreach ($row as $colName => $value) {
				$select_sql .=  "'" . mysql_escape_string($value) . "' AS " . $colName . ", ";
			}
			$select_sql = substr(trim($select_sql), 0, -1);
		}
		if ($select_sql == '') {
			$select_sql = "SELECT * FROM (SELECT 1 AS tmp) AS tmptable where tmptable.tmp = 2";
		}

		$result = $this->connection->MySQL_Execute($select_sql);
		if ($result === false) {
			$this->error = KT_getResource('PHP_DB_SELECT_UNION_D','DB',array($this->connection->ErrorMsg(), $select_sql));
			$this->hasError = true;
			return null;
		}
		return $result;
	}
	
	/**
	 * Gets the a fake adodb recordset.
	 * @return object
	 * @access private
	 */
	function getADODBfakeRS() {
		$result = '';
		$fields = array();
		
		$j = 0;
		foreach ($this->fields as $key => $value) {
			$i = 0;
			if (!is_array($value)) {
				$fields[$i][$key] = $value;
				$fields[$i][$j] = $value;
			} else {
				foreach ($value as $key2 => $val) {
					$fields[$i][$key] = $val;
					$fields[$i][$j] = $val;
					$i++;
				}
			}
			$j++;
		}
		
		$result = new KT_fakeADORecordset($fields);
		return $result;
	}
	
	/**
	 * Gets the error message
	 * @return string
	 * @access public
	 */
	function getError() {
		return $this->error;
	}
}

/**
 * Util KT_fakeADORecordset class. Transform an array in a fake ADODB recordset.
 */
class KT_fakeADORecordset {
	/**
	 * The original array with all the fields/rows.
	 * @var array
	 * @access public
	 */
	var $allFields = array();
	/**
	 * The returned fields
	 * @var array
	 * @access public
	 */
	var $fields = array();
	/**
	 * Current position
	 * @var integer
	 * @access public
	 */
	var $index = 0;
	/**
	 * Are we at the end of the record?
	 * @var boolean
	 * @access public
	 */
	var $EOF = true;
	/**
	 * Number of rows in recordset
	 * @var integer
	 * @access public
	 */
	var $_numOfRows = 0;
	/**
	 * Number of fields in a ro
	 * @var integer
	 * @access public
	 */
	var $_numOfFields = 0;
	/**
	 * The constructor. Sets the totalrows and total fields values and current fields value.
	 * @param array field to be transformed in an ADODB recordset.
	 * @access public
	 */
	function KT_fakeADORecordset(&$fields) {
		if ( is_array($fields) && count($fields) > 0 ) {
			$this->allFields = $fields;
			$this->fields = $fields[0];
			$this->EOF = false;
			$this->_numOfRows = count($fields);
			$this->_numOfFields = count($fields[0]) / 2;
		}
	}
	
	/**
	 * Moves to the first row if any
	 * @return nothing
	 * @access public
	 */
	function MoveFirst() {
		$this->index = 0;
		if (isset($this->allFields[$this->index])) {
			$this->fields = $this->allFields[$this->index];
			$this->EOF = false;
		}
	}
	
	/**
	 * Moves to the next row if exists
	 * @return nothing
	 * @access public
	 */
	function MoveNext() {
		$this->index++;
		if (isset($this->allFields[$this->index])) {
			$this->fields = $this->allFields[$this->index];
		} else {
			$this->EOF = true;
		}
	}
	
	/**
	 * Returns the value of a field
	 * @param field name
	 * @return mixt null or field value
	 * @access public
	 */
	function Fields($colName) {
		return $this->fields[$colName];
	}
	
	/**
	 * Gets the record count
	 * @return integer
	 * @access public
	 */
	function RecordCount() {
		return $this->_numOfRows;
	}
	
	/**
	 * Gets the fields(columns) count
	 * @return integer
	 * @access public
	 */
	function FieldCount() {
		return $this->_numOfFields;
	}
	
	/**
	 * Bogus method. Just to implement the interface.
	 * @return nothing
	 * @access public
	 */
	function Close() {
			
	}

}
?>