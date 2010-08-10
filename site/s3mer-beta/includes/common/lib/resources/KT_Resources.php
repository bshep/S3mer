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

	$KT_RES_uploadErrorMsg = '<strong>File not found:</strong> <br />%s<br /><strong>Please upload the includes/ folder to the testing server.</strong>';
	$KT_RES_uploadFileList = array('KT_ResourcesFunctions.inc.php', '../../KT_common.php');

	for ($KT_RES_i=0;$KT_RES_i<sizeof($KT_RES_uploadFileList);$KT_RES_i++) {
		$KT_RES_uploadFileName = dirname(realpath(__FILE__)). '/' . $KT_RES_uploadFileList[$KT_RES_i];
		if (file_exists($KT_RES_uploadFileName)) {
			require_once($KT_RES_uploadFileName);
		} else {
			die(sprintf($KT_RES_uploadErrorMsg,$KT_RES_uploadFileList[$KT_RES_i]));
		}
	}

?>