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
 * This class delete a record from the given table, field;
 * @access public
 */
class tNG_DeleteDetailRec {
	/**
	* The reference to transaction object
	* @var object tNG transaction object
	* @access public
	*/
	var $tNG;
	/**
	* name of the table
	* @var string
	* @access public
	*/
	var $table;
	/**
	* Name of the field
	* @var string
	* @access public
	*/
	var $field;
	/**
	 * name of the field from database wich helds the file name
         * Only for PRO version
	 * @var array 
	 * @access public
	 */
	var $dbFieldName = '';
	/**
	 * folder name
         * Only for PRO version
	 * @var array
	 * @access public
	 */
	var $fileFolder = array();
	/**
	 * the rename rule for file
         * Only for PRO version
	 * @var array
	 * @access public
	 */
	var $fileRenameRule = array();
	/**
	 * folder name
         * Only for PRO version
	 * @var array
	 * @access public
	 */
	var $folder = array();
	/**
	 * the rename rule for folder
         * Only for PRO version
	 * @var array
	 * @access public
	 */
	var $folderRenameRule = array();
	/**
	 * counter for file
         * Only for PRO version
	 * @var integer
	 * @access public
	 */
	var $indexFile = 0;
	/**
	 * counter for folder
         * Only for PRO version
	 * @var integer
	 * @access public
	 */
	var $indexFolder = 0;
	
	/**
	 * Constructor. Sets the transaction and some defaults values for table/field.
	 * @param object tNG  The reference to transaction object
	 * @access public
	 */
	function tNG_DeleteDetailRec(&$tNG) {
		$this->tNG = &$tNG;
		$this->table = 'mytable';
		$this->field = 'myfield';
	}
	/**
	 * setter. sets the name of the table
	 * @param string 
	 * @access public
	 */
	function setTable($table) {
		$this->table = $table;
	}
	/**
	 * setter. sets the name of the field
	 * @param string
	 * @access public
	 */
	function setFieldName($field) {
		$this->field = $field;
	}
	/**
	 * setter. set the rename rule, folder name for file
         * Only for PRO version
	 * @var object tNG
	 * @access public
	 */
	function addFile($renameRule, $folder) {
		$this->fileRenameRule[$this->indexFile] = $renameRule;
		$this->fileFolder[$this->indexFile] = KT_realpath($folder);
		$this->indexFile++;
	}
	/**
	 * setter. set the rename rule, folder name for folder
         * Only for PRO version
	 * @var object tNG
	 * @access public
	 */
	function addFolder($renameRule, $folder) {
		$this->folderRenameRule[$this->indexFolder] = $renameRule;
		$this->folder[$this->indexFolder] = KT_realpath($folder);
		$this->indexFolder++;
	}
	/**
	 * delete the tumbnails if exists
         * Only for PRO version
	 * @var string folder name
	 * @var string name of the file
	 * @return nothing
	 * @access public
	 */
	function deleteThumbnails($folder, $oldName) {
		tNG_deleteThumbnails($folder, $oldName, '');
	}
	/**
	 * contruct the SQL and execute it. it is using as value for the field the primarey key value from the transaction;
	 * return mix null or error object;
	 * @access public
	 */
	function Execute() {
		$pk_value = $this->tNG->getPrimaryKeyValue();
		$pk_type = $this->tNG->getColumnType($this->tNG->getPrimaryKey());
		$pk_value = KT_escapeForSql($pk_value, $pk_type);
		
		if (count($this->fileRenameRule) > 0 || count($this->folderRenameRule) > 0 ) {
			$sql = 'SELECT * FROM '. $this->table .' WHERE '. KT_escapeFieldName($this->field) . " = " . $pk_value;
			$rs = $this->tNG->connection->Execute($sql);
			if ($rs === false) {
				return new tNG_error('DEL_DR_SQL_ERROR', array(), array($this->tNG->connection->ErrorMsg(), $sql));
			}
			if ($rs->RecordCount() == 0) {
				return null;
			}
		}		
		
		// prepare to delete files
		if (count($this->fileRenameRule) > 0 ) {
			$fullFileName = array();
			$fullFileNameFolder = array();
			for ($i=0; $i<count($this->fileRenameRule); $i++) {	
				while (!$rs->EOF) {
					$arr = array();
					foreach ($rs->fields as $col => $value) {
						$arr[$col] = $value;
					}
					$folder = $this->fileFolder[$i];
					$fileName = KT_DynamicData($this->fileRenameRule[$i], $this->tNG, '', false, $arr);
					// security
                                        if (substr(KT_realpath($folder . $fileName), 0, strlen($folder)) != $folder) {
                                                $baseFileName = dirname(KT_realpath($folder . $fileName, false));
						$ret = new tNG_error("FOLDER_DEL_SECURITY_ERROR", array(), array($baseFileName, $folder));
						return $ret;
					}
					$fullFileName[] = $fileName;
					$fullFileNameFolder[] = $folder;
					$rs->MoveNext();
				}
				$rs->MoveFirst();
			}
		}
		
		// prepare to delete related folders
		if (count($this->folderRenameRule) > 0 ) {
			$relatedFolder = array();
			for ($i=0; $i<count($this->folderRenameRule); $i++) {
				while (!$rs->EOF) {
					$arr = array();
					foreach ($rs->fields as $col => $value) {
						$arr[$col] = $value;
					}	
					$folder = $this->folder[$i];
					$f = KT_DynamicData($this->folderRenameRule[$i], $this->tNG, '', false, $arr);
					// security
                                        if (substr(KT_realpath($folder . $f), 0, strlen($folder)) != $folder) {
                                                $baseFileName = dirname(KT_realpath($folder . $f, false));
						$ret = new tNG_error("FOLDER_DEL_SECURITY_ERROR", array(), array($baseFileName, $folder));
						return $ret;
					}
					$relatedFolder[] = $folder . $f;
					$rs->MoveNext();
				}
				$rs->MoveFirst();
			}
		}
	
		// delete reocords
		$sql = "DELETE FROM " . $this->table . " WHERE " . KT_escapeFieldName($this->field) . " = " . $pk_value;
		$ret = $this->tNG->connection->Execute($sql);
		if ($ret === false) {
			return new tNG_error('DEL_DR_SQL_ERROR', array(), array($this->tNG->connection->ErrorMsg(), $sql));
		}
		// delete files
		if (count($this->fileRenameRule) > 0 ) {
			for ($i=0; $i<count($fullFileName); $i++) {			
				if (file_exists($fullFileNameFolder[$i] . $fullFileName[$i])) {
					$delRet = @unlink($fullFileNameFolder[$i] . $fullFileName[$i]);
					$path_info = KT_pathinfo($fullFileNameFolder[$i] . $fullFileName[$i]);
					$this->deleteThumbnails($path_info['dirname'] . '/thumbnails/', $path_info['basename']);					
				}
			}
		}
		// delete related folder
		if (count($this->folderRenameRule) > 0 ) {
			for ($i=0; $i<count($relatedFolder); $i++) {
				$folder = new KT_Folder();
				// delete thumbnails
				$folder->deleteFolderNR($relatedFolder[$i]);				
			}
		}
		return null;
	}

}
?>