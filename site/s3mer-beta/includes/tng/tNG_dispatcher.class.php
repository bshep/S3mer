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
 * The dispatcher class, that handles all the transactions in a page.
 * @access public
 */
class tNG_dispatcher {
	/**
	 * The associated transactions array
	 * @var array
	 * @access private
	 */
	var $tNGs = array();

	/**
	 * The number of associated transactions
	 * @var integer
	 * @access private
	 */
	var $n = 0;

	var $UnivalProps = array();
	/**
	 * The relative path of the current tNG_dispathcer
	 * @var string
	 * @access public
	 */
	var $relPath = "";
	/**
	 * Internal counter for the number of validations;
	 * @var integer
	 * @access private
	 */
	var $UnivalCount = 0;
	
	/**
	 * Constructor. Set the value of relative path
	 * @param string $relPath The relative path of the current tNG_dispathcer
	 * @access public
	 */
	function tNG_dispatcher($relPath) {
		$this->relPath = $relPath;
	}
	/**
	 * Adds a transaction to the current dispatcher
	 * @param object tNG &$tNG - the transaction to add
	 * @access public
	 */
	function addTransaction(&$tNG) {
		$tNG->setDispatcher($this);
		$this->tNGs[$this->n++] = &$tNG;
	}

	/**
	 * Gets the correct recordset from the transactions
	 * @param string $tableName  - the table to search
	 * @return object RedourceID
	 *         the Recordset Resource ID
	 * @access public
	 */
	function getRecordset($tableName) {

			$method_where = -1;
			$method = -1;
			/*
				-1 = unknown
				 1 = default values
				 2 = from recordset
				 3 = submitted values
			*/
			for ($i=0;$i<$this->n;$i++) {
				if ($this->tNGs[$i]->getTable() == $tableName) {
					switch($this->tNGs[$i]->getTransactionType()) {
						case '_login':
						case '_custom':
						case '_insert':
						case '_multipleInsert':
							if ($method < 1) {
								$method = 1;
								$method_where = $i;
							}
							break;
						case '_update':
						case '_multipleUpdate':
							$pkv = $this->tNGs[$i]->getPrimaryKeyValue();
							if (isset($pkv)) {
								if ($method < 2) {
									$method = 2;
									$method_where = $i;
								}
							}
							break;
					}
					if ($this->tNGs[$i]->isStarted()) {
						if ($this->tNGs[$i]->exportsRecordset()) {
							$tmpArrErr = $this->tNGs[$i]->getErrorMsg();
							if ($tmpArrErr[1] != '' || $tmpArrErr[2] != '') {
								$method = 3;
								$method_where = $i;
							}
						}
					}
				}
			}
			if ($method_where == -1) {
				$ret = $this->tNGs[0]->getRecordset(); 
				//die('tNG_dispatcher.getRecordset:<br/>The transaction that handles the curent recordset is not properly configured.<br>Check the settings from the user interface that generated the code. Possible reasons for this failure: you have specified URL parameter for the Primary key column in the interface and you are running the page without any parameter or the incorect parameter.');
			} else {
				$ret = $this->tNGs[$method_where]->getRecordset();
		}

		return $ret;
	}

	/**
	 * Executes the linked transactions, one by one
	 * @access public
	 */
	function executeTransactions() {

		for ($i=0;$i<$this->n;$i++) {
			$this->tNGs[$i]->executeTransaction();
		}
	}
	
