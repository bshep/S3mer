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
 * Only for PRO version
 * This the CheckForbiddenWords trigger; 
 * Checks if the fields have forbidden words.
 * @access public
 */
class tNG_ChkForbiddenWords {
	/**
	 * The tNG object
	 * @var object tNG
	 * @access public
	 */
	var $tNG;
	/**
	 * The action
	 * @var string action; replace|remove|block
	 * @access public
	 */
	var $action;
	/**
	 * The replacement string
	 * @var string 
	 * @access public
	 */
	var $replaceWith;
	/**
	 * The replacement values for fields if the action is replace/remove
	 * @var array
	 * @access public
	 */
	var $replacements;	
	/**
	 * The error message to be used;
	 * @var string
	 * @access public
	 */
	var $errorMsg;
	/**
	 * table name;
	 * @var string
	 * @access public
	 */
	var $table;
	/**
	 * field name;
	 * @var string
	 * @access public
	 */
	var $field;
	/**
	 * tNG error object
	 * @var object
	 * @access private
	 */
	var $error;
		
	/**
	* Constructor. Sets the reference to the transaction in which the trigger is used.
	* @param object tNG &$tNG reference to transaction object
	* @access public
	*/
	function tNG_ChkForbiddenWords(&$tNG) {
		$this->tNG = &$tNG;
		$this->action = '';
		$this->replaceWith = '*';
		$this->replacements = array();
		$this->table = '';
		$this->field = '';
	}	
	
	/**
	* Setter. Sets the action: remove|replace|block.
	* @param string action
	* @access public
	*/
	function setAction($action) {
		$this->action = strtolower($action);
	}

	/**
	* Setter. Sets the table name
	* @param string table
	* @access public
	*/
	function setTable($table) {
		$this->table = $table;
	}
	
	/**
	* Setter. Sets field name
	* @param string field
	* @access public
	*/
	function setField($field) {
		$this->field = $field;
	}
	
	/**
	* setter. set the error message
	* @param string  error message
	* @return nothing
	* @access public
	*/
	function setErrorMsg($error) {
		$this->errorMsg =  KT_DynamicData($error, $this->tNG, '', false);
	}
	
	/**
	* Main Class method. Sets the action: remove|replace|block.
	* @return mixt object in case of errors or null
	* @access public
	*/
	function Execute() {
		$ret = null;
		$arr = $this->tNG->columns;
		$columns = array();
		foreach ($arr as $colName=>$colDetails) {
			if ($colDetails['type'] == 'STRING_TYPE') {
				$columns[$colName] = $colDetails['value'];
			}
		}
		if (count($columns) == 0) {
			return ;
		}
		$words = $this->getWords();
		if (is_object($this->error)) {
			return $this->error;
		}
		if (count($words) == 0) {
			return ;
		}
		$fieldWithErrors = array();
		foreach ($columns as $colName => $value) {
			if (!$this->checkValue($colName, $value, $words)) {
				$error = true;
				$fieldWithErrors[] = $colName;
			}
		}
		// action block
		if (isset($error) && $this->action == 'block') {
			if ($this->errorMsg == '') {
				$ret = new tNG_error('TRIGGER_MESSAGE__CHECK_FORBIDDEN_WORDS', array(implode(', ', $fieldWithErrors)), array());
			} else {
				$ret = new tNG_error('%s', array($this->errorMsg), array(''));
			}
			$errorMsg = KT_getResource('FORBIDDEN_FIELD_ERROR', 'tNG', array());
			foreach($fieldWithErrors as $colName) {
				// set field error to $errorMsg
				$ret->setFieldError($colName, '%s', array($errorMsg));
				if ($this->tNG->columns[$colName]['method'] != 'POST') {
					// set composed message as user error
					$ret->addDetails('%s', array($errorMsg), array(''));
				}
			} 	
		// action remove/replace
		} else if (isset($error) && $this->action != 'block') {
			foreach ($this->replacements as $colName => $value) {
				$this->tNG->setColumnValue($colName, $value);
			}
		}
		return $ret;
	}	
	/**
	* Verify if the given string has forbidden words.
	* If this.action is different from 'block', replace|remove all the occurence.
	* @param string column name from transaction
	* @param string string to test
	* @param array forbidden words
	* @return boolean true if no forbidden words was found
	* @access public
	*/
	function checkValue($colName, $value, $words) {
		$ret = true;
		reset($words);
		$regexp = "/\b(?:". implode('|', $words) .")\b/ims";
		if (preg_match($regexp, $value, $m)) {
			$ret = false;
			if ($this->action == 'block') {
				return $ret;
			} else if ($this->action == 'replace') {
				$replacement = '$1' . $this->replaceWith . '$2';
				$value = preg_replace($regexp, $replacement, $value);
			// remove	
			} else {
				$replacement = '$1$2';
				$value = preg_replace($regexp, $replacement, $value);
			}
		}
		$this->replacements[$colName] = $value;
		return $ret;
	}
	
	/**
	* Read the forbidden words file and return the words in an array.
	* @return array of forbidden words
	* @access public
	*/
	function getWords() {
		$arr = array();
		if ($this->table != '' && $this->field != '') {
			$sql = 'SELECT '.KT_escapeFieldName($this->field).' AS myfield FROM '.$this->table; 
			$rs = $this->tNG->connection->Execute($sql);
			if ($this->tNG->connection->errorMsg()!='') {
				$this->error = new tNG_error('BADWORDS_SQL_ERROR', array(), array($this->tNG->connection->errorMsg(), $sql));
				return $arr;
			}
			while (!$rs->EOF) {
				$arr[] = trim($rs->Fields('myfield'));
				$rs->MoveNext();
			}
			$rs->Close();
		} else {
			$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tNG_ChkForbiddenWords.txt';
		   	if (!file_exists($file)) {
		   		$this->error = new tNG_error('BADWORDS_FILE_ERROR', array(), array($file));
		    	return $arr;
		   	}
		   	if ($fd = @fopen($file, 'rb')) {
				while (!feof ($fd)) {
		 			$tmp = fgets($fd, 4096);
		 			$tmp = addcslashes($tmp, '/.()[]{}|^$');
		 			if (trim($tmp) != '') {
		  				$arrTmp = explode(',', $tmp);
		      			foreach ($arrTmp as $k => $v) {
		       				$arr[] = trim($v);
		     		 	}
		     		}
		    	}
		    	fclose ($fd);
		   	} else {
		    	$this->error = new tNG_error('BADWORDS_FILE_ERROR', array(), array($file));
				return $arr;
		   	}
		}
		return $arr;
	}	
}
?>