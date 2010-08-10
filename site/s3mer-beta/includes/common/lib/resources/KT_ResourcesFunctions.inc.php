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
 * Getter the resource value;
 * @var string $resourceName the resource key;
 * @var array $dictionary  dictionary name;
 * @var array $args array with optional parameters for sprintf functions.
 * @return string;
 * @access public
 */
function KT_getResource($resourceName='default', $dictionary='default', $args = array()) {
	if (!isset($GLOBALS['interakt']['resources'])) {
		$GLOBALS['interakt']['resources'] = array();
	}
	$dictionaryFileName = KT_realpath(dirname(realpath(__FILE__)). '/../../../resources/'). '%s.res.php';
	$resourceValue = $resourceName;
	
	if (!isset($GLOBALS['interakt']['resources'][$dictionary])) {
		@include(sprintf($dictionaryFileName,$dictionary));
		if (isset($res)) {
			$GLOBALS['interakt']['resources'][$dictionary] = $res;
			unset($res);
		}
		@include(sprintf($dictionaryFileName,$dictionary."_pro"));
		if (isset($res)) {
			$GLOBALS['interakt']['resources'][$dictionary] = array_merge($GLOBALS['interakt']['resources'][$dictionary], $res);
		}

	}

	if (isset($GLOBALS['interakt']['resources'][$dictionary][$resourceName])) {
		$resourceValue = $GLOBALS['interakt']['resources'][$dictionary][$resourceName];
	} else {
		/*if (trim($resourceName) != "" && trim($resourceName) != "%s") {
			die("<br />Resource '".$resourceName."' not defined in dictionary '".$dictionary."'.<br />");
		}*/
		if (substr($resourceValue,-2) == "_D") {
			$resourceValue = substr($resourceValue,0,-2);
		}
	}

	if (count($args) > 0) {
		array_unshift($args, $resourceValue);
		$resourceValue = call_user_func_array('sprintf', $args);
	}
	return $resourceValue;
}
?>