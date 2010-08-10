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
 * This class is the "import" implementation of the tNG_multiple class.
 * @access public
 */
class tNG_import extends tNG_multiple {
	/**
	 * Set to true if the import transaction uses headers to describe the data columns
	 * @see headers
	 * @var boolean
	 * @access protected
	 */
	var $hasHeader;
	
	/**
	 * if is false will check set the error on each transaction and will reset the Number of transactions executed successfully
	 * @param boolean
	 * @access protected
	 */
	var $executeSubSets;
	
	/**
	 * The way duplicate records are handled (skip, update database record, throw error)
	 * @param string
	 * @access protected
	 */
	var $handleDuplicates;
	
	/**
	 * Structure storing the type and the reference of the source
	 * @param array
	 * @access protected
	 */
	var $source;
	
	/**
	 * Structure used to store the import data. Represented as a 2-dimensional array
	 * @var array
	 * @access protected
	 */
	var $data;
	
	/**
	 * List of headers (column names) used to map data to columns
	 * If the import transaction doesn't use headers, then this structure is empty.
	 * @see hasHeader
	 * @var array
	 * @access protected
	 */
	var $headers;
	
	/**
	 * Unique key field used to identofy duplicates
	 * @param string
	 * @access protected
	 */
	var $uniqueKey;
	
	/**
	 * The import type
	 * Depending on the class that extends the base class, this changes.
	 * @var string
	 * @access protected
	 */
	var $importType;
	
	/**
	 * The current imported row values reference
	 * Stores the data corresponding to the currently imported row for use in Common functions
	 * @var array
	 * @access public
	 */
	var $importReference;
	
	/**
	 * The data start row value
	 * @var integer
	 * @access protected
	 */
	var $lineStart;
	
	/**
	 * Constructor. Sets the connection, the database name and other default values.
	 * Also sets the transaction type.
	 * @param object KT_Connection &$connection the connection object
	 * @access public
	 */
	function tNG_import(&$connection) {
		parent::tNG_multiple($connection);
		$this->transactionType = '_import';
		$this->exportRecordset = false;
		$this->source = array();
		$this->data = array();
		$this->headers = array();
		$this->hasHeader = false;
		$this->importType = '';
		$this->importReference = '';
		$this->lineStart = 0;
	}
	
	/**
	 * Sets the source of the import data
	 * @param string $type The type of the source (GET, POST, FILES)
	 * @param string $reference The submitted variable name (if type=FILES and reference=test, value=$_FILES['test'])
	 * @access public
	 */
	function setSource($type, $reference) {
		$this->source['type'] = strtoupper(trim($type));
		$this->source['reference'] = trim($reference);
	}
	
	/**
	 * Sets the unique key column
	 * @param string $uniqueKey The name of the unique key column
	 * @access public
	 */
	function setUniqueKey($uniqueKey) {
		if ($uniqueKey != '') {
			if (!isset($this->columns[$uniqueKey])) {
				die('tNG_Import.setUniqueKey:<br /> Unique Key Column ' . $uniqueKey . ' is not part of the current transaction.');
			}
		}	
		$this->uniqueKey = $uniqueKey;
	}
	
	/**
	 * Sets the way duplicates are handled
	 * @param string $handleDuplicates Handle duplicates type
	 * @access public
	 */
	function setHandleDuplicates($handleDuplicates) {
		$this->handleDuplicates = strtoupper(trim($handleDuplicates));
	}
	
	
	/**
	 * Abstract method; must be implemented by the inherited classes;
	 * @params none
	 * @return nothing
	 */
	function prepareData() {
		die('tNG_import.prepareData:<br />Method must be implemented in inherited class.');
	}
	
