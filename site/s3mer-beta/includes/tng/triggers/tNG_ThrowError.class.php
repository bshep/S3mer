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
 * tNG_ThrowError class;
 * @access public
 */
	class tNG_ThrowError {
		/**
		 * transaction object
		 * @var object tNG
		 * @access public
		 */
		var $tNG;
		/**
		 * error message to be used
		 * @var string
		 * @access public
		 */
		var $errorMsg;
		/**
		 * error message to be used on the field in the page
		 * @var string
		 * @access public
		 */
		var $fieldErrorMsg;
		/**
		 * field name
		 * @var string
		 * @access public
		 */
		var $field;
		/**
		 * Constructor. set the transaction and put default values for other vars
		 * @param object tNG detail transaction
		 * @access public
		 */
		function tNG_ThrowError(&$tNG) {
			$this->tNG = &$tNG;
			$this->errorMsg = '';
			$this->field = '';
			$this->fieldErrorMsg = '';
		}
		/**
		 * setter. set the error message
		 * @param string
		 * @access public
		 */
		function setErrorMsg($errorMsg) {
			$this->errorMsg = $errorMsg;
		}
		/**
		 * setter. set the field error message
		 * @param string
		 * @access public
		 */
		function setFieldErrorMsg($fieldErrorMsg) {
			$this->fieldErrorMsg = $fieldErrorMsg;
		}
		/**
		 * setter. set the field name
		 * @param string
		 * @access public
		 */
		function setField($field) {
			$this->field = $field;
		}
		/**
		 * Main method of the class. Execute the code
		 * Return the error object with the error message in it and set the field error message on the field from transaction if the field was set in the class;
		 * @return object error
		 * @access public
		 */
		function Execute() {
			$useSavedData = false;
			if (in_array($this->tNG->transactionType, array('_delete', '_multipleDelete'))) {
				$useSavedData = true;
			}

			$this->errorMsg = KT_DynamicData($this->errorMsg, $this->tNG, '', $useSavedData);
			$this->fieldErrorMsg = KT_DynamicData($this->fieldErrorMsg, $this->tNG, '', $useSavedData);

			$err = new tNG_error('%s', array($this->errorMsg), array(''));
			if (isset($this->tNG->columns[$this->field])) {
				// set field error to $this->errorMsg
				$err->setFieldError($this->field, '%s', array($this->fieldErrorMsg));
				if ($this->tNG->columns[$this->field]['method'] != 'POST') {
					// set composed message as user error
					$err->addDetails('%s', array($this->fieldErrorMsg), array(''));
				}
			} else {
				// set composed message as user error
				$err->addDetails('%s', array($this->fieldErrorMsg), array(''));
			}
			return $err;
		}
	}
?>