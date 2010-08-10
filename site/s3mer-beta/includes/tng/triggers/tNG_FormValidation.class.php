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
Class definition
NAME:
	formValidator
DESCRIPTION:
	This Class is a simple validator class that incapsulate the UniVal functionality
**/

//TODO : better error triggering
class tNG_FormValidation {
	var $tNG = null;
	var $columns = array();
	var $validationRules = array();
	var $genericValidationMessages = array();
	var $mustValidate = false;

	// constructor
	function tNG_FormValidation() {
		$this->tNG = null;
		$this->validationRules = array();
		$this->loadValidatioRules($this->validationRules);
		$this->loadGenericValidationMessages($this->genericValidationMessages);

		KT_getInternalTimeFormat();
	}

	function setTransaction(&$tNG) {
		$this->tNG = &$tNG;
		$this->mustValidate = true;
	}

	function addField($fieldName, $required, $validationType, $format, $min, $max, $errorMessage) {

		$validationType = strtolower($validationType);

		$field = array();
		$field['name'] = $fieldName;
		$field['required'] = $required;
		$field['type'] = $validationType;
		if ($validationType == 'mask') {
			$field['format'] = '';
			$field['additional_params'] = $format;
		} elseif ($validationType == 'regexp') {
			$field['format'] = '';
			if (substr($format, 0, 1) != '/') {
				$format = '/' . $format . '/im';
			}
			$field['additional_params'] = $format;
		} else {
			$field['format'] = strtolower($format);
			if (isset ($this->validationRules[$validationType][$format]['regexp'])) {
				$additionals = $this->validationRules[$validationType][$format]['regexp'];
			} else {
				$additionals = '';
			}
			$field['additional_params'] = $additionals;
		}
		switch ($field['format']) {
			case 'date' :
				$field['additional_params'] = $GLOBALS['KT_screen_date_format'];
				$field['date_screen_format'] = $GLOBALS['KT_screen_date_format'];
				break;
			case 'time' :
				$field['additional_params'] = $GLOBALS['KT_screen_time_format_internal'];
				$field['date_screen_format'] = $GLOBALS['KT_screen_time_format'];
				break;
			case 'datetime' :
				$field['additional_params'] = $GLOBALS['KT_screen_date_format'] . ' ' . $GLOBALS['KT_screen_time_format_internal'];
				$field['date_screen_format'] = $GLOBALS['KT_screen_date_format'] . ' ' . $GLOBALS['KT_screen_time_format'];
				break;
		}
		$min1 = $min;
		$max1 = $max;
		
		$min1_placeholders = KT_getReplacementsFromMessage($min1);
		if (count($min1_placeholders) > 0) {
			$min1 = '';
		}
		$max1_placeholders = KT_getReplacementsFromMessage($max1);
		if (count($max1_placeholders) > 0) {
			$max1 = '';
		}
		
		// min_cs and max_cs are used for client side validation
		$field['min_cs'] = $min1;
		$field['max_cs'] = $max1;
		
		$min = KT_DynamicData($min, null);
		$max = KT_DynamicData($max, null);
		$field['min'] = $min;
		$field['max'] = $max;
		$field['message'] = $errorMessage;
		$field['additional_params'] = $field['additional_params'];
		$this->columns[$fieldName] = $field;
	}

	function mask2regexp($txt) {
		$txt = preg_replace('/([-\/\[\]()\*\+])/', "\\1", $txt);
		$txt = preg_quote($txt);
		$txt = str_replace('\?', '?', $txt);
		$txt = str_replace('?', '.', $txt);
		$txt = str_replace('9', '\d', $txt);
		$txt = str_replace('X', '\w', $txt);
		$txt = str_replace('A', '[A-Za-z]', $txt);
		$txt = str_replace('/', '\/', $txt);
		$txt = '/^' . $txt . '$/';
		return $txt;
	}

