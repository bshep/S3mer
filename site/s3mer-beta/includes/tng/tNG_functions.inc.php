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
 * Set the necessary information in SESSION for a download;
 * @param string $siteRootPath 
 * @param string $dynamicFolder the name of the folder
 * @param string $dynamicFileName the name of the file
 * @return string path to the tNG file that handling the download
 * @access public
 */
function tNG_downloadDynamicFile($siteRootPath, $dynamicFolder, $dynamicFileName) {
	//Download File 1
	$id = 'KT_download'. md5($siteRootPath.'_'.$dynamicFolder.'_'.$dynamicFileName);
	if (!isset($GLOBALS[$id]) || !is_object($GLOBALS[$id])) {
		$dwnldObj1 = new tNG_Download($siteRootPath, $id);
		$dwnldObj1->setFolder($dynamicFolder);
		$dwnldObj1->setRenameRule($dynamicFileName);
		$dwnldObj1->Execute();
		$GLOBALS[$id] = $dwnldObj1;
	}
	//Execute
	return $GLOBALS[$id]->getDownloadLink();
}
/**
 * Checks if a file specified by the dynamic folder and dynamic file expressions exists
 * @param string $dynamicFolder the folder name (may be a tNG dynamic expression)
 * @param string $dynamicFileName the file name (may be a tNG dynamic expression)
 * @return boolean
 *         true if the file exists, 
 *         false if the file does not exist
 */
function tNG_fileExists($dynamicFolder, $dynamicFileName) {
	$ret = false;
	
	$folder = KT_DynamicData($dynamicFolder,null);
	$fileName = KT_DynamicData($dynamicFileName,null);
	
	if ($fileName != "") {
		$folder = KT_realpath($folder);
		$relPath = KT_realpath($folder . $fileName, false);
		$ret = file_exists($relPath);
	}
	return $ret;
}

/**
 * Creates and returns the image relative path using the dynamic folder and dynamic file expressions
 * @param string $dynamicFolder the folder name (may be a tNG dynamic expression)
 * @param string $dynamicFileName the file name (may be a tNG dynamic expression)
 * @return string
 *         the relative path to the image file, 
 *         empty if the dynamicFileName is empty
 */
function tNG_showDynamicImage($siteRootPath, $dynamicFolder, $dynamicFileName) {
	$folder = KT_DynamicData($dynamicFolder,null);
        $fileName = KT_DynamicData($dynamicFileName,null);
        if($fileName != '') {
	  $folder = str_replace("\\", "/", $folder);
	  if (substr($folder, strlen($folder)-1) != '/') {
		$folder .= '/';
          }
          $fullFileName = KT_realPath($folder . $fileName, false);

          if(tNG_isFileInsideBaseFolder($dynamicFolder, $dynamicFileName) === false) {
            if ($GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT') {
              $baseFileName = dirname($fullFileName);
              $errorMsg = KT_getResource("FOLDER_DEL_SECURITY_ERROR_D", "tNG", Array($baseFileName, tNG_getBaseFolder($dynamicFolder)));
              $relPath = $siteRootPath . "includes/tng/styles/img_not_found.gif\" />" . $errorMsg . "<img style=\"display:none\" src=\"".$siteRootPath."includes/tng/styles/img_not_found.gif";
            } else {
              $relPath = $siteRootPath . "includes/tng/styles/img_not_found.gif";
            }
          } else {
            $relPath = $folder . $fileName;

            if ( $fileName == '' || !file_exists($fullFileName) ) {
	      $relPath = $siteRootPath . "includes/tng/styles/img_not_found.gif";
            }
          }
        } else {
          $relPath = $siteRootPath . "includes/tng/styles/img_not_found.gif";
        }
        
        return $relPath;
}

/**
 * Creates and returns the relative path of an image thumbnail using the dynamic folder and dynamic file expressions
 * @param string $dynamicFolder the folder name (may be a tNG dynamic expression)
 * @param string $dynamicFileName the file name (may be a tNG dynamic expression)
 * @param integer $width the width of the thumbnail to be created
 * @param integer $height the width of the thumbnail to be created
 * @param boolean $proportional specify if the thumbnail preserve the proportions of the original image
 * @return string
 *         the relative path to the image file, 
 *         empty if the dynamicFileName is empty or if the thumbnail could ne be created
 */
