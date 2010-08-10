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

	$KT_FLD_uploadErrorMsg = '<strong>File not found:</strong> <br />%s<br /><strong>Please upload the includes/ folder to the testing server.</strong>';
	$KT_FLD_uploadFileList = array('KT_Folder.class.php', '../../KT_common.php', '../resources/KT_Resources.php');

	for ($KT_FLD_i=0;$KT_FLD_i<sizeof($KT_FLD_uploadFileList);$KT_FLD_i++) {
		$KT_FLD_uploadFileName = dirname(realpath(__FILE__)). '/' . $KT_FLD_uploadFileList[$KT_FLD_i];
		if (file_exists($KT_FLD_uploadFileName)) {
			require_once($KT_FLD_uploadFileName);
		} else {
			die(sprintf($KT_FLD_uploadErrorMsg,$KT_FLD_uploadFileList[$KT_FLD_i]));
		}
	}

?>