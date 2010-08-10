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

// tidy configuration values
$GLOBALS['TidyContent_TidyLocations'] = array(
	'C:\utils\tidy.exe',
	'tidy',
	'/usr/bin/tidy',
	'/usr/local/bin/tidy',
	'/usr/local/tidy/bin/tidy',
	'/usr/bin/tidy/bin/tidy',
	'tidy.exe', 
	'C:/Progra~1/tidy/tidy.exe',
	'C:/Windows/tidy.exe', 
	'C:/utils/dlls/tidy.exe'
	);
$GLOBALS['TidyContent_TidyConfiguration'] = KT_RealPath(dirname(realpath(__FILE__)), true) . '.tidyconf';
$GLOBALS['TidyContent_TidyTempPath'] = dirname(__FILE__).'/../../common/_temp/.tidy/';
?>