	/**
	 * Executes all sub-transactions
	 * @access protected
	 */
	function prepareSQL() {
		tNG_log::log('tNG_import', 'prepareSQL', 'begin');
		
		$ret = $this->prepareData();
		if ($ret === null) {
			$this->noSuccess = 0;
			$this->noSkip = 0;
			$failed = false;
			$line = $this->lineStart;
			
			$tNGindex = 1;
			
			for ($k = 0; $k < count($this->data); $k++) {
				$dataarr = $this->data[$k];
				$skipped = false;
				$line++;
				
				/*
				if ( !is_array($dataarr) || count($dataarr) < 1 || (count($dataarr) == 1 && reset($dataarr) == '') ) {
					// skip empty lines
					continue;
				}
				*/
				
				// exports the values line to be available for KT_getRealValue and KT_DynamicData
				unset($GLOBALS[$this->importReference]);
				$GLOBALS[$this->importReference] = $dataarr;
				
				unset($GLOBALS[$this->importReference . '_LINE']);
				$GLOBALS[$this->importReference . '_LINE'] = $line;
				
				$isInsert = true;
				$uniqueColName = $this->uniqueKey;
				if ($uniqueColName != '') {
					$uniqueColDetails = $this->computeMultipleValues($this->columns[$uniqueColName], $tNGindex);
					if ($uniqueColDetails['value'] != '') {
						$sql = 'SELECT ' . KT_escapeFieldName($uniqueColName) . ' FROM ' . $this->getTable() . ' WHERE ' . KT_escapeFieldName($uniqueColName) . ' = ' . KT_escapeForSql($uniqueColDetails['value'], $uniqueColDetails['type']);
						$rs = $this->connection->Execute($sql);
						if ($rs === false) {
							$failed = true;
							$ret = new tNG_error('IMPORT_SQL_ERROR', array(), array($sql, $this->connection->ErrorMsg()));
							tNG_log::log('KT_ERROR');
							break;
						}
						if ($rs->recordCount() >= 1) {
							// duplicates found
							if ($this->handleDuplicates == "SKIP") {
								// ignore case
								$isInsert = false;
								$this->noSkip++;
								continue;
							}
							if ($this->handleDuplicates == "UPDATE") {
								// update case
								$isInsert = false;
								$this->multTNGs[$tNGindex-1] = new tNG_update($this->connection);
							}
							if ($this->handleDuplicates == "SKIPWITHERROR") {
								// throw error case
								$isInsert = false;
								$skipped = true;
								$this->noSkip++;
								$this->multTNGs[$tNGindex-1] = new tNG_insert($this->connection);
								$this->multTNGs[$tNGindex-1]->setError(new tNG_error($this->importType . '_IMPORT_DUPLICATE_ERROR', array($line, $uniqueColDetails['value'], $uniqueColName), array()));
							}
						}
					}
				}
				
				if ($isInsert) {
					$this->multTNGs[$tNGindex-1] = new tNG_insert($this->connection);
				}
				
				$this->multTNGs[$tNGindex-1]->setDispatcher($this->dispatcher);
				$this->multTNGs[$tNGindex-1]->multipleIdx = $tNGindex;
				// register triggers
				for ($j=0;$j<sizeof($this->multTriggers);$j++) {
					call_user_func_array(array(&$this->multTNGs[$tNGindex-1], "registerConditionalTrigger"), $this->multTriggers[$j]);
				}
				$this->multTNGs[$tNGindex-1]->setTable($this->table);
				// add columns
				
				foreach($this->columns as $colName=>$colDetails) {
					$colDetails = $this->computeMultipleValues($colDetails, $tNGindex);
					$this->columns[$colName]['value'] = $colDetails['value'];
					if ($this->multTNGs[$tNGindex-1]->transactionType == '_update') {
						if ($colName != $uniqueColName) {
							$this->multTNGs[$tNGindex-1]->addColumn($colName, $colDetails['type'], $colDetails['method'], $colDetails['reference']);
						}
					} else {
						$this->multTNGs[$tNGindex-1]->addColumn($colName, $colDetails['type'], $colDetails['method'], $colDetails['reference'], $colDetails['default']);
					}
				}
				
				if ($this->multTNGs[$tNGindex-1]->transactionType == '_update') {
					$this->multTNGs[$tNGindex-1]->setPrimaryKey($uniqueColName, $uniqueColDetails['type'], 'VALUE', $uniqueColDetails['value']);
				} else {
					$this->multTNGs[$tNGindex-1]->setPrimaryKey($this->primaryKey, $this->primaryKeyColumn['type']);
				}
				$this->multTNGs[$tNGindex-1]->compileColumnsValues();
				
				if ($this->getError()) {
					$this->multTNGs[$tNGindex-1]->setError($this->getError());
				}
				
				$this->multTNGs[$tNGindex-1]->setStarted(true);
				$this->multTNGs[$tNGindex-1]->doTransaction();
				
				if (!$skipped) {
					if ($this->multTNGs[$tNGindex-1]->getError()) {
						$err = $this->multTNGs[$tNGindex-1]->getError();
						$tmp_all_errmsg = '';
						$tmp_unique_details = '';
						if ($uniqueColName != '') {
							if ($uniqueColDetails['value'] != '') {
								$tmp_unique_details = ' (' . $uniqueColName . ' = ' . $uniqueColDetails['value'] . ')';
							}
						}
						foreach($err->fieldErrors as $tmp_col => $tmp_errmsg) {
							$tmp_all_errmsg .= "\n<br />&nbsp;&nbsp;&nbsp;- ".$tmp_col." : ".$tmp_errmsg;
						}
						if ($tmp_all_errmsg == '') {
							$tmp_all_errmsg = $err->getDetails();
						}
						$lineErr = $line . $tmp_unique_details;
						$newErr = new tNG_error($this->importType . '_IMPORT_LINE_ERROR',array($lineErr, $tmp_all_errmsg), array());
						$this->multTNGs[$tNGindex-1]->setError($newErr);
						$failed = true;
					} else {
						$this->noSuccess++;
						if ($this->getPrimaryKey() == $this->multTNGs[$tNGindex-1]->getPrimaryKey()) {
							$this->primaryKeyColumn['value'] = $this->multTNGs[$tNGindex-1]->getPrimaryKeyValue();
						}
					}
				}
				
				$tNGindex++;
			}
			
			if (!$failed) {
				for ($i=0;$i<sizeof($this->multTNGs);$i++) {
					if ($this->multTNGs[$i]->getError()) {
						$failed = true;
						$ret = new tNG_error('IMPORT_SKIPPED', array(), array());
						tNG_log::log('KT_ERROR');
						break;
					}
				}
			}
		
			if ($failed) {
				if ($ret === null) {
					$ret = new tNG_error('IMPORT_ERROR', array(), array());
					tNG_log::log('KT_ERROR');
				}
				if ($this->executeSubSets === false) {
					for ($i=0;$i<sizeof($this->multTNGs);$i++) {
						if (!$this->multTNGs[$i]->getError()) {
							$this->multTNGs[$i]->setError($ret);
							$this->multTNGs[$i]->executeTriggers('ERROR');
						}
					}
				}
			}
			
			if ($this->executeSubSets === false) {
				$this->noSuccess = 0;
			}
			
		} else {
			tNG_log::log('KT_ERROR');
		}
		
		tNG_log::log('tNG_import', 'prepareSQL', 'end');
		return $ret;
	}
	
