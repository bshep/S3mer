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
 * This the CheckUnique trigger; 
 * Checks if a value already exists in table.
 * @access public
 */
	class tNG_CheckUnique {
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
		 * The array with field(s) name
		 * @var array
		 * @access public
		 */
		var $field;				
		/**
		 * The error message to be used;
		 * @var string
		 * @access public
		 */
		var $errorMsg;
		
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
			$this->field = array();
			$this->field[$field] = $field;
		}
		/**
			* setter. set the field name
			* @param string field name
			* @return nothing
			* @access public
			*/
		function addFieldName($field) {
			$this->field[$field] = $field;
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
			* Constructor. Sets the reference to the transaction in which the trigger is used.
			* @param object tNG &$tNG reference to transaction object
			* @access public
			*/
		function tNG_CheckUnique(&$tNG) {
			$this->tNG = &$tNG;
			$this->table = 'mytable';
			$this->errorMsg = '';
			$this->field = array();		
		}
		
		/**
			* execute method of the class; check if record exists and return null or error;
			* @param none
			* @return mix null or error object if record exists
			* @access public
			*/
		function Execute() {
			$where = array();
			$i = 0;
			foreach ($this->field as $field ) {
				if ($i++==0) {
					$first = $field;
				}
				$type = $this->tNG->getColumnType($field);
				$value = $this->tNG->getColumnValue($field);
				$where[] = KT_escapeFieldName($field) . " = " . KT_escapeForSql($value, $type);				
			}
			
			$sql = "SELECT * FROM " . $this->table . " WHERE " . implode(' AND ', $where);
			if (in_array($this->tNG->transactionType, array('_update', '_multipleUpdate'))) {
				$pk = $this->tNG->getPrimaryKey();
				$pk_value = $this->tNG->getPrimaryKeyValue();
				$pk_type = $this->tNG->getColumnType($this->tNG->getPrimaryKey());
				$pk_value = KT_escapeForSql($pk_value, $pk_type);
				$sql .= " AND " . $pk . " <> " . $pk_value;
			}
			$ret = $this->tNG->connection->Execute($sql);
			if ($ret === false) {
				return new tNG_error('CHECK_TF_SQL_ERROR', array(), array($this->tNG->connection->ErrorMsg(), $sql));
			}
			if (!$ret->EOF) {
				$useSavedData = false;
				if (in_array($this->tNG->transactionType, array('_delete', '_multipleDelete'))) {
					$useSavedData = true;
				}
				$this->errorMsg = KT_DynamicData($this->errorMsg, $this->tNG, '', $useSavedData);
				if ($GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT') {
					$err = new tNG_error('TRIGGER_MESSAGE__CHECK_UNIQUE',  array(implode(', ', $this->field)), array());
				} else {
					$err = new tNG_error('%s', array($this->errorMsg), array());
				}
				if (count($this->field)==1 && isset($this->tNG->columns[$this->field[$first]])) {
					// set field error to $this->errorMsg
					$err->setFieldError($this->field[$first], '%s', array($this->errorMsg));
					if ($this->tNG->columns[$this->field[$first]]['method'] != 'POST') {
						// set composed message as user error
						$err->addDetails('%s', array($this->errorMsg), array(''));
					}
				} else {
					// set composed message as user error
					$err->addDetails('%s', array($this->errorMsg), array(''));
				}
				return $err;
			}
			return null;
		}
	}

?>