function tNG_showDynamicThumbnail($siteRootPath, $dynamicFolder, $dynamicFileName, $width, $height, $proportional) {
	$id = 'KT_thumbnail'.md5($siteRootPath.'_'.$dynamicFolder.'_'. $dynamicFileName.'_'.$width.'_'.$height.'_'.$proportional);
	if (!isset($GLOBALS[$id]) || !is_object($GLOBALS[$id])) {
		$objDynamicThumb1 = new tNG_DynamicThumbnail($siteRootPath, $id);
		$objDynamicThumb1->setFolder($dynamicFolder);
		$objDynamicThumb1->setRenameRule($dynamicFileName);
		$objDynamicThumb1->setResize($width, $height, $proportional);
		$GLOBALS[$id] = $objDynamicThumb1; 
	}
	return $GLOBALS[$id]->Execute();
}

/**
 * Checks if the value for a given expression changed
 * @param string $fieldName unique identifier of the expression to be checked for change
 * @param any $fieldValue the value of the expression to be checked
 * @return boolean
 *         true if the field value has changed
 *         false if not
 */
function tNG_fieldHasChanged($fieldName, $fieldValue) {
	static $values;
	$retVal = false;
	if (!isset($values[$fieldName]) || $values[$fieldName] != $fieldValue) {
		$retVal = true;
	}
	$values[$fieldName] = $fieldValue;
	return $retVal;
}

function tNG_getEscapedStringFromMessage(&$string) {
	$newmessage = preg_replace('/\{[^\s}]+\}/', '%s', $string);
	return $newmessage;
}

/**
 * Sets the value for a specific column
 * @param array &$colDetails column details (one element of the $column array)
 * @access private
 */
function tNG_prepareValues(&$colDetails) {
	$type2alt = array(
		'CHECKBOX_1_0_TYPE'=>'1',
		'CHECKBOX_-1_0_TYPE'=>'-1',
		'CHECKBOX_YN_TYPE'=>"Y",
		'CHECKBOX_TF_TYPE'=>"t",
	);
	if (isset($colDetails['method']) && isset($colDetails['reference']) && isset($colDetails['type'])) {
		$colValue = KT_getRealValue($colDetails['method'], $colDetails['reference']);
		if ($colDetails['method'] == 'VALUE') {
			$colValue = KT_DynamicData($colValue, null);
			if (isset($colDetails['default'])) {
				$colDetails['default'] = $colValue;
			}
		} elseif (isset($colDetails['default'])) {
			$colDetails['default'] = KT_DynamicData($colDetails['default'], null);
		}
		switch ($colDetails['type']) {
			case 'CHECKBOX_YN_TYPE':
			case 'CHECKBOX_1_0_TYPE':
			case 'CHECKBOX_-1_0_TYPE':
			case 'CHECKBOX_TF_TYPE':
				$colValue = !isset($colValue) ?  '' : $type2alt[$colDetails['type']];
				break;
			case 'DATE_TYPE':
			case 'DATE_ACCESS_TYPE':
				$colValue = KT_formatDate2DB($colValue);
				if (isset($colDetails['default'])) {
					$colDetails['default'] = KT_formatDate2DB($colDetails['default']);
				}
				break;
		}
	} else {
		$colValue = "";
	}
	$colDetails['value'] =  $colValue;
}

/**
 * Session functions
 * try to match $tNGinc_path into $absPath_running_script and get the remainings 
 * ( remainings = the relative path of the current file to the root of the site)
 * @return string $valabilitu_path
 * @access public
 */
function tNG_getRememberMePath() {
	$tNGinc_path = KT_getSiteRoot() . '/';
	
	$absPath_running_script = str_replace("\\", "/", $_SERVER['PATH_TRANSLATED']);
	$absPath_running_script = str_replace('//', '/', $absPath_running_script);

	// try to match $tNGinc_path into $absPath_running_script and get the remainings 
	// ( remainings = the relative path of the current file to the root of the site)

	$pos = strpos(strtolower($absPath_running_script), strtolower($tNGinc_path));
	if ($pos === false) {
		$valability_path = "/";
	} else {
		// build the relPath_running_script as the remaining after removing $tNGinc_path from $absPath_running_script
		$relPath_running_script = substr($absPath_running_script, $pos + strlen($tNGinc_path));
		
		$url_running_script = $_SERVER['PHP_SELF'];
		// to get valability path must remove $relPath_running_script from $url_running_script
		$pos = strpos(strtolower($url_running_script), strtolower($relPath_running_script));
		if ($pos === false) {
			$valability_path = "/";
		} else {
			$valability_path = substr($url_running_script, 0, $pos);
		}
	}
	$parts = explode("/",$valability_path);
	$partsURL = array_map("rawurlencode",$parts);
	$valability_path = implode("/", $partsURL);
	return $valability_path;
}

