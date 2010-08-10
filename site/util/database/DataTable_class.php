<?php

require_once 'DB.php';
require_once 'Database_class.php';


class DataTable {
	var $_db;
	var $tableName;
	var $fields;
	var $keys;
	
	function count($filter = null) {
		if ($filter == null) {
			$where_clause = " 1 = 1";
		} else {
			$where_clause = $this->prepareWhereClauseFromFilter($filter);
		}
		
		$stmt = "SELECT COUNT(*) FROM ".$this->tableName." WHERE ".$where_clause;
		
		$count = $this->_db->DatabaseBackend->getOne($stmt);
		
		return $count;
	}
	
	function DataTable($db) {
		$fields = array();
		$keys = array();
		
		$this->_db = $db;
	}
	
	function init() {
		$this->populateFields();	
	}
	
	function populateFields() {
		$this->_db->DatabaseBackend->setFetchMode(DB_FETCHMODE_ASSOC);

		$field_schema = $this->_db->DatabaseBackend->getAll("SHOW FIELDS FROM ".$this->tableName);

		foreach( $field_schema as $key => $field ) {
			$this->fields[$field_schema[$key]["Field"]] = null;	
		}		

	}
	
	function getAll($filter, $maxitems = -1, $offset = 0) {
		$whereClause = $this->prepareWhereClauseFromFilter($filter);

		$fieldsClause = $this->prepareFields();
		
		$select_stmt = "SELECT ".$fieldsClause." FROM ".$this->tableName." WHERE ".$whereClause;
		
		$this->_db->DatabaseBackend->setFetchMode(DB_FETCHMODE_ASSOC);
		
		if ($maxitems > -1 && $offset >= 0) {
			$select_stmt .= " LIMIT ".$offset.", ".$maxitems;
		}

		$ret = $this->_db->DatabaseBackend->getAll($select_stmt);
		
		return $ret;
	}
	
	function updateRow() {
		$whereClause = $this->prepareWhereClauseFromKeys();
		
		$fieldsClause = $this->prepareFieldsUpdate();
		
		$select_stmt = "UPDATE ".$this->tableName." SET ".$fieldsClause." WHERE ".$whereClause;	
		
		
		return $this->_db->execute_query($select_stmt);
}
	
	function prepareFields() {
		$fieldsClause = "";
		foreach ($this->fields as $key => $field) {
			if ($fieldsClause <> "") {
				$fieldsClause .= ", ";	
			}
			
			$fieldsClause .= $key;
		}
		
		return $fieldsClause;
	}
	
	function prepareFieldsUpdate() {
		$fieldsClause = "";
		foreach ($this->fields as $key => $field) {
			if( is_null($field) ) {
				continue;	
			}
			
			if ($fieldsClause <> "") {
				$fieldsClause .= ", ";	
			}
			
			$value = iconv("UTF-8","ISO-8859-1",$this->fields[$key]);

			$fieldsClause .= $key." = '".$this->_db->escape_string($value)."'";
		}
		
		return $fieldsClause;
	
	}
	
	function prepareWhereClauseFromFilter($filter) {
		$whereClause = "";
		foreach ($filter as $key => $value) {
			$whereClause .= $filter[$key]->getFilterString();
		}
		
		return $whereClause;
	}

	function prepareWhereClauseFromKeys() {
		$filter = array();
		
		foreach ($this->keys as $key => $value) {
			$val = $this->fields[$key];
			
			$val = iconv("UTF-8","ISO-8859-1",$val);
			
			if (count($filter) == 0) { 
				$filter[] =  new TableFilter($key,"=",$val,true,"");
			} else {
				$filter[] =  new TableFilter($key,"=",$val,true,"AND");
			}
		}
		
		return $this->prepareWhereClauseFromFilter($filter);
	}
	
}

class TableField { 
	var $fieldname;
	var $value;
	var $dirty;
}

class TableFilter {
	var $field;
	var $operator;
	var $value;
	var $quote;
	var $booleanoper;
	
	function TableFilter($_field, $_operator, $_value, $_quote, $_booleanoper) {
		$this->field = $_field;
		$this->operator = $_operator;
		$this->value = $_value;
		$this->quote = $_quote;	
		$this->booleanoper = $_booleanoper;
	}
	
	function getFilterString() {
		$mystring = " ".$this->booleanoper." ".$this->field." ".$this->operator." ";
		
		if ($this->quote == true) {
			$mystring .= '\''.$this->value.'\'';
		} else {
			$mystring .= $this->value;
		}
		
		return $mystring;
	}
}
?>