	/**
	 * Sets the column details corresponding to its method and current transaction index
	 * @param array $colDetails Column details (one element of the $column array)
	 * @param integer $tNGindex The current transaction's index
	 * @return array $colDetails
	 * @access private
	 */
	function computeMultipleValues($colDetails, $tNGindex) {
		if ($colDetails['method'] == 'VALUE') {
			$reference = $colDetails['reference'];
			$value = KT_getRealValue($colDetails['method'], $colDetails['reference']);
		} elseif ($colDetails['method'] == $this->importType) {
			$value = KT_getRealValue($colDetails['method'], $this->headers[$colDetails['reference']]);
			$reference = $this->headers[$colDetails['reference']];
		} else {
			$reference = $colDetails['reference'] . '_' . $tNGindex;
			$value = KT_getRealValue($colDetails['method'], $colDetails['reference'] . '_' . $tNGindex);
			if (!isset($value)) {
				$reference = $colDetails['reference'];
				$value = KT_getRealValue($colDetails['method'], $colDetails['reference']);
			}
		}
		$colDetails['value'] = $value;
		$colDetails['reference'] = $reference;
		return $colDetails;
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
		if ($method == 'VALUE') {
			$this->columns[$colName]['default'] = $reference;
		} else {
			if ($method == $this->importType) {
				$this->headers[$reference] = $reference;
			}
			$this->columns[$colName]['default'] = $defaultValue;
		}
	}
	
	/**
	 * Gets the error message
	 * Adds the import specific messages, then calls the parent getErrorMsg method.
	 * @return string transaction error message (formatted)
	 * @access public
	 */
	function getErrorMsg() {
		$ret_warning = '';
		if (isset($this->noSkip) && $this->noSkip!=0) {
			$ret_warning = KT_getResource('IMPORT_OPERATIONS_SKIPPED','tNG',array($this->noSkip));
		}
		
		$ret = parent::getErrorMsg();
		$ret[0] .= $ret_warning;
		$ret[0] = trim($ret[0]);
		return $ret;
	}
	
	/**
	 * No data needs to be saved on insert. 
	 * @param none
	 * @return nothing
	 * @access public
	 */
	function saveData() {
		return;
	}
}
?>