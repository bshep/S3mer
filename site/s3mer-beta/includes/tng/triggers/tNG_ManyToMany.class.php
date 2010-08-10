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
 * many to many class;
 * @access public
 */
class tNG_ManyToMany{
	/**
	 * transaction object
	 * @var object tNG
	 * @access public
	 */
	var $tNG;
	/**
	 * table name
	 * @var string
	 * @access public
	 */
	var $table;
	/**
	 * primarey key name
	 * @var string
	 * @access public
	 */
	var $pkName;
	/**
	 * foreign key name
	 * @var string
	 * @access public
	 */
	var $fkName;
	/**
	 * reference to the foreign key
	 * @var string tNG
	 * @access public
	 */
	var $fkReference;
	/**
	 * extra columns
	 * @var array
	 * @access public
	 */
	var $columns;
	/**
	 * validation rules for extra columns;
	 * @var array
	 * @access public
	 */
	var $validation;
	
	/* presupunem ca
		cimpurile din tabela mtm au acelasi tip cu pk-ul
		foreign-key-ul din mtm catre tabela curenta e chiar pk-ul
		metoda e POST
	*/
	/**
	 * Constructor. set tNG object and default values for other variables
	 * @param object tNG transaction
	 * @access public
	 */
	function tNG_ManyToMany(&$tNG) {
		$this->tNG = &$tNG;
		$this->table = '';
		$this->pkName = '';
		$this->fkName = '';
		$this->fkReference = '';
		$this->columns = array();
		$this->validation = array();
	}
	/**
	 * setter. set the table name
	 * @param string 
	 * @access public
	 */
	function setTable($table){
		$this->table = trim($table);
	}
	/**
	 * setter. set the table name
	 * @param string 
	 * @access public
	 */
	function setTableFk($table){
		$this->tableFk = trim($table);
	}
	/**
	 * setter. set primary key name
	 * @param string 
	 * @access public
	 */
	function setPkName($pkName){
		$this->pkName = trim($pkName);
	}
	/**
	 * setter. set foreign key name
	 * @param string 
	 * @access public
	 */
	function setFkName($fkName){
		$this->fkName = trim($fkName);
	}
	/**
	 * setter. set foreign key reference
	 * @param string 
	 * @access public
	 */
	function setFkReference($fkReference){
		$this->fkReference = trim($fkReference);
	}
	/**
	 * setter. add extra field 
	 * @param string column name
	 * @param string type
	 * @param string method
	 * @param string reference
	 * @param string default value 
	 * @access public
	 */
	function addField($colName, $type, $method, $reference, $default = ""){
		if (!isset($this->columns[$colName])) {
			$this->columns[$colName] = array();
		}
		$this->columns[$colName]['type'] = $type;
		$this->columns[$colName]['method'] = $method;
		$this->columns[$colName]['reference'] = $reference;
		$this->columns[$colName]['default'] = $default;

		if ($method == 'VALUE') {
			$this->columns[$colName]['value'] = $reference;
			return;
		}
		if ($method == 'EXPRESSION') {
			$this->columns[$colName]['method'] = "VALUE";
		}
		if ($default != '') {
			$this->columns[$colName]['value'] = KT_DynamicData($default, $this->tNG, '');
		} else {
			$this->columns[$colName]['value'] = KT_DynamicData($reference, $this->tNG, '');
		}
		if ($type == 'DATE_TYPE') {
			$this->columns[$colName]['value'] = KT_formatDate2DB($this->columns[$colName]['value']);
		}
	}
	
	/*function addValidationRule($fieldName, $required, $validationType, $format, $min, $max, $errorMessage) {
		$this->validation[$fieldName] = array($fieldName, $required, $validationType, $format, $min, $max, $errorMessage);
	}*/
	
