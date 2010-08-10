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

	$KT_EML_uploadErrorMsg = '<strong>File not found:</strong> <br />%s<br /><strong>Please upload the includes/ folder to the testing server.</strong>';
	$KT_EML_uploadFileList = array('../shell/KT_Shell.php', '../resources/KT_Resources.php', 'KT_Captcha.config.php', 'KT_CaptchaImage.class.php');

	for ($KT_EML_i=0;$KT_EML_i<sizeof($KT_EML_uploadFileList);$KT_EML_i++) {
		$KT_EML_uploadFileName = dirname(realpath(__FILE__)). '/' . $KT_EML_uploadFileList[$KT_EML_i];
		if (file_exists($KT_EML_uploadFileName)) {
			require_once($KT_EML_uploadFileName);
		} else {
			die(sprintf($KT_EML_uploadErrorMsg,$KT_EML_uploadFileList[$KT_EML_i]));
		}
	}	
	
	
?>