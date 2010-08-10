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
 * This class is the "delete" implementation of the tNG_fields class.
 * @access public
 */
class tNG_delete extends tNG_fields {

	/**
	 * Constructor. Sets the connection, the database name and other default values.
	 * Also sets the transaction type.
	 * @param object KT_Connection &$connection the connection object
	 * @access public
	 */
	function tNG_delete(&$connection) {
		parent::tNG_fields($connection);
		$this->transactionType = '_delete';
		$this->registerTrigger("BEFORE", "Trigger_Default_saveData", -1);
	}
	
	/**
	 * Prepares the delete SQL query to be executed
	 * @access protected
	 */
	function prepareSQL() {
		tNG_log::log('tNG_delete', 'prepareSQL', 'begin');
		parent::prepareSQL();
		// check if we have a valid primaryKey
		if (!$this->primaryKey) {
			$ret = new tNG_error('DEL_NO_PK_SET', array(), array());
		}

		// check the primary key value
		if (!isset($this->primaryKeyColumn['value'])) {
			$ret = new tNG_error('DEL_NO_PK_VAL', array(), array());
		}

		$ret = null;
		$sql = 'DELETE FROM ' . $this->table . ' WHERE ' . KT_escapeFieldName($this->primaryKey) . ' = ';
		$sql .= KT_escapeForSql($this->primaryKeyColumn['value'], $this->primaryKeyColumn['type']);
		$this->setSQL($sql);
		tNG_log::log('tNG_delete', 'prepareSQL', 'end');
		return $ret;
	}

	/**
	 * This function exits because the current class does not export a recordset.
	 * @access protected
	 */
	function getLocalRecordset() {
		$this->setError(new tNG_error('DEL_NO_RS', array(), array()));
	}
}
?>