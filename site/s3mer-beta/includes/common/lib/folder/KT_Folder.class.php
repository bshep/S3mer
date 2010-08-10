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
* manipulate folders;	
* @access public
*/
class KT_folder {
	/**
	 * error message to be displayed as User Error
	 * @var array 
	 * @access private
	 */
	var $errorType = array();
	
	/**
	 * 	error message to be displayed as Developer Error
	 * @var array
	 * @access private
	 */
	var $develErrorMessage = array();
	
	/**
	 * Constructor. doing nothing
	 * @access public
	 */
	function KT_folder() {
		
	}

	/**
	 * verify the rights on the given folder;
	 * @param string $folder the absolute path to the folder to be checked
	 * @param string $right the right to be checked: read/write:
	 * @return boolean true if the right exists on the folder or false if not;
	 * @access public
	 */
	function checkRights($folder, $right)
	{
		clearstatcache();
		$folder = $this->preparePath($folder);
		$res = false;
		switch ($right) {
			case 'read':
				if ($this->is_readable($folder)) {
					$res = true;
				}
				break;
			case 'write':
				if ($this->is_writable($folder)) {
					$res = true;
				}
				break;
		}
		return $res;
		
	}
	
	/**
	 * check if the given folder has read permissions;
	 * @param string $folder the folder name
	 * @return boolean true if it can be read or false otherwise;
	 * @access public
	 */
	function is_readable($folder)
	{
		if (@opendir($folder)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * check if the given folder has write permissions;
	 * @param string $folder the folder name
	 * @return boolean true if it can be write or false otherwise;
	 * @access public
	 */
	function is_writable($folder)
	{
		$filename = md5(uniqid("")).'.test';
		if (substr($folder, -1)!='\\' && substr($folder, -1)!='/') {
			$filename = DIRECTORY_SEPARATOR.$filename;
		}
		$fd = @fopen($folder.$filename, 'w+');
		if ($fd && file_exists($folder.$filename)) {
			@fclose($fd);
			@unlink($folder.$filename);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * read the content of the folder.
	 * @param string  $folder 		- the absolute path to the folder
	 * @param boolean $details 		- Will return in the array the size for each entry (0 size for any folder).
	 * @return array  [array] - Returns: array with the listing of the folder/files, empty if error occured;
	 * @access public
	 */
	function readFolder($folder, $details=false)
	{
		if (!$this->checkRights($folder, 'read')) {
			$this->setError('PHP_FOLDER_READ_ERR', array(), array($folder));
			return array();
		}
		
		$arrDir = array();
		$arrFil = array();
		$folder = $this->preparePath($folder);
		$folder = KT_realpath($folder);
		$dir = dir($folder);
		while (false !== ($entry = $dir->read())) {
			if ($entry != '.' && $entry != '..') {
				if (is_dir($folder . $entry)) {
					$arrDir[] = array('name'=>$entry, 'size'=>0);	
				} else {
					if (@filesize($dir->path . $entry)===false) {
						$arrFil[] = array('name'=>$entry, 'size'=>($details==true ? 0 :0 ));
					} else {
						$arrFil[] = array('name'=>$entry, 'size'=>($details==true ? filesize($dir->path . $entry) :0 ));
					}
				}	
			}	  
		} 
		$dir->close();
		return array('files'=>$arrFil, 'folders'=>$arrDir);
	}
	
	/**
	 * create recursively the folder;
	 * @param string $path  the absolute path to the folder
	 * @return nothing
	 * @access public
	 */
	function createFolder($path)
	{
		$path = $this->preparePath($path);
		$path = KT_realpath($path);
		
		$arrCreate = array();
		$arrPath = split("[\\/]", $path);
		if ($arrPath[count($arrPath)-1]=='') {
			array_pop($arrPath);
		}
		
		while (!file_exists(implode(DIRECTORY_SEPARATOR, $arrPath)) && count($arrPath)>0) {
			$arrCreate[] = array_pop($arrPath);
		}
		
		if (count($arrCreate)>0) {
			$arrCreate = array_reverse($arrCreate);
			$folder = implode(DIRECTORY_SEPARATOR, $arrPath);
			
			foreach ($arrCreate as $key => $dir) {
				$folder .= DIRECTORY_SEPARATOR .$dir;
				@mkdir($folder);
				KT_setFilePermissions($folder,true);
				if (!$this->is_writable($folder)) {
					$this->setError('PHP_FOLDER_CREATE_ERR', array(), array($folder));
					return;
				}
			}
		}
	}
	
	/**
	 * delete recursively the folder;
	 * @param string  $folder the absolute path to the folder
	 * @return nothing
	 * @access public
	 */
	function deleteFolder($folder)
	{
		if (!$this->checkRights($folder, 'write')) {
			$this->setError('PHP_FOLDER_DELETE_ERR', array(), array($folder));
			return;
		}
		$folder = $this->preparePath($folder);

		$folder = KT_realpath($folder);
		$d = dir($folder);
		while (false!==($entry = $d->read())) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			if (is_dir($d->path . $entry)) {
				$this->deleteFolder($d->path . $entry);
			} else {
				$ret = @unlink($folder . $entry);
				if ($ret === false) {
					$this->setError('PHP_FOLDER_DELETE_ERR', array(), array($folder . $entry));
				}
			}
		}
		$d->close();
		$ret = @rmdir($folder);
		if ($ret === false) {
			$this->setError('PHP_FOLDER_DELETE_ERR', array(), array($folder));
		}
	}
	
	/**
	 * delete non-recursively the folder (delete the folder only if the content is: files and/or thumbnails folder);
         * Only for PRO version	 
	 * @param string  $folder the absolute path to the folder
	 * @return nothing
	 * @access public
	 */
	function deleteFolderNR($folder)
	{
		if (!file_exists($folder)) {
			return;
		}
		if (!$this->checkRights($folder, 'write')) {
			$this->setError('PHP_FOLDER_DELETE_ERR', array(), array($folder));
			return;
		}
		$folder = $this->preparePath($folder);
		
		$arrToDel = array();
		$empty = true;
		$folder = KT_realpath($folder);
		
		$d = dir($folder);
		while (false!==($entry = $d->read())) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			if (is_dir($d->path . $entry)) {
				if ($entry != 'thumbnails') {
					$empty = false;
					$this->setError('PHP_FOLDER_DELETE_ERR', array(), array($d->path . $entry));
					return;
				} else {
					$foundThumbnailsFolder = true;	
				}
			} else {
				$arrToDel[] = $folder . $entry;
			}
		}
		$d->close();
			
		if ($empty) {
			// delete thumbnails subfolder first
			if (isset($foundThumbnailsFolder)) {
				// delete files from thumbnails
				$d = dir($folder . DIRECTORY_SEPARATOR . 'thumbnails');
				while (false!==($entry = $d->read())) {
					if ($entry == '.' || $entry == '..') {
						continue;
					}
					$ret = @unlink($folder . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR . $entry);
					if ($ret === false) {
						$this->setError('PHP_FOLDER_DELETE_ERR', array(), array($folder . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR . $entry));
						return;
					}
				}
				$d->close();
				$ret = @rmdir($folder . 'thumbnails');
				if ($ret === false) {
					$this->setError('PHP_FOLDER_DELETE_ERR', array(), array($folder . 'thumbnails'));
					return;
				}
			}
			for($i=0; $i<count($arrToDel); $i++) {
				$ret = @unlink($arrToDel[$i]);
				if ($ret === false) {
					$this->setError('PHP_FOLDER_DELETE_ERR', array(), array($arrToDel[$i]));
					return;
				}
			}
			// delete folder;
			$ret = @rmdir($folder);
			if ($ret === false) {
				$this->setError('PHP_FOLDER_DELETE_ERR', array(), array($folder));
				return;
			}
		}		
	}
	
	/**
	 * rename a folder
	 * @param string $folder the path to the folder
	 * @param string $oldName old folder name
	 * @param string $newName new folder name
	 * @return nothing
	 * @access public
	 */
	function renameFolder($folder, $oldName, $newName) {
		$folder = KT_realpath($folder);
		if (!$this->checkRights($folder, 'write')) {
			$this->setError('PHP_FOLDER_RENAME_RIGHTS', array(), array($folder));
			return;
		}
		
		if (!file_exists($folder.$oldName)) {
			$this->setError('PHP_FOLDER_RENAME_NO_FILE', array(), array($folder.$oldName));
			return;
		}
		if (file_exists($folder.$newName)) {
			$this->setError('PHP_FOLDER_RENAME_EXISTS', array(), array($folder.$newName));
			return;
		}
		if (!@rename($folder.$oldName, $folder.$newName)) {
			$this->setError('PHP_FOLDER_RENAME', array(), array($folder.$oldName, $folder.$newName));
		}
	}

	/**
	 * copy a folder
	 * @param string $folder the path to the folder
	 * @param string $parentFolder the parent of the copied folder
	 * @return nothing
	 * @access public
	 */
	function copyFolder($folder, $parentFolder) {
		$folder = $this->preparePath($folder);
		$folder = KT_realpath($folder);
		$parentFolder = $this->preparePath($parentFolder);
		$parentFolder = KT_realpath($parentFolder);
		if (!$this->checkRights($folder, 'read')) {
			$this->setError('PHP_FOLDER_COPY_RIGHTS', array(), array($folder));
			return;
		}
		if (!$this->checkRights($parentFolder, 'write')) {
			$this->setError('PHP_FOLDER_COPY_RIGHTS', array(), array($parentFolder));
			return;
		}
		
		$destFolder = $parentFolder . basename($folder);
		$this->createFolder($destFolder);
		if ($this->hasError()) {
			$err = $this->getError();
			$this->setError('PHP_FOLDER_COPY', array(), array($destFolder, $err[1]));
			return;
		}
		$destFolder = KT_realpath($destFolder);
		
		$d = dir($folder);
		while (false!==($entry = $d->read())) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			if (is_dir($folder . $entry)) {
				$this->copyFolder($folder . $entry, $destFolder);
			} else {
				@copy($folder . $entry, $destFolder . $entry);
				KT_setFilePermissions($destFolder . $entry);
			}
		}
		$d->close();
	}

	/**
	 * move a folder
	 * @param string $folder the path to the folder
	 * @param string $parentFolder the new parent of the folder
	 * @return nothing
	 * @access public
	 */
	function moveFolder($folder, $parentFolder) {
		$this->copyFolder($folder, $parentFolder);
		if ($this->hasError()) {
			$err = $this->getError();
			$this->setError('PHP_FOLDER_MOVE', array(), array($parentFolder, $err[1]));
			return;
		}
		$this->deleteFolder($folder);
		if ($this->hasError()) {
			$err = $this->getError();
			$this->setError('PHP_FOLDER_MOVE', array(), array($parentFolder, $err[1]));
		}
	}

	/**
	 * replace the '/' with '\' for windows and '\' with '/' for linux;
	 * @param string $path  the path 
	 * @return string the translated path;
	 * @access public
	 */
	function preparePath($path)
	{
		$path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
		return $path;
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
			$this->errorType[] = KT_getResource($errorCode, 'Folder', $arrArgsUsr);
		} else {
			$this->errorType = array();
		}
		if ($errorCodeDev!='') {
			$this->develErrorMessage[] = KT_getResource($errorCodeDev, 'Folder', $arrArgsDev);
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