	/**
	 * construct the JS to be used in page for client side validation
	 * @return string 
	 * @access public
	 */
	function displayValidationRules() {
		$outRules = '';
		$outRules .= '<script src="'.$this->relPath.'includes/tng/scripts/FormValidation.js" type="text/javascript"></script>' . "\r\n";
		$outRules .= '<script src="'.$this->relPath.'includes/tng/scripts/FormValidation.js.php" type="text/javascript"></script>' . "\r\n";
		if (isset($this->UnivalProps) && is_array($this->UnivalProps) && count($this->UnivalProps) > 0) {
			$outRules .= '<script type="text/javascript">';
			$univalPropKeys = array_keys($this->UnivalProps);
			$count = count($univalPropKeys);
			$sw = false;
			for ($i = 0; $i < $count; $i++) {
				$fieldName = $univalPropKeys[$i];
				$field = $this->UnivalProps[$fieldName];
				// get the form field name
				$formFieldName = $fieldName;
				$skip = false;
				for ($j=0;$j<$this->n;$j++) {
					if (isset($this->tNGs[$j]->columns[$fieldName])) {
						$formFieldName = $this->tNGs[$j]->columns[$fieldName]['reference'];
						if ($this->tNGs[$j]->columns[$fieldName]['method'] == 'CURRVAL') {
								$skip = true; 
						}						
					}
				}
				if ($skip) {
					continue;
				}
				
				if ($formFieldName !== '') {
					$outRules .= "\r\n  KT_FVO['" . KT_escapeJS($formFieldName) . "'] = {";
					$outRules .= "required: " . $field['required'] . ", ";
					$outRules .= "type: '" . $field['type'] . "', ";
					if ($field['format'] != '' ) {
						$outRules .= "format: '" . $field['format'] . "', ";
					}
					if ($field['additional_params'] != '') {
						$outRules .= "additional_params: '" . KT_escapeJS($field['additional_params']) . "', ";
					}
					if ($field['min'] != '' ) {
						$outRules .= "min: '" . $field['min'] . "', ";
					}
					if ($field['max'] != '' ) {
						$outRules .= "max: '" . $field['max'] . "', ";
					}
					if ($this->UnivalErrors[$fieldName] != '' ) {
						$outRules .= "errorMessage: '" . KT_escapeJS($this->UnivalErrors[$fieldName]) . "', ";
					}
					$outRules = substr($outRules, 0, strlen($outRules) - 2);
					$outRules .= "}";
				}
			}
			$outRules .= "\r\n\r\n";
			$outRules .= "  KT_FVO_properties['noTriggers'] += " . $this->UnivalCount . ";\r\n";
			$outRules .= "  KT_FVO_properties['noTransactions'] += " . $this->n . ";\r\n";
			$outRules .= '</script>';
		}
		return $outRules;
	}
	
	/**
	 * Gets the hint for the fieldname
	 * @param string $fieldName
	 * @return string hint
	 * @access public
	 */
	function displayFieldHint($fieldName) {
		if (isset($this->fieldHints[$fieldName]) && $this->fieldHints[$fieldName] != '()') {
			return '<span class="KT_field_hint">'.$this->fieldHints[$fieldName].'</span>'."\r\n";
		}
	}
	
	/**
	 * return the error for the field
	 * @param string $tableName
	 * @param string $fieldName
	 * @cnt integer $cnt
	 * @return string error for the field
	 * @access public
	 */
	function displayFieldError($tableName, $fieldName, $cnt = 1) {
		$ret = '';
		for ($i=0; $i<$this->n; $i++) {
			if ($this->tNGs[$i]->getTable() == $tableName) {
				if ($this->tNGs[$i]->isStarted()) {
					$tmp = $this->tNGs[$i]->getError();
					if ($tmp) {
						$ret = $this->tNGs[$i]->getFieldError($fieldName, $cnt);
						break;
					}
				}
			}
		}
		if ($ret != '') {
			return '<br class="clearfixplain" /><div class="KT_field_error">'.$ret.'</div>'."\r\n";
		} else {
			return '';
		}
	}
	
