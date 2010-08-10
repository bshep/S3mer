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
 * many to many object helper class;
 * Only for PRO version	 
 * @access public
 */
class tNG_MtmFakeRs {
	/**
	 * connection object
	 * @var object
	 * @access public
	 */
	var $conn;
	/**
	 * columns names and values
	 * @var array
	 * @access public
	 */
	var $arrFields;
	/**
	 * columns data type
	 * @var array
	 * @access public
	 */
	var $arrTypes;
	/**
	 * input fields prefix
	 * @param string 
	 * @access public
	 */
	var $reference;
	
	/**
	 * Constructor. input fields prefix
	 * @param object connection object 
	 * @param string 
	 * @access public
	 */
    function tNG_MtmFakeRs(&$conn, $reference) {
    	$this->arrFields = array();
    	$this->arrTypes = array();
    	$this->conn = &$conn;
    	$this->reference = $reference;
    }
    /**
	 * Setter. Sets a column name, type and default value
	 * @param string 
	 * @param string 
	 * @param string 
	 * @access public
	 */
    function addField($colName, $type, $default = '') {
    	$this->arrFields[$colName] = array(KT_DynamicData($default, null));
    	$this->arrTypes[$colName] = $type;
    }
	/**
	 * Main class method. return array of values/columns
	 * @param string primary key table from slave table
	 * @param string idx for multiple transactions
	 * @param string pk name from many to many table
	 * @param object recordeset reference 
	 * @return array
	 * @access public
	 */    
    function Execute($fk, $cnt1, $pkName, &$rs) {
    	$arr = $this->arrFields;
					
			if (is_resource($rs)) {
				mysql_data_seek($rs, 0);
				$totalRows = mysql_num_rows($rs);
				$row = mysql_fetch_assoc($rs);
			} else {
				$rs->MoveFirst();
				$row = $rs->fields;
				$totalRows = $rs->RecordCount();
			}
			foreach ($row as $k=>$v) {
				if (!isset($arr[$k])) {
					$arr[$k][0] = '';
				}
			}
			$arrFields = $arr;
					
			for ($i=0; $i<$totalRows; $i++) {
				$id = $this->getColValue($row, $fk);
				reset($arr);
				foreach ($arr as $k => $v) {
					if ($k != $pkName) {
					$name = $this->reference.'_'.$k.'_'.$id;
					} else {
						$name = $this->reference.'_'.$id;
					}
					if ($cnt1 > 0) {
						$name .= '_'.$cnt1;
					}
					$arrFields[$k][$i] = $arr[$k][0];
					if (isset($_POST[$name])) {
						$arrFields[$k][$i] = $_POST[$name];
					} else if ($this->getColValue($row, $pkName) != '' || $arr[$k][0] == '') {
						$arrFields[$k][$i] = $this->getColValue($row, $k);
					}
					
					if (isset($this->arrTypes[$k]) && $this->arrTypes[$k] == 'DATE_TYPE' && ($this->getColValue($row, $k) == '' || isset($_POST[$name]))) {
						$arrFields[$k][$i] = KT_formatDate2DB($arrFields[$k][$i]);		
					}
				
				}
				// move next row;
				if (is_resource($rs)) {
					$row = mysql_fetch_assoc($rs);
				} else {
					$rs->MoveNext();
					$row = $rs;
				}
			}
		
			$obj = new KT_FakeRecordset($this->conn);
			return $obj->getFakeRecordset($arrFields);
    }
    /**
	 * Wrapper for getting values from recordset (mysql or adodb)
	 * @param reference recordset
	 * @param string column name
	 * @return string
	 * @access public
	 */
    function getColValue(&$rs, $col) {
    	if (!$rs) return;
			if (is_array($rs)) {
    		return $rs[$col];
    	} else {
    		return $rs->Fields($col);
    	}
    }
}
?>