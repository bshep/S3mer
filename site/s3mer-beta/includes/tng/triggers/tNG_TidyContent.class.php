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
 * This is the Tidy class; 
 * Only for PRO version	 
 * Checks if the fields have forbidden words.
 * @access public
 */
class tNG_TidyContent {
	/**
	 * The tNG object
	 * @var object tNG
	 * @access public
	 */
	var $tNG;
	/**
	 * Column names to be cleaned
	 * @var array
	 * @access public
	 */
	var $columns;
	/**
	 * The error message to be used;
	 * @var string
	 * @access public
	 */
	var $errorMsg;
	
	/**
	 * The denied tags
	 * @var string
	 * @access public
	 */
	var $deniedTagsList;
	/**
	 * allowed tags
	 * @var string
	 * @access public
	 */
	var $allowedTagsList;
	/**
	 * The cleanup values
	 * @var array
	 * @access public
	 */
	var $tidiedValues;
	/**
	 * Encoding for output
	 * @var string
	 * @access public
	 */
	var $outEncoding;
	/**
	 * Temp Folder for output 
	 * @var string
	 * @access public
	 */
	var $folderName;
		
	/**
	* Constructor. Sets the reference to the transaction in which the trigger is used.
	* @param object tNG &$tNG reference to transaction object
	* @access public
	*/
	function tNG_TidyContent(&$tNG) {
		$this->tNG = &$tNG;
		$this->folderName = KT_RealPath($GLOBALS['TidyContent_TidyTempPath'], true);
		$this->columns = array();
		$this->outEncoding = 'ascii';
		$this->deniedTagsList = '';
		$this->allowedTagsList = '';
		$this->tidiedValues = array();
	}	
	/**
	* setter. sets a column name
	* @param string  
	* @access public
	*/
	function addColumn($name) {
		$this->columns[] = $name;
	}
	/**
	* setter. sets the outeconding
	* @param string  
	* @access public
	*/
	function setOutEncoding($outEncoding) {
		$this->outEncoding = $outEncoding;	
	}
	/**
	* setter. sets the allowed tagd
	* @param string  
	* @access public
	*/
	function setAllowedTags($tags) {
		$this->allowedTagsList = str_replace(array('<', '>', ' ', '/'), array('', '', '', ''), $tags);
	}
	/**
	* setter. sets the allowed tagd
	* @param string  
	* @access public
	*/
	function setDeniedTags($tags) {
		$this->deniedTagsList = str_replace(array('<', '>', ' ', '/'), array('', '', '', ''), $tags);	
	}
	/**
	* setter. set the error message
	* @param string  error message
	* @return nothing
	* @access public
	*/
	function setErrorMsg($err1, $err2) {
		if ($GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT') {
			$this->errorMsg =  KT_DynamicData($err2, $this->tNG, '', false);
		} else {
			$this->errorMsg =  KT_DynamicData($err1, $this->tNG, '', false);
		}
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
			if (in_array($colName, $this->columns) && $colDetails['type'] == 'STRING_TYPE') {
				$columns[$colName] = $colDetails['value'];
			}
		}
		if (count($columns) == 0) {
			return ;
		}
		$fieldWithErrors = array();
		foreach ($columns as $colName => $value) {
			$return = $this->execTidy($colName, $value);
			if ($return === false) {
				$error = true;
				$fieldWithErrors[] = $colName;
			} else {
				$columns[$colName] = $this->tidiedValues[$colName];
			}
		}
		// write the values back in tNG
		foreach($columns as $name => $value) {
			$this->tNG->setColumnValue($name, $value);
		}
		if (isset($error)) {
			if ($this->errorMsg == '') {
				$ret = new tNG_error('ERROR_TIDY_CONTENT', array(), array(implode(', ', $fieldWithErrors)));
			} else {
				$ret = new tNG_error('%s', array($this->errorMsg), array($this->errorMsg));
			}			 	
		}
		return $ret;
	}	
	/**
	* executes tidy and gets the value
	* @param string column name from transaction
	* @param string string to test
	* @return boolean true if tidy executed succesfully
	* @access private
	*/
	function execTidy($colName, $value) {
		$arg_test = array('--version');
		if (isset($_SESSION['TidyContent']['ExecPath'])) {
			$loc = array($_SESSION['TidyContent']['ExecPath']);
		} else {
			$loc = $GLOBALS['TidyContent_TidyLocations'];
			if (isset($GLOBALS['KT_prefered_tidy_path'])) {
				array_unshift($loc, $GLOBALS['KT_prefered_tidy_path'].'tidy');
				array_unshift($loc, $GLOBALS['KT_prefered_tidy_path'].'tidy.exe');
			}
		}

		$shell = new KT_shell();
		$shell->execute($loc, $arg_test);
		if ($shell->hasError()) {
			$arr = $shell->getError();
			$this->setErrorMsg($arr[0], $arr[1]);
			return false;
		}
		$execPath = $shell->getExecutedCommand();
		if (!isset($_SESSION['TidyContent']['ExecPath']) && $execPath != '') {
			$_SESSION['TidyContent']['ExecPath'] = $execPath;
			$loc = array($execPath);
		}
		
		$tidyEncoding = 'raw';
		if (strtolower($this->outEncoding) == 'iso-8859-1') {
			$tidyEncoding = 'ascii';
		}
		if (strpos(strtolower($this->outEncoding), 'utf-8') !== false) {
			$tidyEncoding = 'utf8';
		}

		$string = $value;
		$string = str_replace("&amp;nbsp;", "&amp;amp;nbsp;", $string);
		$string = str_replace("&nbsp;", "&amp;nbsp;", $string);

		if (!file_exists($this->folderName)) {
			$folder = new KT_folder();
			$folder->createFolder($this->folderName); 
			if ($folder->hasError()) {
				$arr = $folder->getError();
				$this->setErrorMsg($arr[0], $arr[1]);
				return false;
			}
		}

		$f = tempnam(substr($this->folderName,0,-1), 'tidy');
		if ($f === false) {
			$err = KT_getResource('ERROR_TIDY_CONTENT', 'tNG', array());
			$this->setErrorMsg($err, $err);
			return false;
		}
		$fout = $f.'_out';

		$file = new KT_file();
		$file->writeFile($f,'append', $string); 
		if ($file->hasError()) {
			$arr = $file->getError();
			$this->setErrorMsg($arr[0], $arr[1]);
			return false;
		}
		$path = $GLOBALS['TidyContent_TidyConfiguration'];
		$arg = array(
					"-config",
					$path,
					'-' . $tidyEncoding,
					"-o",
					$fout,
					$f
				);
		
		$shell = new KT_shell();
		$output = $shell->execute($loc, $arg);
		if ($shell->hasError() && !file_exists($fout)) {
			$arr = $shell->getError();
			$this->setErrorMsg($arr[0], $arr[1]);
			@unlink($f);
			@unlink($fout);
			return false;
		}
		
		$file = new KT_file();
		$content = $file->readFile($fout);
		if ($file->hasError()) {
			$arr = $file->getError();
			$this->setErrorMsg($arr[0], $arr[1]);
			@unlink($f);
			@unlink($fout);
			return false;
		}
		$file->deleteFile($f);
		if ($file->hasError()) {
			$arr = $file->getError();
			$this->setErrorMsg($arr[0], $arr[1]);
			@unlink($fout);
			return false;
		}
		$file->deleteFile($fout);
		if ($file->hasError()) {
			$arr = $file->getError();
			$this->setErrorMsg($arr[0], $arr[1]);
			return false;
		}
		$content = str_replace("&amp;nbsp;", "&nbsp;", $content);
		$content = str_replace("&amp;amp;nbsp;", "&amp;nbsp;", $content);
		$content = $this->cleanContent($content);
		$this->tidiedValues[$colName] = $content;
		return true;
	}
	
	/**
	 * Cleanup content 
	 * @param string content to clean;
	 * @return string cleaned content;
	 * @access private
	 */
	function cleanContent($content) {
		return KT_cleanContent($content, $this->deniedTagsList, $this->allowedTagsList);		
	}	

}
?>