	function loadValidatioRules(&$arr) {
		$arr['text'] = array();
		$arr['text'][''] = array();
		$arr['text']['email']['regexp'] = '/^\w+[\w\+\.\-]*@\w+(?:[\.\-]\w+)*\.\w+$/i';
		$arr['text']['cc_generic']['regexp'] = '/^[3-6]{1}[0-9]{12,15}$/';
		$arr['text']['cc_generic']['callback'] = 'validate_cc';
		$arr['text']['cc_visa']['regexp'] = '/^4[0-9]{12,15}$/';
		$arr['text']['cc_visa']['callback'] = 'validate_cc';
		$arr['text']['cc_mastercard']['regexp'] = '/^5[1-5]{1}[0-9]{14}$/';
		$arr['text']['cc_mastercard']['callback'] = 'validate_cc';
		$arr['text']['cc_americanexpress']['regexp'] = '/^3(4|7){1}[0-9]{13}$/';
		$arr['text']['cc_americanexpress']['callback'] = 'validate_cc';
		$arr['text']['cc_discover']['regexp'] = '/^6011[0-9]{12}$/';
		$arr['text']['cc_discover']['callback'] = 'validate_cc';
		$arr['text']['cc_dinersclub']['regexp'] = '/^3((0[0-5]{1}[0-9]{11})|(6[0-9]{12})|(8[0-9]{12}))$/';
		$arr['text']['cc_dinersclub']['callback'] = 'validate_cc';
		$arr['text']['zip_generic']['regexp'] = '/^\d+$/';
		$arr['text']['zip_us5']['regexp'] = '/^\d{5}$/';
		$arr['text']['zip_us9']['regexp'] = '/^\d{5}-\d{4}$/';
		$arr['text']['zip_canada']['regexp'] = '/^[A-Z]{1}\d[A-Z]{1}\s?\d[A-Z]{1}\d$/i';
		$arr['text']['zip_uk']['regexp'] = '/^[A-Z]{1,2}\d[\dA-Z]?\s?\d[A-Z]{2}$/i';
		$arr['text']['phone']['regexp'] = '/^[(]?[+]{0,2}[0-9-.\s\/()]+$/';
		$arr['text']['ssn']['regexp'] = '/^\d{3}\s?\d{2}\s?\d{4}$/';
		// implemented RFC 3986 http://tools.ietf.org/html/rfc3986
		$arr["text"]['url']['regexp'] = '/^(?:https?|ftp)\:\/\/(?:(?:[a-z0-9\-\._~\!\$\&\'\(\)\*\+\,\;\=:]|%[0-9a-f]{2,2})*\@)?(?:((?:(?:[a-z0-9][a-z0-9\-]*[a-z0-9]|[a-z0-9])\.)*(?:[a-z][a-z0-9\-]*[a-z0-9]|[a-z])|(?:\[[^\]]*\]))(?:\:[0-9]*)?)(?:\/(?:[a-z0-9\-\._~\!\$\&\'\(\)\*\+\,\;\=\:\@]|%[0-9a-f]{2,2})*)*(?:\?(?:[a-z0-9\-\._~\!\$\&\'\(\)\*\+\,\;\=\:\@\/\?]|%[0-9a-f]{2,2})*)?(?:\#(?:[a-z0-9\-\._~\!\$\&\'\(\)\*\+\,\;\=\:\@\/\?]|%[0-9a-f]{2,2})*)?$/i';
		$arr['text']['ip']['regexp'] = '/^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/';
		$arr['text']['ip']['callback'] = 'validate_ip';
		$arr['text']['color_generic']['callback'] = 'validate_color';
		$arr['text']['color_hex']['regexp'] = '/^#[0-9a-f]{6}$/i';
		
		$arr['numeric'] = array();
		$arr['numeric'][''] = array();
		$arr['numeric']['int']['regexp'] = '/^-?\d+$/';
		$arr['numeric']['int_positive']['regexp'] = '/^\d+$/';
		$arr['numeric']['int_positive']['callback'] = 'validate_positive';
		$arr['numeric']['zip_generic']['regexp'] = '/^\d+$/';
		
		$arr['double'][''] = array();
		$arr['double']['float']['regexp'] = '/^-?[0-9]*(\.|,)?[0-9]+([eE]\-[0-9]+)?$/';
		$arr['double']['float_positive']['regexp'] = '/^[0-9]*(\.|,)?[0-9]+([eE]\-[0-9]+)?$/';
		$arr['double']['float_positive']['callback'] = 'validate_positive';
		
		$arr['date'] = array();
		$arr['date']['date'] = array();
		$arr['date']['datetime'] = array();
		$arr['date']['time'] = array();
		
		$arr['mask'] = array();
		$arr['regexp'] = array();
	}

