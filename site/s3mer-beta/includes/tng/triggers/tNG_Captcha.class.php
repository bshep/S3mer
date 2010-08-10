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
* Check the enter value is equal to the value saved in session.
*/
class tNG_Captcha {
	/**
	* @param object tNG reference;
	*/
	var $tNG;
	/**
	* @param string value of the captcha form field ;
	*/
	var $fieldValue;
	/**
	* @param string name to use to store in session the captcha string;
	*/
	var $name;
	/**
	* @param string error message to be used;
	*/
	var $errorMessage;
	
	/**
	* Constructor. 
	* @param string name to use to store in session the captcha string;
	* @param object tNG reference;
	* @return nothing
	*/
	function tNG_Captcha($name, &$tNG) {
		$this->fieldValue = '';
		$this->name = 'KT_captcha_'.$name; 
		$this->tNG = &$tNG; 
	}
	
	/**
	* get the string stored in the session if setted
	* @return string
	*/
	function getTextCaptcha() {
		if (!isset($_SESSION[$this->name])) {
			$_SESSION[$this->name] = substr(md5(uniqid(rand(),true)), 0, rand(5,8));
		}
		return $_SESSION[$this->name];
	}
	
	/**
	* unset the string stored in session
	* @return nothing
	*/
	function resetCaptcha() {
		unset($_SESSION[$this->name]);
	}
	
	/**
	* set the form field value
	* @param string method of the form (post / get)
	* @param string field name
	* @return nothing
	*/
	function setFormField($method, $reference) {
		$this->fieldValue = KT_getRealValue($method, $reference);
	}
	
	/**
	* set the the errror message
	* @param string error
	* @return nothing
	*/
	function setErrorMsg($errorMessage) {
		$this->errorMessage = KT_DynamicData($errorMessage, $this->tNG);
	}
	
	/**
	* main class method; verify that the captcha sumited is identical to the one store in the session;
	* @param none
	* @return mixt boolean or error object
	*/
	function Execute() {
		if ($this->fieldValue == $this->getTextCaptcha()) {
			$ret = true;
		} else {
			$ret = new tNG_error("%s", array($this->errorMessage), array(''));
		}
		$this->resetCaptcha();
		return $ret;
	}
		
}
?>