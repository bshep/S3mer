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
 * Represents the base class for checkDetails and checkMaster classes;
 * @access public
 */
	class tNG_CheckTableField {
		/**
		 * The tNG object
		 * @var object tNG
		 * @access public
		 */
		var $tNG;
		/**
		 * The table name
		 * @var string table name
		 * @access public
		 */
		var $table;
		/**
		 * The field name
		 * @var string
		 * @access public
		 */
		var $field;
		/**
		 * The type of the field
		 * @var string 
		 * @access public
		 */
		var $type;
		/**
		 * The value of the field
		 * @var string 
		 * @access public
		 */
		var $value;
		/**
		 * The error message to be used;
		 * @var string
		 * @access public
		 */
		var $errorMsg;
		/**
		 * if it will be throw error in case the records exists;
		 * @var boolean
		 * @access public
		 */
		var $throwErrorIfExists;
	
		/**
			* Constructor. Sets the reference to the transaction in which the trigger is used.
			* set some defaults values
			* @param object tNG &$tNG reference to transaction object
			* @access public
			*/
		function tNG_CheckTableField(&$tNG) {
			$this->tNG = &$tNG;
			$this->table = 'mytable';
			$this->field = 'myfield';
			$this->type = 'NUMERIC_TYPE';
			$this->value = -1;
			$this->errorMsg = '';
			$errorIfExists = false;
		}
		/**
			* setter. set the table name
			* @param string table name
			* @return nothing
			* @access public
			*/
		function setTable($table) {
			$this->table = $table;
		}
		
		/**
			* setter. set the field name
			* @param string field name
			* @return nothing
			* @access public
			*/
		function setFieldName($field) {
			$this->field = $field;
		}
		/**
			* setter. set the field type
			* @param string field type
			* @return nothing
			* @access public
			*/
		function setFieldType($type) {
			$this->type = $type;
		}
		/**
			* setter. set the field value
			* @param string field value
			* @return nothing
			* @access public
			*/
		function setFieldValue($value) {
			$this->value = $value;
		}
		/**
			* setter. if it will be throw error in case the records exists;
			* @param boolean 
			* @return nothing
			* @access public
			*/
		function errorIfExists($throwErrorIfExists) {
			$this->throwErrorIfExists = $throwErrorIfExists;
		}
		/**
			* setter. set the error message
			* @param string  error message
			* @return nothing
			* @access public
			*/
		function setErrorMsg($errorMsg) {
			$this->errorMsg = $errorMsg;
		}
		
		/**
			* execute method of the class;
			* @param none
			* @return mix null or error object if records exists and the value of the throwErrorIfExists;
			* @access public
			*/
		function Execute() {
			$field_value = KT_escapeForSql($this->value, $this->type);
			$sql = "SELECT " . KT_escapeFieldName($this->field) . " FROM " . $this->table . " WHERE " . KT_escapeFieldName($this->field) . " = " . $field_value;
			$ret = $this->tNG->connection->Execute($sql);
			if ($ret === false) {
				return new tNG_error('CHECK_TF_SQL_ERROR', array(), array($this->tNG->connection->ErrorMsg(), $sql));
			}
			$useSavedData = false;
			if (in_array($this->tNG->transactionType, array('_delete', '_multipleDelete'))) {
				$useSavedData = true;
			}
			
			if ($this->throwErrorIfExists && !$ret->EOF) {
				$err = new tNG_error('DEFAULT_TRIGGER_MESSAGE', array(), array());
				return $err;
			}
			if (!$this->throwErrorIfExists && $ret->EOF) {
				$err = new tNG_error('DEFAULT_TRIGGER_MESSAGE', array(), array());
				return $err;
			}
			return null;
		}
	}
?>