	function loadGenericValidationMessages(&$arr) {
		$d = 'tNG_FormValidation';
		
		$arr["failed"] = KT_getResource('FAILED', $d);
		
		$arr["required"] = KT_getResource('REQUIRED', $d);
		$arr["type"] = KT_getResource('TYPE', $d);
		$arr["format"] = KT_getResource('FORMAT', $d);
		
		$arr["text_"] = KT_getResource('TEXT_', $d);
		$arr["text_email"] = KT_getResource('TEXT_EMAIL', $d);
		$arr["text_cc_generic"] = KT_getResource('TEXT_CC_GENERIC', $d);
		$arr["text_cc_visa"] = KT_getResource('TEXT_CC_VISA', $d);
		$arr["text_cc_mastercard"] = KT_getResource('TEXT_CC_MASTERCARD', $d);
		$arr["text_cc_americanexpress"] = KT_getResource('TEXT_CC_AMERICANEXPRESS', $d);
		$arr["text_cc_discover"] = KT_getResource('TEXT_CC_DISCOVER', $d);
		$arr["text_cc_dinersclub"] = KT_getResource('TEXT_CC_DINERSCLUB', $d);
		$arr["text_zip_generic"] = KT_getResource('TEXT_ZIP_GENERIC', $d);
		$arr["text_zip_us5"] = KT_getResource('TEXT_ZIP_US5', $d);
		$arr["text_zip_us9"] = KT_getResource('TEXT_ZIP_US9', $d);
		$arr["text_zip_canada"] = KT_getResource('TEXT_ZIP_CANADA', $d);
		$arr["text_zip_uk"] = KT_getResource('TEXT_ZIP_UK', $d);
		$arr["text_phone"] = KT_getResource('TEXT_PHONE', $d);
		$arr["text_ssn"] = KT_getResource('TEXT_SSN', $d);
		$arr["text_url"] = KT_getResource('TEXT_URL', $d);
		$arr["text_ip"] = KT_getResource('TEXT_IP', $d);
		$arr["text_color_hex"] = KT_getResource('TEXT_COLOR_HEX', $d);
		$arr["text_color_generic"] = KT_getResource('TEXT_COLOR_GENERIC', $d);
		
		$arr["numeric_"] = KT_getResource('NUMERIC_', $d);
		$arr["numeric_int"] = KT_getResource('NUMERIC_INT', $d);
		$arr["numeric_int_positive"] = KT_getResource('NUMERIC_INT_POSITIVE', $d);
		$arr["numeric_zip_generic"] = KT_getResource('TEXT_ZIP_GENERIC', $d);
		
		$arr["double_"] = KT_getResource('DOUBLE_', $d);
		$arr["double_float"] = KT_getResource('DOUBLE_FLOAT', $d);
		$arr["double_float_positive"] = KT_getResource('DOUBLE_FLOAT_POSITIVE', $d);
		
		$arr["date_"] = KT_getResource('DATE_', $d);
		$arr["date_date"] = KT_getResource('DATE_DATE', $d);
		$arr["date_time"] = KT_getResource('DATE_TIME', $d);
		$arr["date_datetime"] = KT_getResource('DATE_DATETIME', $d);
		
		$arr["mask_"] = KT_getResource('MASK_', $d);
		
		$arr["regexp_"] = KT_getResource('REGEXP_', $d);
		$arr["regexp_failed"] = KT_getResource('REGEXP_FAILED', $d);
		
		$arr["text_min"] = KT_getResource('TEXT_MIN', $d);
		$arr["text_max"] =  KT_getResource('TEXT_MAX', $d); 
		$arr["text_between"] = KT_getResource('TEXT_BETWEEN', $d); 
		
		$arr["other_min"] = KT_getResource('OTHER_MIN', $d);
		$arr["other_max"] = KT_getResource('OTHER_MAX', $d);
		$arr["other_between"] = KT_getResource('OTHER_BETWEEN', $d);
	}

