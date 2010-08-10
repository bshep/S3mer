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
* Provides functionalities for handling files;
* @access public
*/
class KT_file
{
	/**
	 * error message to be displayed as User Error
	 * @var array $errorType
	 * @access private
	 */
	var $errorType = array();
	/**
	 * error message to be displayed as Developer Error
	 * @var array $develErrorMessage
	 * @access private
	 */
	var $develErrorMessage = array();
	
	/**
	 * Constructor. Doing nothing.
	 */
	function KT_file()
	{
	}

	/**
	 * Read and return the content of the given file;
	 * @param string $file the path to the file to be read;
	 * @return string nothing or the content of the file;
	 * @access public
	 */
	function readFile($file) 
	{
		$this->checkFolder($file, 'read', 'Read content');
		if ($this->hasError()) {
			return;
		}
		if ($fd = @fopen($file, 'rb')) {
			$content = fread ($fd, filesize ($file)); 
			@fclose($fd);
			return $content;
		} else {
			$this->setError('PHP_FILE_READ_ERROR', array(), array($file));
		}
	}
	
	/**
	 * Write the content to the given file;
	 * @param string $file the path to the file to be read;
	 * @param string $mode has value: truncate, prepend, append 
	 * @param string $content	the content to be written;
	 * @return nothing;
	 * @access public
	 */
	function writeFile($file, $mode, $content) 
	{
		$this->checkFolder($file, 'write', 'Write content');
		if ($this->hasError()) {
			return;
		}
		
		switch (strtolower($mode)) {
			case 'truncate':
				$m = 'w';
				break;
			case 'prepend':
				$m = 'r+';
				break;
			case 'append':
			default:
				$m = 'a';
				break;
		}
		$m .= 'b'; 
				
		if ($fd = @fopen($file, $m)) {
			@fwrite($fd, $content); 
			@fclose($fd);
		} else {
			$this->setError('PHP_FILE_WRITE_ERROR', array(), array($file));
		}		
	}
	
	/**
	 * Create an empty file;
	 * @param string $file the path of the file to be created;
	 * @return nothing;
	 * @access public
	 */
	function createFile($file)
	{
		$this->checkFolder($file, 'write', 'Create file');
		if ($this->hasError()) {
			return;
		}
		$fd = fopen($file, 'ab');
		if (!$fd) {
			$this->setError('PHP_FILE_CREATE_ERROR', array(), array($file));
		} else {
			fclose($fd);
		}
	}
	
	/**
	 * Delete a file;
	 * @param string $file the path of the file to be deleted;
	 * @return nothing;
	 * @access public
	 */	
	function deleteFile($file)
	{
		$this->checkFolder($file, 'write', 'Delete File');
		if ($this->hasError()) {
			return;
		}
		if (!@unlink($file)) {
			$this->setError('PHP_FILE_DELETE_ERROR', array(), array($file));
		}
	}
	
	/**
	 * Rename a file;
	 * @param string $file file to be renamed;
	 * @param string $newfile final name of the file;
	 * @return nothing;
	 * @access public
	 */	
	function renameFile($file, $newfile) {
		$this->checkFolder($file, 'write', 'Rename File');
		if ($this->hasError()) {
			return;
		}
		$this->checkFolder($newfile, 'write', 'Rename File');
		if ($this->hasError()) {
			return;
		}
		if (!file_exists($file)) {
			$this->setError('PHP_FILE_RENAME_NO_FILE', array(), array($file));
			return;
		}
		if (file_exists($newfile)) {
			$this->setError('PHP_FILE_RENAME_EXISTS', array(), array($file));
			return;
		}
		if (!@rename($file, $newfile)) {
			$this->setError('PHP_FILE_RENAME', array(), array($file, $newfile));
		}
	}

	/**
	 * Copy a file
	 * @param string $file file to be copy;
	 * @param string $newfile final name of the file;
	 * @access public
	 */
	function copyFile($file, $newfile) {
		$this->checkFolder($file, 'read', 'Copy File');
		if ($this->hasError()) {
			return;
		}
		$this->checkFolder($newfile, 'write', 'Copy File');
		if ($this->hasError()) {
			return;
		}
		if (!file_exists($file)) {
			$this->setError('PHP_FILE_COPY_NO_FILE', array(), array($file));
			return;
		}
		if (file_exists($newfile)) {
			$this->setError('PHP_FILE_COPY_EXISTS', array(), array($file));
			return;
		}
		if (!@copy($file, $newfile)) {
			$this->setError('PHP_FILE_COPY', array(), array($file, $newfile));
		}
	}
	
	/**
	 * verify the rights on the folder of the given file;
	 * @param string $file the absolute path of the file to be checked;
	 * @param string $mode the right to be checked: read/write:
	 * @param string $from the function that needs the check;
	 * @return nothing;
	 * @access private
	 */
	function checkFolder($file, $mode, $from) 
	{
		$folderName = $this->getFolder($file);
		$folder = new KT_folder();
		$folder->createFolder($folderName);
		switch ($mode) {
			case 'read':
				$right = $folder->checkRights($folderName, 'read');
				break;
			case 'write':
			default:
				$right = $folder->checkRights($folderName, 'write');
				break;
		}
		if ($folder->hasError()) {
			$arr = $folder->getError();
			$this->setError('PHP_FILE_FOLDER_ERROR', array($from, $arr[0]), array($from, $arr[1]));
		}
		if ($right !== true) {
			$this->setError('PHP_FILE_CHECK_FOLDER_ERROR', array($from), array($from, $mode, $folderName));
		}
		
	}
	
	/**
	 * getter. return the folder from a file path with the correct directory separator
	 * @param string $file the path to the file 
	 * return string
	 * @access public
	 */
	function getFolder($file)
	{
		if (strtolower(substr(PHP_OS, 0, 1))=='w') {
			$file = str_replace('/', '\\', $file);
		} else {
			$file = str_replace('\\', '/', $file);
		}
		$arr = split('[\\/]', $file);
		array_pop($arr);
		return implode(DIRECTORY_SEPARATOR, $arr);
	}
	
	/**
	 * Setter. set error for developper and user.
	 * @var string $errorCode error message code;
	 * @var array $arrArgsUsr  array with optional parameters for sprintf functions;
	 * @var array $arrArgsDev array with optional parameters for sprintf functions.
	 * @return nothing;
	 * @access private
	 */
	function setError($errorCode, $arrArgsUsr, $arrArgsDev)
	{
		$errorCodeDev = $errorCode;
		if ( !in_array($errorCodeDev, array('', '%s')) ) {
			$errorCodeDev .= '_D';
		}
		if ($errorCode!='') {
			$this->errorType[] = KT_getResource($errorCode, 'File', $arrArgsUsr);
		} else {
			$this->errorType = array();
		}
		if ($errorCodeDev!='') {
			$this->develErrorMessage[] = KT_getResource($errorCodeDev, 'File', $arrArgsDev);
		} else {
			$this->develErrorMessage = array();
		}
	}
	
	/**
	 * check if an error was setted.
	 * @return boolean true if error is set or false if not;
	 * @access public
	 */
	function hasError()
	{	
		if (count($this->errorType)>0 || count($this->develErrorMessage)>0) {
			return 1;	
		}	
		return 0;
	}
		
	/**
	 * Getter. 	return the errors setted.
	 * @return array  array - 0=>error for user, 1=>error for developer;
	 * @access public
	 */
	function getError()
	{
		return array(implode('<br />', $this->errorType), implode('<br />', $this->develErrorMessage));	
	}

}
?>