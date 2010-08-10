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
 * class that make link transaction possible;
 * @access public
 */
	class tNG_LinkedTrans{
			/**
			 * master transaction
			 * @var object tNG
			 * @access public
			 */
			var $masterTNG;
			/**
			 * detail transaction
			 * @var object tNG
			 * @access public
			 */
			var $detailTNG;
			/**
			 * field name that's link this transactions
			 * @var string 
			 * @access public
			 */
			var $linkField;
			
			/**
			 * Constructor. set the master/detaul transactions
			 * @param object tNG master transaction
			 * @param object tNG detail transaction
			 * @access public
			 */
			function tNG_LinkedTrans(&$masterTNG, &$detailTNG) {
				$this->masterTNG = &$masterTNG;
				$this->detailTNG = &$detailTNG;
			}
			/**
			 * setter. set the field name that's link these transactions
			 * @param string linkField value
			 * @access public
			 */
			function setLink($linkField) {
				$this->linkField = $linkField;
			}
			/**
			 * Main method of the class. Execute the code
			 * @return mix null or error object
			 * @access public
			 */
			function Execute() {
				if ($this->masterTNG->getError()) {
					return $this->onError();
				} else {
					return $this->onSuccess();
				}
			}
			/**
			 * Execute the detail transaction ad retrieve the result (error)
			 * @return mix null or error object
			 * @access private
			 */
			function onSuccess() {
				$this->detailTNG->setColumnValue($this->linkField, $this->masterTNG->getPrimaryKeyValue());
				$this->detailTNG->executeSubSets = false;
				$this->detailTNG->setStarted(true);	
				$this->detailTNG->compileColumnsValues();
				$this->detailTNG->doTransaction();	

				return $this->detailTNG->getError();
			}
			/**
			 * Executed if the master has error;
			 * execute the rollbacktransaction on the detail transaction
			 * @return nothing
			 * @access private
			 */
			function onError() {
				if ($this->detailTNG->isStarted()) {
					// if the 2nd transaction has started
					if (!$this->detailTNG->getError()) {
						// if it did not throw any error
						$this->detailTNG->rollBackTransaction($this->masterTNG->getError());
					}
				} else {
					$this->detailTNG->setColumnValue($this->linkField, $this->masterTNG->getPrimaryKeyValue());
					$this->detailTNG->executeSubSets = false;
					$this->detailTNG->setError($this->masterTNG->getError());
					$this->detailTNG->setStarted(true);
					$this->detailTNG->compileColumnsValues();
					$this->detailTNG->doTransaction();
				}
				return null;
			}

	}
?>