	/**
	 * Get all the new values for foreign key into an array
	 * @return array
	 * @access public
	 */
	function getValues() {
		$values = array();
		
		$fkReference = $this->fkReference;
		$idxReference = "";
		if (isset($this->tNG->multipleIdx)) {
			$idxReference = '_' . $this->tNG->multipleIdx;
			$idxReference = preg_quote($idxReference, '/');
		}
		$fkReference = preg_quote($fkReference, '/');
		$keys = array_keys($_POST);
		foreach ($keys as $idx => $key) {
			if (preg_match('/^' . $fkReference . '_(\d+)'.$idxReference.'$/', $key, $matches)) {
				array_push($values, $matches[1]);
			}
		}
		return $values;
	}
	/**
	 * Get all the old values for foreign key into an array
	 * @return array
	 * @access public
	 */
	function getOldValues() {
		$ret = array();
		$pk_value = $this->tNG->getPrimaryKeyValue();
		$pk_type = $this->tNG->getColumnType($this->tNG->getPrimaryKey());
		$pk_value = KT_escapeForSql($pk_value, $pk_type);
		$sql = "SELECT ".KT_escapeFieldName($this->fkName)." FROM " . $this->table . " WHERE " . KT_escapeFieldName($this->pkName) . " = " . $pk_value;
		$rs = $this->tNG->connection->Execute($sql);
		if ($rs === false) {
			return new tNG_error('TRIGGER_MESSAGE__MTM_SQL_ERROR', array(), array($this->tNG->connection->ErrorMsg(), $sql));
		}
		while (!$rs->EOF) {
			$ret[] = $rs->Fields($this->fkName);
			$rs->MoveNext();
		}
		return $ret;
	}
	/**
	 * Main method of the class. Execute the code.
	 * Insert the new values and delete the old values
	 * @return mix null or error object
	 * @access public
	 */
	function Execute() {
		if ($this->fkReference == '') {
			return new tNG_error('TRIGGER_MESSAGE__MTM_NO_REFERENCE', array(), array());
		}
		$pk_value = $this->tNG->getPrimaryKeyValue();
		$pk_type = $this->tNG->getColumnType($this->tNG->getPrimaryKey());
		$pk_value = KT_escapeForSql($pk_value, $pk_type);
		
		$values = $this->getValues();
		$oldValues = $this->getOldValues();
		if (is_object($oldValues)) {// Returned error message
			return $oldValues;
		}
		if (count($oldValues) >0) {
			$deleteValues = array_diff($oldValues, $values);
			if (count($deleteValues) >0) {
				$in_sql = "";
				foreach ($deleteValues as $key => $value) {
					if ($in_sql != '') {
						$in_sql .= ",";
					}
					$in_sql .= KT_escapeForSql($value,$pk_type);					
				}
				$sql = "DELETE FROM " . $this->table . " WHERE " . KT_escapeFieldName($this->pkName) . " = " . $pk_value." AND ".KT_escapeFieldName($this->fkName)." IN (".$in_sql.")";
				$this->tNG->connection->Execute($sql);
				if ($this->tNG->connection->ErrorMsg()!='') {
					return new tNG_error('TRIGGER_MESSAGE__MTM_SQL_ERROR', array(), array($this->tNG->connection->ErrorMsg(), $sql));
				}
			}
			// update existing values;
			$updateValues = array_intersect($oldValues, $values);
			if (count($updateValues) > 0) {
				foreach ($updateValues as $key => $value) {
					$arrExtraColumns = $this->getExtraColumnsValues($value, $updateValues);
					if (isset($arrExtraColumns['validate']) && is_object($arrExtraColumns['validate'])) {
						return $arrExtraColumns['validate'];
					}
					if (isset($arrExtraColumns['update']) && count($arrExtraColumns['update']) > 0) {
						$sql = "UPDATE " . $this->table . " SET ". implode(', ', $arrExtraColumns['update'])  ." WHERE ". KT_escapeFieldName($this->pkName) . " = " . $pk_value." AND ". KT_escapeFieldName($this->fkName)." = ". $value;
						$this->tNG->connection->Execute($sql);
						if ($this->tNG->connection->ErrorMsg()!='') {
							return new tNG_error('TRIGGER_MESSAGE__MTM_SQL_ERROR', array(), array($this->tNG->connection->ErrorMsg(), $sql));
						}
					}
				}
			}
		}
		if (count($values)>0) {
			$insertValues = array_diff($values, $oldValues);
			if (count($insertValues) >0) {
				foreach ($insertValues as $key => $value) {
					$arrExtraColumns = $this->getExtraColumnsValues($value, $insertValues);
					if (isset($arrExtraColumns['validate']) && is_object($arrExtraColumns['validate'])) {
						return $arrExtraColumns['validate'];
					}
					$extraCols = '';
					$extraVals = '';
					if (count($arrExtraColumns) > 0 && count($arrExtraColumns['cols']) > 0 && count($arrExtraColumns['values']) > 0) {
						$extraCols = ', ' . implode(', ', $arrExtraColumns['cols']);
						$extraVals = ', ' . implode(', ', $arrExtraColumns['values']);
					}
					$value = KT_escapeForSql($value, $pk_type);
					$sql = "INSERT INTO " . $this->table . " ( " . KT_escapeFieldName($this->pkName) . " , " . KT_escapeFieldName($this->fkName) . $extraCols . ") VALUES (" . $pk_value . " , " . $value . $extraVals . ")";
					$ret = $this->tNG->connection->Execute($sql);
					if ($ret === false) {
						return new tNG_error('TRIGGER_MESSAGE__MTM_SQL_ERROR', array(), array($this->tNG->connection->ErrorMsg(), $sql));
					}
				}
			}
		}
		return null;
	}
	 
