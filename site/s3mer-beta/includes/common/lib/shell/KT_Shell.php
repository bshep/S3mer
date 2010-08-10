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

	$KT_SHL_uploadErrorMsg = '<strong>File not found:</strong> <br />%s<br /><strong>Please upload the includes/ folder to the testing server.</strong>';
	$KT_SHL_uploadFileList = array('KT_Shell.class.php', '../resources/KT_Resources.php');

	for ($KT_SHL_i=0;$KT_SHL_i<sizeof($KT_SHL_uploadFileList);$KT_SHL_i++) {
		$KT_SHL_uploadFileName = dirname(realpath(__FILE__)). '/' . $KT_SHL_uploadFileList[$KT_SHL_i];
		if (file_exists($KT_SHL_uploadFileName)) {
			require_once($KT_SHL_uploadFileName);
		} else {
			die(sprintf($KT_SHL_uploadErrorMsg,$KT_SHL_uploadFileList[$KT_SHL_i]));
		}
	}

?>