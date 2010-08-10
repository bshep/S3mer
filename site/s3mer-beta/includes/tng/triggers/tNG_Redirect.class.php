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
 * class that handle the redirect;
 * @access public
 */
class tNG_Redirect {
	/**
	 * transaction object
	 * @var object tNG
	 * @access public
	 */
	var $tNG;
	/**
	 * url to use for redirect
	 * @var string
	 * @access public
	 */
	var $URL;
	/**
	 * if the GET params to be kept or not
	 * @var boolean
	 * @access public
	 */
	var $keepUrlParams;
	/**
	 * Constructor. set the transaction
	 * @param object tNG transaction
	 * @access public
	 */
	function tNG_Redirect($tNG = null) {
		$this->tNG = $tNG;
	}
	/**
	 * setter. set the URL to be used for redirect
	 * @param string
	 * @access public
	 */
	function setURL($URL) {
		$this->URL = $URL;
		if (strpos($URL,"includes/nxt/back.php") !== false) {
			$this->URL = KT_makeIncludedURL($this->URL);
		}
	}
	/**
	 * setter. if the GET params to be kept or not
	 * @param boolean
	 * @access public
	 */
	function setKeepURLParams($keepUrlParams) {
		$this->keepUrlParams = $keepUrlParams;
	}
	
	/**
	 * Main method of the class. make the redirect
	 * @return nothing
	 * @access public
	 */
	function Execute() {
		if (!isset($this->tNG)) {
			$page = KT_DynamicData($this->URL,null,'rawurlencode');
		} else {
			$useSavedData = false;
			if ($this->tNG->getTransactionType()=='_delete' || $this->tNG->getTransactionType()=='_multipleDelete') {
				$useSavedData = true;
			}
			$page = KT_DynamicData($this->URL,$this->tNG,'rawurlencode',$useSavedData);
		}
		if ($this->keepUrlParams) {
			foreach($_GET as $param => $value) {
				$page = KT_addReplaceParam($page, $param, $value);
			}
		}
		KT_redir($page);
	}
}
?>