	/**
	 * prepair the validation
	 * @param object $unival
	 * @return string transaction type
	 * @access public
	 */
	function prepareValidation(&$uniVal) {
		$this->UnivalCount++;
		if (!is_array($uniVal->columns) || count($uniVal->columns) == 0) {
			return;
		}
		foreach ($uniVal->columns as $columnName => $column) {
			// Set unival JS div errors
			// here we set the least restrictive required prop
			$required = ($column['required']===true)?'true':'false';
			if (array_key_exists($columnName, $this->UnivalProps)) {
				if ($this->UnivalProps[$columnName]['required'] != $required) {
					$required = 'false';
				}
			}
			$this->UnivalProps[$columnName]['required'] = $required;
			$this->UnivalProps[$columnName]['type'] = $column['type'];
			$this->UnivalProps[$columnName]['format'] = $column['format'];
			$this->UnivalProps[$columnName]['additional_params'] = $column['additional_params'];
			$this->UnivalProps[$columnName]['min'] = $column['min_cs'];
			$this->UnivalProps[$columnName]['max'] = $column['max_cs'];
			@$this->UnivalProps[$columnName]['count']++;
			$this->UnivalErrors[$columnName] = $column['message'];
			// Set field Hints
			if ($column['type'] == 'regexp') {
				$this->fieldHints[$columnName]  = '';
			} elseif ($column['type'] == 'mask') {
				$this->fieldHints[$columnName]  = '(' . $column['additional_params'] . ')';
			} elseif ($column['type'] == 'date' && $column['format'] != '') {
				$this->fieldHints[$columnName]  = '(' . $uniVal->genericValidationMessages['date_' . $column['format']] . ' ' . $column['date_screen_format'] . ')';
			} elseif ($column['format'] != '') {
				$this->fieldHints[$columnName]  = '(' . $uniVal->genericValidationMessages[$column['type'] . '_' . $column['format']] . ')';
			}
		}
	}
	
	/**
	 * return the error message
	 * @return string transaction type
	 * @access public
	 */
	function getErrorMsg() {
		$ret_warning = '';
		$ret_user = '';
		$ret_devel = '';

		$errorWasFound = false;
		for ($i=0;$i<$this->n;$i++) {
			list($ret_warning, $ret_user, $ret_devel) = $this->tNGs[$i]->getErrorMsg();
			if ($ret_warning!='' || $ret_user!='' || $ret_devel != '') {
				$errorWasFound = true;
				break;
			}
		}
		$uniq = uniqid("");
		$rethead = '';
		//$rethead = '<link href="' . $this->relPath . 'includes/tng/styles/default.css" rel="stylesheet" type="text/css" />' . "\r\n";
		//$rethead .= '<script src="' . $this->relPath . 'includes/common/js/base.js" type="text/javascript"></script>' . "\r\n";
		//$rethead .= '<script src="' . $this->relPath . 'includes/common/js/utility.js" type="text/javascript"></script>' . "\r\n";
		$ret = '';
		$txtContent = "";
		$txtContent .= "Client IP:\r\n  " . $_SERVER['REMOTE_ADDR'];
		$txtContent .= "\r\n\r\nHost:\r\n  " . $_SERVER['HTTP_HOST'];
		$txtContent .= "\r\n\r\nRequested URI:\r\n  " . KT_getFullUri();
		$txtContent .= "\r\n\r\nDate:\r\n  " . date("Y-m-d H:i:s");
		if ($errorWasFound) {
			if ($ret_warning!='') {
				$ret .= '<div id="KT_tngwarning">'. $ret_warning . "</div>\r\n";
				$txtContent .= "\r\n\r\nWarning:\r\n  " . $ret_warning;
			}
			if ($ret_user!='') {
				$ret .= '<div id="KT_tngerror"><label>'.KT_getResource('ERROR_LABEL','tNG').'</label><div>' . $ret_user . '</div></div>' . "\r\n";
				$txtContent .= "\r\n\r\n".KT_getResource('ERROR_LABEL','tNG')."\r\n  " . $ret_user;
			}
			
			if ('DEVELOPMENT' == $GLOBALS['tNG_debug_mode']) {
				$js_err = KT_escapeJS($ret_user);
				$js_devNotes = KT_escapeJS($ret_devel);
				$js_os = PHP_OS;
				$js_webserver = @$_SERVER['SERVER_SOFTWARE'];
				$js_servermodel = (!file_exists($this->relPath . 'adodb/')? 'PHP MySQL ': 'PHP ADODB ') . phpversion();				
				$js_installation = KT_escapeJS(php_sapi_name());
				$js_extensions = KT_escapeJS(var_export(get_loaded_extensions(),true));
				$ret = $rethead . $ret;
				if ($ret_devel != '') {
					$ret .= '<div id="KT_tngdeverror"><label>Developer Details:</label><div>'.$ret_devel.'</div></div>';
				}
				$tmp = tNG_log::getResult('html', '_'.$uniq);
				$ret .= '<div id="KT_tngtrace"><label>tNG Execution Trace - <a href="#" onclick="document.getElementById(\'KT_tngtrace_details_'. $uniq .'\').style.display=(document.getElementById(\'KT_tngtrace_details_'. $uniq .'\').style.display!=\'block\'?\'block\':\'none\'); return false;">VIEW</a></label>' . $tmp . '</div>';
			}
			if ("" != $GLOBALS['tNG_debug_log_type'] && $ret_devel != '') {
				$txtContent .= "\r\n\r\nDeveloper Details:\r\n  " . $ret_devel;
				$tmp = tNG_log::getResult('text', '_'.$uniq);
				$txtContent .= "\r\n\r\ntNG Execution Trace:\r\n" . $tmp;
				
				if ($GLOBALS['tNG_debug_log_type'] == 'logfile') {
					// log file
					$logFile = dirname(realpath(__FILE__)) . "/logs/" . date("Ym") . ".log";
					$f = @fopen($logFile, "a");
					if ($f) {
						if (flock($f, LOCK_EX)) { // do an exclusive lock
								fwrite($f, "=== BEGIN MESSAGE ===\r\n");
								fwrite($f, $txtContent);
								fwrite($f, "=== END MESSAGE ===\r\n");
								flock($f, LOCK_UN); // release the lock
						}
						fclose($f);
					}
				} else {
					$email = new KT_Email();
					//$email->setPriority("medium");
					$email->sendEmail($GLOBALS['tNG_email_host'], $GLOBALS['tNG_email_port'], $GLOBALS['tNG_email_user'], $GLOBALS['tNG_email_password'], $GLOBALS['tNG_debug_email_from'], $GLOBALS['tNG_debug_email_to'], "", "", $GLOBALS['tNG_debug_email_subject'], "ISO-8859-1", $txtContent, "");
				}
			}
		}
		return $ret;
	}
	
