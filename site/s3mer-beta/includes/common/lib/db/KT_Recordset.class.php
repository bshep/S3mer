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
 * The Recordset class use with KT_Connection.
 */
class KT_Recordset {

	/**
	 * The Recordset resource
	 * @var object ResourceID
	 * @access private
	 */
	var $resource = null;

	/**
	 * The returned fields
	 * @var array
	 * @access public
	 */
	var $fields = array();

	/**
	 * Are we at the end of the record?
	 * @var boolean
	 * @access public
	 */
	var $EOF = true;

	/**
	 * The constructor
	 * @param object ResourceID &$resource - the recordset resource id
	 * @access public
	 */
	function KT_Recordset(&$resource) {
		$this->resource = &$resource;
		if (mysql_num_rows($this->resource) > 0) {
			mysql_data_seek($this->resource, 0);
		}
		$this->fields = mysql_fetch_assoc($this->resource);
		$this->EOF = ($this->fields)?false:true;
	}

	/**
	 * Gets the record count
	 * @return integer
	 * @access public
	 */
	function RecordCount() {
		return mysql_num_rows($this->resource);
	}

	/**
	 * Gets the fields(columns) count
	 * @return integer
	 * @access public
	 */
	function FieldCount() {
		return mysql_num_fields($this->resource);
	}

	/**
	 * Gets a column's meta informations
	 * @return object
	 * @access public
	 */
	function FetchField($colNum) {
		$field = mysql_fetch_field($this->resource, $colNum);
		return (object)array('name' => $field->name, 'type' => $field->type, 'max_length' => $field->max_length);
	}

	/**
	 * Returns the value of a field
	 * @param field name
	 * @return mixt null or field value
	 * @access public
	 */
	function Fields($colName) {
		if (isset($this->fields[$colName])) {
			return $this->fields[$colName];
		} else {
			return NULL;
		}
	}

	/**
	 * Moves to the next row
	 * @return boolean
	 *         true if there is a next row
	 *         false otherwise
	 * @access public
	 */
	function MoveNext() {
		$this->fields = mysql_fetch_assoc($this->resource);
		$this->EOF = ($this->fields)?false:true;
		return !$this->EOF;
	}

	/**
	 * Moves to the first row
	 * @return boolean
	 *         true if moved
	 *         false otherwise
	 * @access public
	 */
	function MoveFirst() {
		if (mysql_num_rows($this->resource) > 0) {
			mysql_data_seek($this->resource, 0);
			$this->fields = mysql_fetch_assoc($this->resource);
			$this->EOF = ($this->fields)?false:true;
			return true;
		}
		return false;
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