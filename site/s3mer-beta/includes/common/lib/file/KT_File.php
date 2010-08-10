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

	$KT_FIL_uploadErrorMsg = '<strong>File not found:</strong> <br />%s<br /><strong>Please upload the includes/ folder to the testing server.</strong>';
	$KT_FIL_uploadFileList = array('KT_File.class.php', '../../KT_common.php', '../resources/KT_Resources.php', '../folder/KT_Folder.php');

	for ($KT_FIL_i=0;$KT_FIL_i<sizeof($KT_FIL_uploadFileList);$KT_FIL_i++) {
		$KT_FIL_uploadFileName = dirname(realpath(__FILE__)). '/' . $KT_FIL_uploadFileList[$KT_FIL_i];
		if (file_exists($KT_FIL_uploadFileName)) {
			require_once($KT_FIL_uploadFileName);
		} else {
			die(sprintf($KT_FIL_uploadErrorMsg,$KT_FIL_uploadFileList[$KT_FIL_i]));
		}
	}

?>