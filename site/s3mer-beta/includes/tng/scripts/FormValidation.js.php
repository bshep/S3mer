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

	include_once(dirname(realpath(__FILE__)) . '/../../common/lib/resources/KT_Resources.php');
	$d = 'tNG_FormValidation';

	KT_sendExpireHeader(60 * 60 * 24);
	header("Content-Type: application/JavaScript");
?>
//Javascript UniVAL Resources
UNI_Messages = {};
UNI_Messages['required']                = '<?php echo KT_escapeJS(KT_getResource('REQUIRED', $d)); ?>';
UNI_Messages['type']                    = '<?php echo KT_escapeJS(KT_getResource('TYPE', $d)); ?>';
UNI_Messages['format']                  = '<?php echo KT_escapeJS(KT_getResource('FORMAT', $d)); ?>';
UNI_Messages['text_']                   = '<?php echo KT_escapeJS(KT_getResource('TEXT_', $d)); ?>';
UNI_Messages['text_email']              = '<?php echo KT_escapeJS(KT_getResource('TEXT_EMAIL', $d)); ?>';
UNI_Messages['text_cc_generic']         = '<?php echo KT_escapeJS(KT_getResource('TEXT_CC_GENERIC', $d)); ?>';
UNI_Messages['text_cc_visa']            = '<?php echo KT_escapeJS(KT_getResource('TEXT_CC_VISA', $d)); ?>';
UNI_Messages['text_cc_mastercard']      = '<?php echo KT_escapeJS(KT_getResource('TEXT_CC_MASTERCARD', $d)); ?>';
UNI_Messages['text_cc_americanexpress'] = '<?php echo KT_escapeJS(KT_getResource('TEXT_CC_AMERICANEXPRESS', $d)); ?>';
UNI_Messages['text_cc_discover']        = '<?php echo KT_escapeJS(KT_getResource('TEXT_CC_DISCOVER', $d)); ?>';
UNI_Messages['text_cc_dinersclub']      = '<?php echo KT_escapeJS(KT_getResource('TEXT_CC_DINERSCLUB', $d)); ?>';
UNI_Messages['text_zip_generic']        = '<?php echo KT_escapeJS(KT_getResource('TEXT_ZIP_GENERIC', $d)); ?>';
UNI_Messages['text_zip_us5']            = '<?php echo KT_escapeJS(KT_getResource('TEXT_ZIP_US5', $d)); ?>';
UNI_Messages['text_zip_us9']            = '<?php echo KT_escapeJS(KT_getResource('TEXT_ZIP_US9', $d)); ?>';
UNI_Messages['text_zip_canada']         = '<?php echo KT_escapeJS(KT_getResource('TEXT_ZIP_CANADA', $d)); ?>';
UNI_Messages['text_zip_uk']             = '<?php echo KT_escapeJS(KT_getResource('TEXT_ZIP_UK', $d)); ?>';
UNI_Messages['text_phone']              = '<?php echo KT_escapeJS(KT_getResource('TEXT_PHONE', $d)); ?>';
UNI_Messages['text_ssn']                = '<?php echo KT_escapeJS(KT_getResource('TEXT_SSN', $d)); ?>';
UNI_Messages['text_url']                = '<?php echo KT_escapeJS(KT_getResource('TEXT_URL', $d)); ?>';
UNI_Messages['text_ip']                 = '<?php echo KT_escapeJS(KT_getResource('TEXT_IP', $d)); ?>';
UNI_Messages['text_color_hex']          = '<?php echo KT_escapeJS(KT_getResource('TEXT_COLOR_HEX', $d)); ?>';
UNI_Messages['text_color_generic']      = '<?php echo KT_escapeJS(KT_getResource('TEXT_COLOR_GENERIC', $d)); ?>';
UNI_Messages['numeric_']                = '<?php echo KT_escapeJS(KT_getResource('NUMERIC_', $d)); ?>';
UNI_Messages['numeric_int']             = '<?php echo KT_escapeJS(KT_getResource('NUMERIC_INT', $d)); ?>';
UNI_Messages['numeric_int_positive']    = '<?php echo KT_escapeJS(KT_getResource('NUMERIC_INT_POSITIVE', $d)); ?>';
UNI_Messages["numeric_zip_generic"]     = '<?php echo KT_escapeJS(KT_getResource('TEXT_ZIP_GENERIC', $d)); ?>';
UNI_Messages['double_float']            = '<?php echo KT_escapeJS(KT_getResource('DOUBLE_FLOAT', $d)); ?>';
UNI_Messages['double_float_positive']   = '<?php echo KT_escapeJS(KT_getResource('DOUBLE_FLOAT_POSITIVE', $d)); ?>';
UNI_Messages['date_']                   = '<?php echo KT_escapeJS(KT_getResource('DATE_', $d)); ?>';
UNI_Messages['date_date']               = '<?php echo KT_escapeJS(KT_getResource('DATE_DATE', $d)); ?>';
UNI_Messages['date_time']               = '<?php echo KT_escapeJS(KT_getResource('DATE_TIME', $d)); ?>';
UNI_Messages['date_datetime']           = '<?php echo KT_escapeJS(KT_getResource('DATE_DATETIME', $d)); ?>';
UNI_Messages['mask_']                   = '<?php echo KT_escapeJS(KT_getResource('MASK_', $d)); ?>';
UNI_Messages['regexp_']                 = '<?php echo KT_escapeJS(KT_getResource('REGEXP_', $d)); ?>';
UNI_Messages['text_min']                = '<?php echo KT_escapeJS(KT_getResource('TEXT_MIN', $d)); ?>';
UNI_Messages['text_max']                = '<?php echo KT_escapeJS(KT_getResource('TEXT_MAX', $d)); ?>';
UNI_Messages['text_between']            = '<?php echo KT_escapeJS(KT_getResource('TEXT_BETWEEN', $d)); ?>';
UNI_Messages['other_min']               = '<?php echo KT_escapeJS(KT_getResource('OTHER_MIN', $d)); ?>';
UNI_Messages['other_max']               = '<?php echo KT_escapeJS(KT_getResource('OTHER_MAX', $d)); ?>';
UNI_Messages['other_between']           = '<?php echo KT_escapeJS(KT_getResource('OTHER_BETWEEN', $d)); ?>';
UNI_Messages['form_was_modified']       = '<?php echo KT_escapeJS(KT_getResource('FORM_WAS_MODIFIED', $d)); ?>';