	/**
	NAME:
		Execute
	DESCRIPTION:
		validates the columnsValue based on regExp and required information
	ARGUMENTS:
		none - 
		property used: 
				$columns
				$columnsValue
	RETURN:
		string - empty on succes , an error message if fails
		property changed:
			- none
	**/
	function Execute() {
		$failed = false;
		$errObj = new tNG_error('', array(), array());
		
		if ($this->mustValidate && count($this->columns) > 0) {
			$columnKeys = array_keys($this->columns);
			$cols = count($columnKeys);
			for ($i = 0; $i < $cols; $i++) {
				$doRequiredVal = true;
				$colIdx = $columnKeys[$i];
				$column = &$this->columns[$colIdx];
				
				if ( !in_array($column['name'], array_keys($this->tNG->columns)) ) {
					continue;
				}
				
				// on update don't require FILE_TYPE and tNG password fields
				if ($this->tNG->getTransactionType() == '_update' || $this->tNG->getTransactionType() == '_multipleUpdate') {
					if ($this->tNG->getColumnType($column['name']) == 'FILE_TYPE') {
						$doRequiredVal = false;
					}
					if ($this->tNG->getTable() == $GLOBALS['tNG_login_config']["table"] && $column['name'] == $GLOBALS['tNG_login_config']["password_field"]) {
						$doRequiredVal = false;
					}
					// if it is setted to CURRVAL is not required;
					if ($this->tNG->columns[$column['name']]['method'] == 'CURRVAL') {
						$doRequiredVal = false;
					}
				}
				
				$hasRequiredError = false;
				$hasTypeError = false;
				
				$tmpFieldValue = $this->tNG->getColumnValue($column['name']);
				
				if ($column['type'] == 'date' && $column['format'] != '') {
					if ( !in_array($this->tNG->getColumnType($column['name']), array('DATE_TYPE', 'DATE_ACCESS_TYPE')) ) {
						$tmpFieldValue = KT_formatDate2DB($tmpFieldValue);
					}
				}
				
				$column['failed'] = false;
				
				// required parameter validation
				$colCustomMsg = $column['message'];
				if ($doRequiredVal && $column['required']) {
					if (strlen($colCustomMsg) == 0) {
						$colCustomMsg = $this->genericValidationMessages['required'];
					}
					if ((string)($tmpFieldValue) == '') {
						$failed = true;
						$hasRequiredError = true;
						$column['failed'] = true;
						if ($this->tNG->exportsRecordset() !== true) {
							$colCustomMsg = KT_DynamicData($colCustomMsg, $this->tNG, '', ($this->tNG->transactionType == '_delete'));
							$errObj->addDetails('%s', array($colCustomMsg), array($colCustomMsg));
						} else {
							$errObj->setFieldError($column['name'], '%s', array($colCustomMsg));
						}
					}
				}
				
				// type and format validation
				$colCustomMsg = $column['message'];
				if ($tmpFieldValue != '' && $column['type'] != '') {
					if (strlen($colCustomMsg) == 0) {
						$colCustomMsgBefore = $this->genericValidationMessages['format'];
						$colCustomMsgAfter = $this->genericValidationMessages[$column['type'] . '_' . $column['format']];
						$colCustomMsg = sprintf($colCustomMsgBefore, $colCustomMsgAfter);
					}
					$tmpFieldValue = substr($tmpFieldValue, 0, 400);
					switch ($column['type']) {
						case 'regexp':
							$res = @preg_match($column['additional_params'], $tmpFieldValue);
							if ($res === false) {
								$hasTypeError = true;
								$colCustomMsgBefore = $this->genericValidationMessages['format'];
								$colCustomMsgAfter = $this->genericValidationMessages['regexp_failed'];
								$colCustomMsg = sprintf($colCustomMsgBefore, $colCustomMsgAfter);
							}
							if ($res === 0) {
								$hasTypeError = true;
							}
							break;
						case 'mask':
							$myRegexp = $this->mask2regexp($column['additional_params']);
							if (!preg_match($myRegexp, $tmpFieldValue)) {
								$hasTypeError = true;
							}
							break;
						case 'text':
						case 'numeric':
						case 'double':
							$type = $column['type'];
							$format = $column['format'];
							if (is_array($this->validationRules[$type][$format])) {
								$myValidationRule = &$this->validationRules[$type][$format];
								if (isset($myValidationRule['mask'])) {
									$myRegexp = $this->mask2regexp($myValidationRule['mask']);
									$myValidationRule['regexp'] = $myRegexp;
								}
								if (isset($myValidationRule['regexp'])) {
									if (!preg_match($myValidationRule['regexp'], $tmpFieldValue)) {
										$hasTypeError = true;
									}
								}
								if (isset($myValidationRule['callback'])) {
									$ret = call_user_func(array('tNG_FormValidation', $myValidationRule['callback']), $tmpFieldValue);
									if (!$ret) {
										$hasTypeError = true;
									}
								}
							}
							break;
						case 'date':
							$format = $column['format'];
							$checkFullDateTime = true;
							switch ($format) {
								case 'date':
									$inFmtRule = KT_format2rule($GLOBALS['KT_db_date_format']);
									$checkFullDateTime = true;
									break;
								case 'time':
									$inFmtRule = KT_format2rule($GLOBALS['KT_db_time_format_internal']);
									$checkFullDateTime = false;
									break;
								case 'datetime':
									$inFmtRule = KT_format2rule($GLOBALS['KT_db_date_format'] . ' ' . $GLOBALS['KT_db_time_format_internal']);
									$checkFullDateTime = true;
									break;
								default:
									break 2;
							}
							$dateArr = KT_applyDate2rule($tmpFieldValue, $inFmtRule);
							$ret = KT_isValidDate($dateArr, $checkFullDateTime);
							if (!$ret) {
								$hasTypeError = true;
							}
							break;
					}
				}
				if (!$hasRequiredError && $hasTypeError) {
					$column['failed'] = true;
					$failed = true;
						if ($this->tNG->exportsRecordset() !== true) {
							$colCustomMsg = KT_DynamicData($colCustomMsg, $this->tNG, '', ($this->tNG->transactionType == '_delete'));
							$errObj->addDetails('%s', array($colCustomMsg), array($colCustomMsg));
						} else {
							$errObj->setFieldError($column['name'], '%s', array($colCustomMsg));
						}
				}
			}
			
			for ($i = 0; $i < $cols; $i++) {
				$colIdx = $columnKeys[$i];
				$column = &$this->columns[$colIdx];
				
				if ( !in_array($column['name'], array_keys($this->tNG->columns)) ) {
					continue;
				}

				$hasMinMaxError = false;
				
				$tmpFieldValue = $this->tNG->getColumnValue($column['name']);
				
				if ($column['type'] == 'date' && $column['format'] != '') {
					if ( !in_array($this->tNG->getColumnType($column['name']), array('DATE_TYPE', 'DATE_ACCESS_TYPE')) ) {
						$tmpFieldValue = KT_formatDate2DB($tmpFieldValue);
					}
				}
				
				// MIN MAX parameter validation
				$tNG_tNGfield_min = array();
				$tNG_tNGfield_max = array();
				$min = $column['min'];
				$min_placeholders = KT_getReplacementsFromMessage($min);
				if (count($min_placeholders) > 0) {
					foreach ($min_placeholders as $key => $placeholder) {
						if (strpos($placeholder, '.') === false) {
							$tNG_tNGfield_min[] = $placeholder;
						}
					}
				}
				$max = $column['max'];
				$max_placeholders = KT_getReplacementsFromMessage($max);
				if (count($max_placeholders) > 0) {
					foreach ($max_placeholders as $key => $placeholder) {
						if (strpos($placeholder, '.') === false) {
							$tNG_tNGfield_max[] = $placeholder;
						}
					}
				}
				$min = KT_DynamicData($min, $this->tNG);
				$max = KT_DynamicData($max, $this->tNG);
				
				// MIN parameter validation
				if ($tmpFieldValue != '' && $min != '') {
					if ($column['type'] == 'text') {
						if (strlen($tmpFieldValue) < $min) {
							$hasMinMaxError = true;
						}
					}
					if (in_array($column['type'], array('numeric', 'double'))) {
						$evaluateNumeric = true;
						if (count($tNG_tNGfield_min) > 0) {
							foreach ($tNG_tNGfield_min as $key => $tNG_tNGfield) {
								if (!isset($this->columns[$tNG_tNGfield]) || !in_array($this->columns[$tNG_tNGfield]['type'], array('numeric', 'double')) 
									|| $this->columns[$tNG_tNGfield]['format'] == '' || $column['failed']) {
									$evaluateNumeric = false;
									break;
								}
							}
						}
						$tmpFieldValue = str_replace(',', '.', $tmpFieldValue);
						$min = str_replace(',', '.', $min);
						if ($evaluateNumeric) {
							$min = $this->tNG->evaluateNumeric($min);
						}
						if (floatval($tmpFieldValue) < floatval($min)) {
							$hasMinMaxError = true;
						}
					}
					if ($column['type'] == 'date') {
						if (count($tNG_tNGfield_min) > 0) {
							foreach ($tNG_tNGfield_min as $key => $tNG_tNGfield) {
								if ( in_array($this->tNG->getColumnType($tNG_tNGfield), array('DATE_TYPE','DATE_ACCESS_TYPE')) ) {
									$min = KT_formatDate($min);
									break;
								}
							}
						}
						$minDate = KT_formatDate2DB($min);
						$format = $column['format'];
						$checkFullDateTime = true;
						switch ($format) {
							case 'date':
								$inFmtRule = KT_format2rule($GLOBALS['KT_db_date_format']);
								$checkFullDateTime = true;
								break;
							case 'time':
								$inFmtRule = KT_format2rule($GLOBALS['KT_db_time_format_internal']);
								$checkFullDateTime = false;
								break;
							case 'datetime':
								$inFmtRule = KT_format2rule($GLOBALS['KT_db_date_format'] . ' ' . $GLOBALS['KT_db_time_format_internal']);
								$checkFullDateTime = true;
								break;
							default:
								break 2;
						}
						$dateArr = KT_applyDate2rule($tmpFieldValue, $inFmtRule);
						$minArr = KT_applyDate2rule($minDate, $inFmtRule);
						if (KT_isValidDate($minArr, $checkFullDateTime)) {
							if (KT_compareDates($dateArr, $minArr) === 1) {
								$hasMinMaxError = true;
							}
						}
					}
				}
				
				// MAX parameter validation
				if ($tmpFieldValue != '' && $max != '') {
					if ($column['type'] == 'text') {
						if (strlen($tmpFieldValue) > $max) {
							$hasMinMaxError = true;
						}
					}
					if (in_array($column['type'], array('numeric', 'double'))) {
						$evaluateNumeric = true;
						if (count($tNG_tNGfield_max) > 0) {
							foreach ($tNG_tNGfield_max as $key => $tNG_tNGfield) {
								if (!isset($this->columns[$tNG_tNGfield]) || !in_array($this->columns[$tNG_tNGfield]['type'], array('numeric', 'double')) 
									|| $this->columns[$tNG_tNGfield]['format'] == '' || $column['failed']) {
									$evaluateNumeric = false;
									break;
								}
							}
						}
						$tmpFieldValue = str_replace(',', '.', $tmpFieldValue);
						$max = str_replace(',', '.', $max);
						if ($evaluateNumeric) {
							$max = $this->tNG->evaluateNumeric($max);
						}
						if (floatval($tmpFieldValue) > floatval($max)) {
							$hasMinMaxError = true;
						}
					}
					if ($column['type'] == 'date') {
						if (count($tNG_tNGfield_max) > 0) {
							foreach ($tNG_tNGfield_max as $key => $tNG_tNGfield) {
								if ( in_array($this->tNG->getColumnType($tNG_tNGfield), array('DATE_TYPE','DATE_ACCESS_TYPE')) ) {
									$max = KT_formatDate($max);
									break;
								}
							}
						}
						$maxDate = KT_formatDate2DB($max);
						$format = $column['format'];
						$checkFullDateTime = true;
						switch ($format) {
							case 'date':
								$inFmtRule = KT_format2rule($GLOBALS['KT_db_date_format']);
								$checkFullDateTime = true;
								break;
							case 'time':
								$inFmtRule = KT_format2rule($GLOBALS['KT_db_time_format_internal']);
								$checkFullDateTime = false;
								break;
							case 'datetime':
								$inFmtRule = KT_format2rule($GLOBALS['KT_db_date_format'] . ' ' . $GLOBALS['KT_db_time_format_internal']);
								$checkFullDateTime = true;
								break;
							default:
								break 2;
						}
						$dateArr = KT_applyDate2rule($tmpFieldValue, $inFmtRule);
						$maxArr = KT_applyDate2rule($maxDate, $inFmtRule);
						if (KT_isValidDate($maxArr, $checkFullDateTime)) {
							if (KT_compareDates($dateArr, $maxArr) === -1) {
								$hasMinMaxError = true;
							}
						}
					}
				}
				
				$colCustomMsg = $column['message'];
				if (strlen($colCustomMsg) == 0) {
					$colCustomMsgBefore = $column['type'] == 'text'?'text':'other';
					if ($min != '' && $max != '') {
						$colCustomMsgAfter = 'between';
						$colCustomMsg = $this->genericValidationMessages[$colCustomMsgBefore . '_' . $colCustomMsgAfter];
						$colCustomMsg = sprintf($colCustomMsg, $min, $max);
					} elseif ($min != '') {
						$colCustomMsgAfter = 'min';
						$colCustomMsg = $this->genericValidationMessages[$colCustomMsgBefore . '_' . $colCustomMsgAfter];
						$colCustomMsg = sprintf($colCustomMsg, $min);
					} else {
						$colCustomMsgAfter = 'max';
						$colCustomMsg = $this->genericValidationMessages[$colCustomMsgBefore . '_' . $colCustomMsgAfter];
						$colCustomMsg = sprintf($colCustomMsg, $max);
					}
				}
				
				if ($hasMinMaxError && $column['failed'] == false) {
					$column['failed'] = true;
					$failed = true;
						if ($this->tNG->exportsRecordset() !== true) {
							$colCustomMsg = KT_DynamicData($colCustomMsg, $this->tNG, '', ($this->tNG->transactionType == '_delete'));
							$errObj->addDetails('%s', array($colCustomMsg), array($colCustomMsg));
						} else {
							$errObj->setFieldError($column['name'], '%s', array($colCustomMsg));
						}
				}
				
			}
		}
		if (!$failed) {
			$errObj = null;
		} else {
			if ($this->tNG->exportsRecordset() === true) {
				$errObj->addDetails('%s', array($this->genericValidationMessages['failed']), array(''));
			}
		}
		return $errObj;
	}

