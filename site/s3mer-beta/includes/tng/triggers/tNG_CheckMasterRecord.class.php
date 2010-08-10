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
 * This the CheckMasterRecord trigger; extends the tNG_CheckTableField class;
 * Checks if a master record exists; it is used inside of a trigger registered to a transaction.
 * @access public
 */
	class tNG_CheckMasterRecord extends tNG_CheckTableField {
		/**
		 * The name of the foreign key column;
		 * @var string
		 * @access public
		 */
		var $fkField = '';
		/**
			* Constructor. Sets the reference to the transaction in which the trigger is used.
			* @param object tNG &$tNG reference to transaction object
			* @access public
			*/
		function tNG_CheckMasterRecord(&$tNG) {
			parent::tNG_CheckTableField($tNG);
		}
		/**
	  * setter. Sets the name of the foreign key column, type and value;
			* @param string $field the name of the foreign key column
			* @return nothing
			* @access public
			*/
		function setFkFieldName($field) {
			$this->fkField = $field;
			$this->setFieldType($this->tNG->getColumnType($field));
			$this->setFieldValue($this->tNG->getColumnValue($field));
		}

		/**
	  * execute method of the class; check if master record exists and return null or error;
	  * @param none
		* @return mix null or error object if records don't exists
	  * @access public
	  */
		function Execute() {
			$this->errorIfExists(false);
			$err = parent::Execute();
			if ($err != NULL) {
				$useSavedData = false;
				if (in_array($this->tNG->transactionType, array('_delete', '_multipleDelete'))) {
					$useSavedData = true;
				}
				$this->errorMsg = KT_DynamicData($this->errorMsg, $this->tNG, '', $useSavedData);
				
				$err = new tNG_error('TRIGGER_MESSAGE__CHECK_MASTER_RECORD', array(), array());
				if ($this->fkField != '') {
					// set field error to $this->errorMsg
					$err->setFieldError($this->fkField, '%s', array($this->errorMsg));
					if ($this->tNG->columns[$this->fkField]['method'] != 'POST') {
						// set composed message as user error
						$err->addDetails('%s', array($this->errorMsg), array(''));
					}
				} else {
					// set composed message as user error
					$err->addDetails('%s', array($this->errorMsg), array(''));
				}
			}
			return $err;
		}
	}
?>