/**
 * Return a random string
 * @param string $len length of the random string
 * @return string random string
 * @access public
 */
function tNG_generateRandomString($len) {
	//make a seed for the random generator
	list($usec, $sec) = explode(' ', microtime());
	$seed =  (float) $sec + ((float) $usec * 100000);
	//generate a new random value
	srand($seed);
	$newstring = md5(rand());
	if ($len) {
		return substr($newstring,0,$len);
	} else {
		return $newstring;
	}
}

/**
 * Return the encripted string useing MD5();
 * @param string $plain_string 
 * @return string encripted string
 */
function tNG_encryptString($plain_string) {
	$encrypted_string = md5($plain_string);
	return $encrypted_string;
}

/**
 * Try to activate an user and login in if the random key and user id exists;
 * @param object $connection object
 * @return string
 * @access public
 */
function tNG_activationLogin(&$connection) {
	if (isset($_GET['kt_login_id']) && isset($_GET['kt_login_random'])) {
		// make an instance of the transaction object
		$loginTransaction_activation = new tNG_login($connection);
		// register triggers
		// automatically start the transaction
		$loginTransaction_activation->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "VALUE", "1");

		// add columns
		$loginTransaction_activation->setLoginType('activation');
		$loginTransaction_activation->addColumn("kt_login_id", $GLOBALS['tNG_login_config']['pk_type'] , "GET", "kt_login_id");
		$loginTransaction_activation->addColumn("kt_login_random", "STRING_TYPE", "GET", "kt_login_random");
		 
		$loginTransaction_activation->executeTransaction();
		if (isset($loginTransaction_activation->columns["kt_login_redirect"])) {
			// return already computed redirect page
			return $loginTransaction_activation->getColumnValue("kt_login_redirect");
		}
	}
	return "";
}

/**
 * try to log in an user using the cookies;
 * @param object $connection object;
 * @return nothing
 */
function tNG_cookieLogin(&$connection) {
	tNG_clearSessionVars();
	if (isset($_SESSION['kt_login_user'])) {
		if ( isset($GLOBALS['tNG_login_config']['logger_table']) && isset($GLOBALS['tNG_login_config']['logger_pk']) && isset($GLOBALS['tNG_login_config']['logger_user_id']) && isset($GLOBALS['tNG_login_config']['logger_ip']) && isset($GLOBALS['tNG_login_config']['logger_datein']) && isset($GLOBALS['tNG_login_config']['logger_datelastactivity']) && isset($GLOBALS['tNG_login_config']['logger_session']) && 
			$GLOBALS['tNG_login_config']['logger_table']!='' && $GLOBALS['tNG_login_config']['logger_pk']!='' &&  $GLOBALS['tNG_login_config']['logger_user_id']!='' && $GLOBALS['tNG_login_config']['logger_ip']!='' && $GLOBALS['tNG_login_config']['logger_datein']!='' && $GLOBALS['tNG_login_config']['logger_datelastactivity']!='' && $GLOBALS['tNG_login_config']['logger_session']!='') {
			$tNG = new tNG_custom($connection);
			$tNG->addColumn('kt_login_id', 'STRING_TYPE', 'EXPRESSION', '{SESSION.kt_login_id}');
			$tNG->executeTransaction();
			Trigger_Login_LoggerOut($tNG);
			return;
		}
	}
	if (isset($_COOKIE['kt_login_id']) && isset($_COOKIE['kt_login_test'])) {
		// make an instance of the transaction object
		$loginTransaction_cookie = new tNG_login($connection);
		// register triggers
		// automatically start the transaction
		$loginTransaction_cookie->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "VALUE", "1");
		
		// add columns
		$loginTransaction_cookie->setLoginType('cookie');
		$loginTransaction_cookie->addColumn("kt_login_id", $GLOBALS['tNG_login_config']['pk_type'] , "COOKIE", "kt_login_id");
		$loginTransaction_cookie->addColumn("kt_login_test", "STRING_TYPE", "COOKIE", "kt_login_test");
		 
		$loginTransaction_cookie->executeTransaction();
	}
}