	// validation callbacks for special types
	function validate_positive($value) {
		$value = str_replace(',', '.', $value);
		if (floatval($value) >= 0) {
			return true;
		}
		return false;
	}

	function validate_ip($value) {
		$pieces = explode('.', $value);
		if (count($pieces) != 4) {
			return false;
		}
		foreach ($pieces as $key => $piece) {
			if ($piece > 255) {
				return false;
			}
		}
		return true;
	}

	function validate_color($value) {
		$colors = array(
			"black",
			"green",
			"silver",
			"lime",
			"gray",
			"olive",
			"white",
			"yellow",
			"maroon",
			"navy",
			"red",
			"blue",
			"purple",
			"teal",
			"fuchsia",
			"aqua"
		);
		if (!in_array(strtolower($value), $colors)) {
			return false;
		}
		return true;
	}

	function validate_cc($value) {
		$digits = array();
		$j = 1;
		for ($i = strlen($value) - 1; $i >= 0; $i--) {
			if (($j%2) == 0) {
				$digit = substr($value, $i, 1) * 2;
				$digits[] = substr($digit, 0, 1);
				if (strlen($digit) == 2) {
					$digits[] = substr($digit, 1, 1);
				}
			} else {
				$digit = substr($value, $i, 1);
				$digits[] = $digit;
			}
			$j++;
		}
		$sum = array_sum($digits);
		if (($sum%10) == 0) {
			return true;
		}
		return false;
	}

}
?>