	/**
	 * Return the values for extra columns to use in insert/update SQL;
	 * Only for PRO version	 
	 * @param string foreign key value
	 * @param array selected values
	 * @return array
	 * @access public
	 */
	function getExtraColumnsValues($fk, $insertValues) {
		$arr = array();
		if (!in_array($fk, $insertValues)) {
			return $arr;	
		}	
		if (count($this->columns) > 0) {
			$arr['cols'] = array();
			$arr['values'] = array();
			$arr['update'] = array();
			$fkReference = $this->fkReference;
			$idxReference = "";
			if (isset($this->tNG->multipleIdx)) {
				$idxReference = '_' . $this->tNG->multipleIdx;
				$idxReference = preg_quote($idxReference, '/');
			}
			$fkReference = preg_quote($fkReference, '/');

			foreach ($this->columns as $colName => $arrTmp) {
				$arr['cols'][] = KT_escapeFieldName($colName);
				if ($arrTmp['method'] == 'VALUE') {
					$arr['values'][] = KT_escapeForSql($arrTmp['value'], $arrTmp['type'], false);
					$arr['update'][] =  KT_escapeFieldName($colName) . '=' . $arr['values'][count($arr['values'])-1];
				} else {
					$found = false;
					foreach ($_POST as $key => $val) {
						if (preg_match('/^' . $fkReference .'_'.$colName. '_'.$fk.$idxReference.'$/', $key)) {
							if ($arrTmp['type'] == 'DATE_TYPE') {
								$val = KT_formatDate2DB($val);
							}
							$arr['values'][] = KT_escapeForSql($val, $arrTmp['type'], false);
							$arr['update'][] =  KT_escapeFieldName($colName) . '=' . $arr['values'][count($arr['values'])-1];
							$found = true;
							break;
						}
					}
					if (!$found && $this->columns[$colName]['default'] != '') {
						$val = KT_DynamicData($this->columns[$colName]['default'], null);
						if ($this->columns[$colName]['type'] == 'DATE_TYPE') {
							$val = KT_formatDate2DB($val);
						}
						$arr['values'][] = KT_escapeForSql($val, $arrTmp['type'], false);
						$arr['update'][] =  KT_escapeFieldName($colName) . '=' . $arr['values'][count($arr['values'])-1];
					}
				}				
			}			
		}
		
		return $arr;
	}
	
}
?>