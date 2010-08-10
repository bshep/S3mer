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
 * This class handle fields compare validation
 * Only for PRO version
 * @access public
 */
class tNG_FieldCompare {
	/**
	 * tNG object
	 * @var object
	 * @access public
	 */
	var $tNG;
   	/**
	 * associative array, keeps error messages, columns and comparison rule
	 * @var array
	 * @access public
	 */
   	var $fields;
   	
   	/**
	 * Constructor. Sets the tNG object
	 * @param object
	 * @access public
	 */	
   	function tNG_FieldCompare(&$tNG) { 
		$this->tNG = &$tNG; 
		$this->fields = array();
	}
	/**
	 * Setter. Sets the information for one comparison
	 * @param string field 1
	 * @param string field 2
	 * @param string operation 
	 * @param string error message
	 * @access public
	 */	
	function addField($name1, $name2, $operation, $error) {
		$value1 = $this->tNG->getColumnValue($name1);
		$value2 = KT_DynamicData($name2, null);
		if ($value2 == $name2) {
			$value2 = KT_DynamicData($name2, $this->tNG);
		}
		if ($this->tNG->getColumnType($name1) == 'DATE_TYPE') {
			$value1 = KT_convertDate($value1,  $GLOBALS['KT_db_date_format'].' ' .$GLOBALS['KT_db_time_format_internal'], "yyyy-mm-dd HH:ii:ss");
			$value2 = KT_convertDate($value2,  $GLOBALS['KT_db_date_format'].' ' .$GLOBALS['KT_db_time_format_internal'], "yyyy-mm-dd HH:ii:ss");
			$value1 = strtotime($value1);
			$value2 = strtotime($value2);
		} else if ($this->tNG->getColumnType($name1) == 'NUMERIC_TYPE' || $this->tNG->getColumnType($name1) == 'DOUBLE_TYPE') {
			$value1 = (float)$value1;	
			$value2 = (float)$value2;	
		}
		$this->fields[] = array(
						'0' => array('name' => $name1, 'value' => $value1, 'type' => $this->tNG->getColumnType($name1)),
						'1' => array('name' => $name2, 'value' => $value2, 'type' => $this->tNG->getColumnType($name1)),
						'operation' => $operation,
						'error' => KT_DynamicData($error, $this->tNG)
						);	
	}
	/**
	 * Main class method. Compare the fields and return error object if any
	 * @return mixt null or tNG error object
	 * @access public
	 */	
	function Execute() {
		$ret = array();
		foreach ($this->fields as $k => $arr) {
			switch ($arr['operation']) {
				case '==':
					if ($arr['0']['value'] != $arr['1']['value']) {
						$ret[] = $arr['error'];
					}
					break;
				case '<>':
				case '!=':
					if ($arr['0']['value'] == $arr['1']['value']) {
						$ret[] = $arr['error'];
					}
					break;
				case '<':
					if ($arr['0']['value'] >= $arr['1']['value']) {
						$ret[] = $arr['error'];
					}
					break;
				case '<=':
					if ($arr['0']['value'] > $arr['1']['value']) {
						$ret[] = $arr['error'];
					}
					break;
				case '>':
					if ($arr['0']['value'] <= $arr['1']['value']) {
						$ret[] = $arr['error'];
					}
					break;
				case '>=':
					if ($arr['0']['value'] < $arr['1']['value']) {
						$ret[] = $arr['error'];
					}
					break;
				default :
					break;
			}
		}
		if (count($ret) > 0) {
			$ret = new tNG_error("%s", array(implode('<br/>', $ret)), array(''));
		} else {
			$ret = null;	
		}
		return $ret;		
	}
}
?>