function tNG_clearSessionVars() {
	if (!isset($_SESSION[md5(KT_getSiteRoot())])) {
		if (isset($_SESSION['kt_login_id'])) {
			unset($_SESSION['kt_login_id']);
		}
		if (isset($_SESSION['kt_login_user'])) {
			unset($_SESSION['kt_login_user']);
		}
		if (isset($_SESSION['kt_login_level'])) {
			unset($_SESSION['kt_login_level']);
		}
	}
}

/**
 * Return the resource for NeXTensio
 * @param string $resourceName the resource name
 * @return string the value for the resource name
 */
function NXT_getResource($resourceName) {
	return KT_getResource($resourceName, 'NXT');
}

/**
 * Delete the thumbnails of an image.
 * @param string $folder absolute path of the folder which contains the image
 * @param string $file the image file name
 * @param string $md5 md5 string
 * @return nothing
 * @access public
 */
function tNG_deleteThumbnails($folder, $fileName, $md5) {
	if ($fileName != '') {
		$path_info = KT_pathinfo($fileName);
		if ($md5 == '') {
			$regexp = '/^'.preg_quote($path_info['filename'],'/').'_\d+x\d+(_w_(\w+))?';
		} else {
			$regexp = '/^'.preg_quote($path_info['filename'],'/').'(_w_(\w+))?';
		}
		if ($path_info['extension'] != "") {
			$regexp	.= '\.'.preg_quote($path_info['extension'],'/');
		}
		$regexp	.= '$/i';
			
		$folderObj = new KT_folder();
		$entry = $folderObj->readFolder($folder, false); 
		if (!$folderObj->hasError()) {
			foreach($entry['files'] as $key => $fDetail) {
				if (preg_match($regexp, $fDetail['name'], $matches)) {
					if ($md5 != '') {
						if (isset($matches[2]) && $matches[2] != $md5) {
							@unlink($folder . $fDetail['name']);
						}
					} else {
						@unlink($folder . $fDetail['name']);
					}					
				}
			}
		}		
	}
}	

/**
 * Create MD5 hash for identyfing "version" of a watermarked image.
 * Only for PRO version	 
 * @param string file path of the watermark image
 * @param string alpha transparency
 * @param array resize options of the watermark
 * @param array alignment of the watermark
 * @return string md5 hash
 * @access public
 */
function tNG_watermarkHash($file, $watermarkAlpha, $watermarkResize, $watermarkAlignment) {
	if (!file_exists($file)) {
		return md5(time() . $watermarkAlpha . implode('_', $watermarkResize) . implode('_', $watermarkAlignment));
	} else {
		return md5(filectime($file) . $watermarkAlpha . implode('_', $watermarkResize) . implode('_', $watermarkAlignment));
	}
}

/**
 * This function returns the base folder of a path that can contain dynamic data. The base folder is considered the path until the first dynamic data expression.
 *
 * @param dynamicBaseFolder The path that can contain dynamic data expressions.
 *
 * @return The base folder. 
 *
 */
function tNG_getBaseFolder($dynamicBaseFolder) {
  $baseFolder = $dynamicBaseFolder;
  $pos = strpos($dynamicBaseFolder, '{');
  if($pos !== false) {
    $baseFolder = substr($dynamicBaseFolder, 0, $pos);
  }
  $baseFolder = KT_realPath($baseFolder, true);

  return $baseFolder;
}

/**
 * This function checks to see if a file is within the specified folder.
 *
 * @param dynamicBaseFolder The folder from which the base folder is computed.
 * @param dynamicFileName The name of the file for which to check.
 *
 * @return True or false whether the file is or is not in the base folder.
 *
 */
function tNG_isFileInsideBaseFolder($dynamicBaseFolder, $dynamicFileName) {
  $baseFolder = tNG_getBaseFolder($dynamicBaseFolder);

  $folder = KT_realPath(KT_DynamicData($dynamicBaseFolder,null));
  $fileName = KT_DynamicData($dynamicFileName,null);
  $absPath = KT_realPath($folder . $fileName, false);

  if(substr($absPath, 0, strlen($baseFolder)) === $baseFolder) {
    return true;
  }

  return false;
}
?>