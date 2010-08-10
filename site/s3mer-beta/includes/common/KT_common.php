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

	$KT_CMN_uploadErrorMsg = '<strong>File not found:</strong> <br />%s<br /><strong>Please upload the includes/ folder to the testing server.</strong>';
	$KT_CMN_uploadFileList = array('KT_config.inc.php', 'KT_functions.inc.php');

	for ($KT_CMN_i=0;$KT_CMN_i<sizeof($KT_CMN_uploadFileList);$KT_CMN_i++) {
		$KT_CMN_uploadFileName = dirname(realpath(__FILE__)). '/' . $KT_CMN_uploadFileList[$KT_CMN_i];
		if (file_exists($KT_CMN_uploadFileName)) {
			require_once($KT_CMN_uploadFileName);
		} else {
			die(sprintf($KT_CMN_uploadErrorMsg,$KT_CMN_uploadFileList[$KT_CMN_i]));
		}
	}
?>