	/**
	 * Save in session transaction errors
         * Only for PRO version	 
	 * @return string saved error message from session
	 * @access public
	 */
	function getSavedErrorMsg() {
		$ret = '';
		if (isset($_SESSION['tng_errors']) && $_SESSION['tng_errors'] !='') {
			$ret = $_SESSION['tng_errors'];
		}
		return $ret;
	}
	
	/**
	 * Save in session transaction errors
         * Only for PRO version	 
	 * @param string error message;
	 * @access public
	 */
	function saveError($error) {
		if (!isset($_SESSION['tng_errors'])){
			$_SESSION['tng_errors'] = '';
		}
		$_SESSION['tng_errors'] .= $error;
	}
	/**
	 * Returns the messages for the Login Page
	 * @access public
	 */
	function getLoginMsg() {
		$show = false;
		for ($i=0;$i<$this->n;$i++) {
			if ($this->tNGs[$i]->getTransactionType() == '_login' && !$this->tNGs[$i]->started) {
				$show = true;
				break;
			}
		}
		
		if ($show) {
			$info_resources = array('REG_ACTIVATE', 'REG_EMAIL', 'REG', 'ACTIVATED', 'FORGOT', 'DENIED', 'MAXTRIES', 'ACCOUNT_EXPIRE');
			$info_key = KT_getRealValue("GET", "info");
			if ($info_key != "") {
				if (in_array($info_key, $info_resources)) {
					$ret = '<div id="KT_tngdeverror">';
					$ret .= '<label>'.KT_getResource('LOGIN_MESSAGE_LABEL','tNG').'</label>';
					$ret .= '<div>' . KT_getResource('LOGIN_MESSAGE__'.$info_key, 'tNG') . '</div>';
					$ret .= '</div>';
					return $ret;
				}
			}
		}	
		return '';
	}
}
?>