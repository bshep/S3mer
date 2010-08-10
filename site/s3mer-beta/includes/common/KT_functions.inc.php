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
 * Try to set certain _SERVER vars if not setted from _ENV vars; 
 * @return nothing;
 */
function KT_setServerVariables() {
	if (!isset($_SERVER['QUERY_STRING']) && isset($_ENV['QUERY_STRING'])) {
		$_SERVER['QUERY_STRING'] = $_ENV['QUERY_STRING'];
	}
	if (!isset($_SERVER['QUERY_STRING'])) {
		$_SERVER['QUERY_STRING'] = '';
	}
	if (!isset($_SERVER['PHP_SELF']) && isset($_ENV['PHP_SELF'])) {
		$_SERVER['PHP_SELF'] = $_ENV['PHP_SELF'];
	}
	if (!isset($_SERVER['REQUEST_URI']) && isset($_ENV['REQUEST_URI'])) {
		$_SERVER['REQUEST_URI'] = $_ENV['REQUEST_URI'];
	}
	if (!isset($_SERVER['REQUEST_URI'])) {
		$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'].(isset($_SERVER['QUERY_STRING'])?"?".$_SERVER['QUERY_STRING']:"");
	}
	if (!isset($_SERVER['SERVER_NAME']) && isset($_ENV['SERVER_NAME'])) {
		$_SERVER['SERVER_NAME'] = $_ENV['SERVER_NAME'];
	}
	if (!isset($_SERVER['HTTP_HOST']) && isset($_ENV['HTTP_HOST'])) {
		$_SERVER['HTTP_HOST'] = $_ENV['HTTP_HOST'];
	}
	if (!isset($_SERVER['HTTP_HOST']) && isset($_SERVER['SERVER_NAME'])) {
		$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'];
	}
	if (!isset($_SERVER['HTTPS']) && isset($_ENV['HTTPS'])) {
		$_SERVER['HTTPS'] = $_ENV['HTTPS'];
	}
	if (!isset($_SERVER['HTTP_REFERER']) && isset($_ENV['HTTP_REFERER'])) {
		$_SERVER['HTTP_REFERER'] = $_ENV['HTTP_REFERER'];
	}
	if (!isset($_SERVER['HTTP_USER_AGENT']) && isset($_ENV['HTTP_USER_AGENT'])) {
		$_SERVER['HTTP_USER_AGENT'] = $_ENV['HTTP_USER_AGENT'];
	}
	if (!isset($_SERVER['REMOTE_ADDR']) && isset($_ENV['REMOTE_ADDR'])) {
		$_SERVER['REMOTE_ADDR'] = $_ENV['REMOTE_ADDR'];
	}
	if (!isset($_SERVER['SCRIPT_FILENAME']) && isset($_ENV['SCRIPT_FILENAME'])) {
		$_SERVER['SCRIPT_FILENAME'] = $_ENV['SCRIPT_FILENAME'];
	}
	if (!isset($_SERVER['PATH_TRANSLATED']) && isset($_ENV['PATH_TRANSLATED'])) {
		$_SERVER['PATH_TRANSLATED'] = $_ENV['PATH_TRANSLATED'];
	}
	if (!isset($_SERVER['PATH_TRANSLATED']) && isset($_SERVER['ORIG_PATH_TRANSLATED'])) {
		$_SERVER['PATH_TRANSLATED'] = $_SERVER['ORIG_PATH_TRANSLATED'];
	}
	if (!isset($_SERVER['PATH_TRANSLATED']) && isset($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['PHP_SELF'])) {
		$_SERVER['PATH_TRANSLATED'] = $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'];
		$_SERVER['PATH_TRANSLATED'] = str_replace("\\", "/", $_SERVER['PATH_TRANSLATED']);
		$_SERVER['PATH_TRANSLATED'] = str_replace('//', '/', $_SERVER['PATH_TRANSLATED']);
	}
	if (!isset($_SERVER['PATH_TRANSLATED']) && isset($_SERVER['SCRIPT_FILENAME'])) {
		$_SERVER['PATH_TRANSLATED'] = $_SERVER['SCRIPT_FILENAME'];
	}
	if (!isset($_SERVER['SERVER_PROTOCOL']) && isset($_ENV['SERVER_PROTOCOL'])) {
		$_SERVER['SERVER_PROTOCOL'] = $_ENV['SERVER_PROTOCOL'];
	}
	if (!isset($GLOBALS['HTTP_SERVER_VARS'])) {
		$GLOBALS['HTTP_SERVER_VARS'] = &$_SERVER;
	}
	if (!isset($GLOBALS['HTTP_GET_VARS'])) {
		$GLOBALS['HTTP_GET_VARS'] = &$_GET;
	}
	if (!isset($GLOBALS['HTTP_POST_VARS'])) {
		$GLOBALS['HTTP_POST_VARS'] = &$_POST;
	}
	if (!isset($GLOBALS['HTTP_COOKIE_VARS'])) {
		$GLOBALS['HTTP_COOKIE_VARS'] = &$_COOKIE;
	}
	if (!isset($GLOBALS['HTTP_SESSION_VARS'])) {
		$GLOBALS['HTTP_SESSION_VARS'] = &$_SESSION;
	}
	if (!isset($GLOBALS['HTTP_ENV_VARS'])) {
		$GLOBALS['HTTP_ENV_VARS'] = &$_ENV;
	}
}

/**
 * Try to transform a relative path in an absolut one; 
 * @param string $pageUrl the url of the calling script;
 * @param string $templateUrl the url of the template url;
 * @param string $relUrl the url to transform in absolute if case;
 * @param boolean $makeAbsolut flag to transform or not in absolut url;
 * @return string return the absolute url;
 */
function KT_Rel2AbsUrl($pageUrl, $templateUrl, $relUrl, $makeAbsolut) {
	$server = KT_getServerName();
	if (!$makeAbsolut) {
		$pageUrl = str_replace($server, '', $pageUrl);
		$templateUrl = str_replace($server, '', $templateUrl);
		$templateUrl = str_replace($pageUrl, '', $templateUrl);
		$server = '';
	} 
	if (substr($relUrl,0,1) == "/") {
		return $server.$relUrl;
	}
	if (strpos($relUrl,"://") !== false) {
		return $relUrl;
	}

	$arrTemplateUrl = explode('/', $templateUrl);
	array_pop($arrTemplateUrl);
	if (strpos($templateUrl,"://") !== false) {
		$ret = implode('/', $arrTemplateUrl) . (count($arrTemplateUrl)>0?'/':'') . $relUrl;
	} else {
		$arrPageUrl = explode('/', $pageUrl);
		array_pop($arrPageUrl);
		$ret = implode('/', $arrPageUrl) . '/' . implode('/', $arrTemplateUrl) . (count($arrTemplateUrl)>0?'/':'') . $relUrl;
	}
	
	$ret = KT_CanonizeRelPath($ret);
	return $ret;
}

/**
 * Try to canonize a path (replace ../ and ./ ); 
 * @param string $relPath the path to be canonize;
 * @return string return the canonized path;
 */
function KT_CanonizeRelPath($relPath) {
	if (strpos($relPath, "..") !== false || strpos($relPath, "/.") !== false) {
		$parts = explode('/',$relPath);
		$newParts = array();
		for($i=0;$i<count($parts);$i++) {
			if ($parts[$i] == '..') {
				if (count($newParts) > 0 && $newParts[count($newParts) - 1] != '..') {
					array_pop($newParts);
				} else {
					$newParts[] = $parts[$i];
				}
			} elseif ($parts[$i] != '.') {
				$newParts[] = $parts[$i];
			}
		}
		$ret = implode('/',$newParts);
	} else {
		$ret = $relPath;
	}
	return $ret;
}

/**
 * Transforms path containing \ to path with / (URI paths) 
 * Only for PRO version	 
 * @param string path to be transformed;
 * @param boolean indicates if path is file or folder
 * @return string;
 * @access public
 */
function KT_TransformToUrlPath($path, $is_folder = true) {
	$path = str_replace(array('\\', '/'), '/', $path);
	if ($path != '' && $is_folder && substr($path, -1, 1) != '/') {
		$path .= '/';
	}
	return $path;
}

/**
 * Try to transform the relative url of any <a>, <link>, <img> in an absolute one; 
 * The absolute path is considered to be the path to the file in which the function is
 * called. 
 * @param string $templateUrl the url of the file in which is the text;
 * @param string $text the text to be parsed;
 * @param boolean $makeAbsolut flag to transform or not in absolut url;
 * @return string return the text with absolute paths in;
 */
/**
* Known bugs:
* 	1. URL contains \" or \'
*	2. URL contains inside "simple quotes" or 'double quotes' will be removed
*/
function KT_transformsPaths($templateUrl, $text, $makeAbsolut) {
	if ($templateUrl == "./" ) {
		$templateUrl = "";
	}
	
	$scriptUrl = KT_getUri();
	preg_match_all('/<(a|img|link|script|form|iframe|embed|applet)([^>]*)>/ims',$text, $matches);
	foreach($matches[2] as $key=>$value) {
		preg_match('/\s(href|src|action|codebase|archive)\s*=\s*(("((\\\"|[^"])+)")|(\'((\\\\\'|[^\'])+)\')|([^\s]+))?/ims' ,$value, $mmatch);
		if (isset($mmatch[2])) {
			if (preg_match("/\s*mailto\s*:/ims", $mmatch[2])) {
				continue;
			}
			$mydelim = '';
			if (substr($mmatch[2], 0, 1) == '\'' || substr($mmatch[2], 0, 1) == '"') {
				$mydelim = substr($mmatch[2], 0, 1);
				$mmatch[2] = substr($mmatch[2], 1, -1);
			}
			if (!($mmatch[1] == 'href' && strpos($mmatch[2], 'javascript:') === 0)
			&& !($mmatch[1] == 'href' && strpos($mmatch[2], '#') === 0)
			&& !($mmatch[1] == 'href' && strpos($mmatch[2], 'mailto:') === 0)
			&& !($mmatch[1] == 'action' && $mmatch[2] == "")) {
				$text = preg_replace("/".$mmatch[1]."\s*=\s*".preg_quote($mydelim . $mmatch[2] . $mydelim, "/")."/ims", $mmatch[1].'="'.KT_Rel2AbsUrl($scriptUrl, $templateUrl, $mmatch[2], $makeAbsolut).'"', $text);
			}
		}
	}
	preg_match_all('/<input([^>]*)>/ims',$text, $matches);
	foreach($matches[1] as $key=>$value) {
		$sub_text = preg_replace('/\svalue\s*=\s*(("((\\\")+|[^"]+)")|(\'((\\\\\')+|[^\']+)\')|([^\s]+))?/ims', '', $value);
		preg_match('/\ssrc\s*=\s*(("((\\\"|[^"])+)")|(\'((\\\\\'|[^\'])+)\')|([^\s]+))?/ims', $sub_text, $mmatch);
		if (isset($mmatch[1])) {
			$mydelim = '';
			if (substr($mmatch[1], 0, 1) == '\'' || substr($mmatch[1], 0, 1) == '"') {
				$mydelim = substr($mmatch[1], 0, 1);
				$mmatch[1] = substr($mmatch[1], 1, -1);
			}
			$text = preg_replace("/src\s*=\s*".preg_quote($mydelim . $mmatch[1] . $mydelim, "/")."/ims", 'src="'.KT_Rel2AbsUrl($scriptUrl, $templateUrl, $mmatch[1], $makeAbsolut).'"', $text);
		}
	}
	preg_match_all('/<param\s+name=(\'|")(movie|src|fileName)(\'|")([^>]*)>/ims',$text, $matches);
	foreach($matches[4] as $key=>$value) {
		preg_match('/\s(value)\s*=\s*(("((\\\"|[^"])+)")|(\'((\\\\\'|[^\'])+)\')|([^\s]+))?/ims' ,$value, $mmatch);
		if (isset($mmatch[2])) {
			$mydelim = '';
			if (substr($mmatch[2], 0, 1) == '\'' || substr($mmatch[2], 0, 1) == '"') {
				$mydelim = substr($mmatch[2], 0, 1);
				$mmatch[2] = substr($mmatch[2], 1, -1);
			}
			$text = preg_replace("/".$mmatch[1]."\s*=\s*".preg_quote($mydelim . $mmatch[2] . $mydelim, "/")."/ims", $mmatch[1].'="'.KT_Rel2AbsUrl($scriptUrl, $templateUrl, $mmatch[2], $makeAbsolut).'"', $text);
		}
	}
	if (preg_match("/UNI_navigateCancel/i", $text)) {
		preg_match_all("/UNI_navigateCancel\(event, '([\.\/]*includes\/nxt\/back.php)'\)/ims",$text, $matches);
		foreach($matches[1] as $key=>$value){
			$text = str_replace($value, KT_Rel2AbsUrl($scriptUrl, $templateUrl, $value, $makeAbsolut), $text);
		}
	}
	if (preg_match("/NEXT_ROOT=/i", $text)) {
		preg_match_all("/NEXT_ROOT=\"([^\"]*)\"/ims",$text, $matches);
		foreach($matches[1] as $key=>$value){
			$text = str_replace($value, KT_Rel2AbsUrl($scriptUrl, $templateUrl, $value, $makeAbsolut), $text);
		}
	}
	if (preg_match('/\$MXW_relPath\s=\s/i', $text)) {
		preg_match_all('/\$MXW_relPath\s=\s\'([^\']*)\';/is',$text, $matches);
		foreach($matches[1] as $key=>$value){
			$text = str_replace('$MXW_relPath = \'' . $value . '\';', '$MXW_relPath = \'' . KT_Rel2AbsUrl($scriptUrl, $templateUrl, $value, $makeAbsolut) . '\';', $text);
		}
	}
	if (preg_match("/\"path\": \".*includes\/ktm\/\",/i", $text)) {
		preg_match_all("/\"path\": \"(.*includes\/ktm\/)\",/ims",$text, $matches);
		foreach($matches[1] as $key=>$value){
			$text = preg_replace("/\"path\": \"" . preg_quote($value,"/") ."\"/ims", "\"path\": \"". KT_Rel2AbsUrl($scriptUrl, $templateUrl, $value, $makeAbsolut). "\"", $text);
		}
	}
	
	// make absolute URLs for AC_FL_RunContent
	if(preg_match_all('/AC_FL_RunContent\((.*)\);/Uims', $text, $matches)) {
		foreach($matches[1] as $key=>$value) {
			$arr = explode(',', $value);

			// check for , in the file name
			$offset = 0;
			if(substr($arr[11], 0, 1) !== "'") {
				$arr[10] = $arr[10] . "," . $arr[11];
				unset($arr[11]);
				$offset++;
			}
			if (isset($arr[17]) && substr($arr[17 + $offset], 0, 1) !== "'") {
				$arr[16 + $offset] = $arr[16 + $offset] . "," . $arr[17 + $offset];
				unset($arr[17 + $offset]);
			}

			$arr[10] = trim($arr[10]);
			$arr[10] = "'" . KT_Rel2AbsUrl($scriptUrl, $templateUrl, substr($arr[10], 1, strlen($arr[10]) - 2), $makeAbsolut) . "'";

			$arr[16 + $offset] = trim($arr[16 + $offset]);
			$arr[16 + $offset] = "'" . KT_Rel2AbsUrl($scriptUrl, $templateUrl, substr($arr[16 + $offset], 1, strlen($arr[16 + $offset]) - 2), $makeAbsolut) . "'";
			
			$text = str_replace($value, implode(',', $arr), $text);
		}
	}
	
	return $text;
}

/**
 * Return the server name on which the page reside. 
 * @return string return the server name (ex. http://server.com/ );
 */
function KT_getServerName() {
	$protocol = 'http';
	if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
		$protocol = 'https';
	}
	$host = $_SERVER['HTTP_HOST'];
	$baseUrl = $protocol . '://' . $host;
	if (substr($baseUrl, -1)=='/') {
		$baseUrl = substr($baseUrl, 0, strlen($baseUrl)-1);
	}
	return $baseUrl;
}

/**
 * Return the current page name and the query string if exists. 
 * @return string current page and query string (ex. /dir/index.php );
 */
function KT_getPHP_SELF() {
	KT_setServerVariables();
	$script = $_SERVER['REQUEST_URI'];
	if (strpos($script, '?') !== false) {
		$pos = strpos($script, '?');
		$script = substr($script, 0, $pos);
	}
	if (substr($script, -1) == '/' && substr($_SERVER['PHP_SELF'], -1) != '/') {
		$file = basename($_SERVER['PHP_SELF']);
		$script .= $file;
	}
	return $script;
}

/**
 * Return the URL of the page in which the script is called. 
 * @return string return the URL (ex. http://server.com/dir/papa.php );
 */
function KT_getUri() {
	$script = KT_getPHP_SELF();
	if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] != '' && $_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF']) {
		$script = substr($script, 0, strlen($script) - strlen($_SERVER['PATH_INFO']));
	}
	return KT_getServerName() . $script;
}

/**
 * Return the folder of the page in which the script is called. 
 * @return string return the folder (ex. http://mysite/admin/ );
 */
function KT_getUriFolder() {
	$ret = KT_getUri();
	if (substr($ret,-1,1) != '/') {
		$ret = dirname($ret).'/';
	}
	return $ret;
}

/**
 * Return the URL of the page in which the script is called and the query string if exists. 
 * @return string return the URL (ex. http://server.com/dir/papa.php?mod=return );
 */
function KT_getFullUri() {
	$ret = KT_getUri();
	if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
		$pos = strpos($_SERVER['REQUEST_URI'], '?');
		$query_string = substr($_SERVER['REQUEST_URI'], $pos);
		if (trim($query_string) != '') {
			$ret = $ret . $query_string;
		}
	}
	return $ret;
}

/**
 * Adding or replacing a param/value in the string. 
 * @param string $qstring the query string wich will be the subject (ex. ?myvar=1&myvar2=a or myvar=3&myvar2=v);
 * @param string $paramName the name of the variable wich will be strip from or will have a new value (ex. myvar or othervar);
 * @param string $paramValue if is not null this will be the value of the $paramName, if not the $paramName will be strip off (ex. myvalue or null);
 * @return string return the URL (ex. ?myvar=myvalue&myvar2=a or ?myvar2=v );
 */
function KT_addReplaceParam($qstring, $paramName, $paramValue=null) {

	// extract the URI if any
	if (strpos($qstring, "?") !== false) {
		$uri = preg_replace("/\?.*$/", "?", $qstring);
		$qstring = preg_replace("/^.*\?/", "", $qstring);
	} else {
		if (strpos($qstring, "=") !== false) {
			$uri = "";
		} else {
			$uri = $qstring;
			if ($paramValue !== null) {
				$uri .= "?";
			}
			$qstring = "";
		}
	}

	// the list of parameters
	$arr = explode('&',$qstring);

	// remove $paramName from the list
	foreach($arr as $key=>$value) {
		$tmpArr = explode('=',$value);
		if (urldecode($tmpArr[0]) == $paramName) {
			unset($arr[$key]);
			break;
		} else {
			if (strpos($paramName, "/") === 0) {
				if (preg_match($paramName, urldecode($tmpArr[0]))) {
					unset($arr[$key]);
					break;
				}
			}
		}
	}

	// add $paramName to the list
	if ($paramValue !== null) {
		$arr[] = rawurlencode($paramName).'='.rawurlencode($paramValue);
	}

	$ret = implode('&',$arr);
	$ret = preg_replace("/^&/", "", $ret);

	// if no parameters, remove the trailing ? from the URI
	if ($ret == '') {
		$uri = preg_replace("/\?$/", "", $uri);
	}

	// merge the URI with the new list
	$ret = $uri . $ret;

	return $ret;
}

/**
 * Make the redirect on server side; 
 * @param string $url the URL to which is makeing the redirect;
 * @return nothing;
 */
function KT_redir($url) {
	$protocol = "http://";
	$server_name = $_SERVER["HTTP_HOST"];
	if ($server_name != '') {
		$protocol = "http://";
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on")) {
			$protocol = "https://";
		}
		if (preg_match("#^/#", $url)) {
			$url = $protocol.$server_name.$url;
		} else if (!preg_match("#^[a-z]+://#", $url)) {
			$script = KT_getPHP_SELF();
			if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] != '' && $_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF']) {
				$script = substr($script, 0, strlen($script) - strlen($_SERVER['PATH_INFO']));
			}
			$url = $protocol.$server_name.(preg_replace("#/[^/]*$#", "/", $script)).$url;
                }
                session_write_close();
		$url = str_replace(" ","%20",$url);
		if (KT_is_ajax_request()) {
			header("Kt_location: ".$url);
			echo "Redirecting to: " . $url;
		} else {
			header("Location: ".$url);
		}
	}
	exit;
}

/**
 * Return $GLOBALS['KT_REL_PATH'] in front of the URL if the URL is not absolute and $GLOBALS['KT_REL_PATH'] is setted; 
 * @param string $url the URL string (ex. admin/shop/);
 * @return nothing (ex. ../../admin/shop/);
 */
function KT_makeIncludedURL($url) {
	$ret = $url;
	if (isset($GLOBALS['KT_REL_PATH'])) {
		if (!preg_match("#^/#", $ret) && !preg_match("#^[a-z]+://#", $ret)) {
			$ret = $GLOBALS['KT_REL_PATH'] . $ret;
		}
	}
	return $ret;
}

/**
 * Escape the " > < from a string with the html entities; 
 * @param string $val the value to be escaped;
 * @return string the escaped value;
 */
function KT_escapeAttribute($val) {
	$val = str_replace(array('"',"<",">"), array("&quot;","&lt;","&gt;"), $val);
	return $val;
}

/**
 * Apply addcslashes php function on the argument for the charlist: \t\r\n\'\\ ; 
 * @param string $val the value to be escaped;
 * @return string the escaped value;
 */
function KT_escapeJS($val) {
	$val = addcslashes($val, "\t\r\n\'\\");
	return $val;
}

/**
 * Return the real path of the folder where includes folder resides ; 
 * @return string the siteRoot path (ex. var/html/www/mysite/);
 */
function KT_getSiteRoot() {
	$siteroot = dirname(realpath(__FILE__)) . '/../..';
	$siteroot = str_replace(DIRECTORY_SEPARATOR, '/', $siteroot);
	$siteroot = KT_CanonizeRelPath($siteroot);
	return $siteroot;
}

/**
 * Start the session if it is not started and put in session the values from _SESSION[md5($siteroot)] ; 
 * @return nothing;
 */
function KT_session_start() {
	if (!session_id()) {
		@session_start();
		if (!session_id()) {
			die('Your session is incorrectly defined and cannot be started. Check your php.ini configuration.');
		}
	}
	
        $siteroot = md5(KT_getSiteRoot());
        if (isset($_SESSION['KT_lastsiteroot'])) {
          $lastsiteroot = $_SESSION['KT_lastsiteroot'];
          if (isset($_SESSION[$lastsiteroot]) && is_array($_SESSION[$lastsiteroot])) {
            foreach ($_SESSION[$lastsiteroot] as $key => $value) {
              unset($_SESSION[$key]);
            }
          }
        }

	if ( isset($_SESSION[$siteroot]) && is_array($_SESSION[$siteroot]) ) {
		foreach ($_SESSION[$siteroot] as $key => $value) {
			$_SESSION[$key] = $value;
		}
        }
        $_SESSION['KT_lastsiteroot'] = $siteroot;
}

/**
 * Put in the $_SESSION[$siteroot][$var] key the $_SESSION[$var] value ; 
 * @param string $var the name of the variable to be setted from _SESSION[$var] to _SESSION[$siteroot][$var]; 
 * @return nothing;
 */
function KT_setSessionVar($var) {
	$siteroot = md5(KT_getSiteRoot());
	if ( !isset($_SESSION[$siteroot]) ) {
		$_SESSION[$siteroot] = array();
	}
	if ( isset($_SESSION[$var]) ) {
		$_SESSION[$siteroot][$var] = $_SESSION[$var];
	}
}

/**
 * Unset the $_SESSION[$siteroot][$var] and $_SESSION[$var] ; 
 * @param string $var the name of the variable to be unseted; 
 * @return nothing;
 */
function KT_unsetSessionVar($var) {
	$siteroot = md5(KT_getSiteRoot());
	if ( isset($_SESSION[$siteroot]) ) {
		if ( isset($_SESSION[$siteroot][$var]) ) {
			unset($_SESSION[$siteroot][$var]);
		}
		if ( count($_SESSION[$siteroot]) == 0 ) {
			unset($_SESSION[$siteroot]);
		}
	}
}

/**
 * Extracts parts of a file (dirname, basename, filename, extension)
 * @param string $fileName The file name
 * @return array returns associative array containing the dirname, basename, filename, extension;
 */
function KT_pathinfo($fileName) {
	if ($fileName != "") {
		$path_info = pathinfo($fileName);
		if (!isset($path_info['extension'])) {
			$path_info['extension'] = "";
		}
		$extSize = strlen($path_info['extension']);
		if ($extSize != 0) {
			$extSize = $extSize + 1;
		}
		$path_info['filename'] = substr($path_info['basename'], 0, strlen($path_info['basename']) - $extSize);
	} else {
		$path_info = array('dirname' => '', 'basename' => '','extension' => '', 'filename' => '');
	}
	return $path_info;
}

/**
 * Transforms a path into its absolute value
 *   Path separator is always "/" and for folders, always adds the trailing "/"
 *   If folder/file does not exist, the "pathName" will be added to the current folder path
 * @param string $pathName The path to be translated
 * @param boolean $isFolder Specifies if the path is a folder or a file
 * @return string return the absolute path
 */
function KT_realpath($pathName, $isFolder = true) {
	$isAbsolute = false;
	if (strtolower(substr(PHP_OS, 0, 3)) === 'win') {
		if (substr($pathName,1,1) == ":") {
			$isAbsolute = true;
		}
	} else {
		if (substr($pathName,0,1) == "/") {
			$isAbsolute = true;
		}
	}
	if ($isAbsolute) {
		$realPath = $pathName;
	} else {
		$realPath = realpath($pathName);
		if ($realPath === false) {
			$realPath = realpath('.') . DIRECTORY_SEPARATOR;
			$realPath .= $pathName;
		}
	}
	$realPath = str_replace('\\\\', '\\', $realPath);
	$realPath = str_replace('\\', '/', $realPath);
	$realPath = str_replace('//', '/', $realPath);
	if ($isFolder) {
		if (substr($realPath, strlen($realPath)-1) != '/') {
			$realPath .= '/';
		}
	}
	$realPath = str_replace("/./", "/", $realPath);
	$realPath = str_replace("/./", "/", $realPath);
	$realPath = KT_CanonizeRelPath($realPath);
	return $realPath;
}

/**
 * Parse the string for values like {...} and return an array with all the conted from {}; 
 * @param string $string the string to be searched; 
 * @return array $replacements;
 */
function KT_getReplacementsFromMessage(&$string) {
	$replacements = array();

	if (preg_match_all('/\{([\w\d\.\s\(\)]+)\}/', $string, $matches)) {
		if (isset($matches[1]) && is_array($matches[1])) {
			$replacements = $matches[1];
		}
	}

	return $replacements;
}

/**
* Function KT_DynamicData replace all the dynamic data with their values;
* @param string $expression The expression to be evaluated
* @param object or null $tNG The tNG context in which the expression is evaluated
* @param string $escapeMethod The string escape method for the evaluated values (rawurlencode and SQL)
* @param booolean $useSavedData Weather to use the current tNG data or the saved values
* @param array $extraParams Extra expression parameters passed when for evaluation (of form $key => $value; any encounter of key will be replaced with its value)
* @return string the string with the dynamic data replaced with their values;
*/
function KT_DynamicData($expression, $tNG, $escapeMethod = '', $useSavedData = false, $extraParams = array(), $errorIfNotFound = true) {
	$PB = '{';
	$PE = '}';

    if (!is_string($expression)){
        return $expression;
    }
	
	// DynamicData functions - use this to define more functions
	KT_getInternalTimeFormat();
	$date_now = KT_convertDate(date('Y-m-d'), "yyyy-mm-dd", $GLOBALS['KT_screen_date_format']);
	$date_dt_now = KT_convertDate(date('Y-m-d H:i:s'), "yyyy-mm-dd HH:ii:ss", $GLOBALS['KT_screen_date_format'].' ' .$GLOBALS['KT_screen_time_format_internal']);
	$date_t_now = KT_convertDate(date('H:i:s'), "HH:ii:ss", $GLOBALS['KT_screen_time_format_internal']);
	$dynamicDataFunctions = array(
		'NOW()' => $date_now,
		'now()' => $date_now,
		'NOW' => $date_now,
		'now' => $date_now,
		'NOW_DT()' => $date_dt_now,
		'now_dt()' => $date_dt_now,
		'NOW_DT' => $date_dt_now,
		'now_dt' => $date_dt_now,
		'NOW_T()' => $date_t_now,
		'now_t()' => $date_t_now,
		'NOW_T' => $date_t_now,
		'now_t' => $date_t_now,
		'KT_REFERRER' => isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'',
		'kt_referrer' => isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'',
		'KT_CSV_LINE' => isset($GLOBALS['KT_CSV_LINE'])?$GLOBALS['KT_CSV_LINE']:'',
		'KT_XML_LINE' => isset($GLOBALS['KT_XML_LINE'])?$GLOBALS['KT_XML_LINE']:''
	);

	$placeholdersArr = KT_getReplacementsFromMessage($expression);

	$replacementsArr = array();

	switch ($escapeMethod) {
		case 'rawurlencode' :
			break;
		case 'expression' :
			break;
		case 'SQL' :
			if (!isset($tNG)) {
				$escapeMethod = false;
			}
			break;
		default :
			$escapeMethod = false;
			break;
	}

	if ($useSavedData !== true) {
		$useSavedData = false;
	}

	foreach ($placeholdersArr as $key => $placeholder) {
		if (array_key_exists($placeholder, $extraParams)) {
			// extra params have priority 1
			$placeholderType = 'tng_ddextra';
			$placeholderName = $placeholder;
		} else {
			// functions have priority 2
			if (array_key_exists($placeholder, $dynamicDataFunctions)) {
				$placeholderType = 'tNG_DDfunction';
				$placeholderName = $placeholder;
			}	else {
				$ptpos = strpos($placeholder, '.');
				if (!$ptpos) {
					// tng field
					if (isset($tNG)) {
						// attached to a tng, replace field with value
						$placeholderType = 'tNG_tNGfield';
						$placeholderName = $placeholder;
					} else {
						// no tng, leave as is
						$placeholderType = 'tNG_tNGfieldLater';
						$placeholderName = $placeholder;
					}
				} else {
					$placeholderType = substr($placeholder, 0, $ptpos);
					$placeholderName = substr($placeholder, $ptpos + 1);
				}
			}
		}
		$placeholder = $PB . $placeholder . $PE;
		switch (strtolower($placeholderType)) {
			case 'tng_ddfunction' :
				$replacementsArr[$placeholder] = $dynamicDataFunctions[$placeholderName];
				break;
			case 'tng_ddextra' :
				$replacementsArr[$placeholder] = $extraParams[$placeholderName];
				break;
			case 'tng_tngfield' :
				if ($useSavedData) {
					$placeholderValue = $tNG->getSavedValue($placeholderName);
				} else {
					if (isset($tNG->columns[$placeholderName]) || $placeholderName == $tNG->getPrimaryKey()) {
						$placeholderValue = $tNG->getColumnValue($placeholderName);
						$placeholderType = $tNG->getColumnType($placeholderName);
					} else {
						if ($errorIfNotFound == true) {
							die('KT_DynamicData:<br />Column ' . $placeholderName . ' is not part of the current transaction.');
						} else {
							$placeholderValue = $placeholder;
						}
					}
					if ($escapeMethod == 'SQL') {
						$placeholderValue = KT_escapeForSql($placeholderValue, $placeholderType);
					}
				}
				$replacementsArr[$placeholder] = $placeholderValue;
				break;
			case 'tng_tngfieldlater':
				break;
			case 'get':
				$myPlaceholderName = $placeholderName;
				if (isset($tNG)) {
					if (isset($tNG->multipleIdx)) {
						$myPlaceholderName .= "_".$tNG->multipleIdx;
					}
				}
				$replacementsArr[$placeholder] = KT_getRealValue("GET",$myPlaceholderName);
				if (!isset($replacementsArr[$placeholder])) {
					$replacementsArr[$placeholder] = KT_getRealValue("GET",$placeholderName);
				}
				break;
			case 'post':
				$myPlaceholderName = $placeholderName;
				if (isset($tNG)) {
					if (isset($tNG->multipleIdx)) {
						$myPlaceholderName .= "_".$tNG->multipleIdx;
					}
				}
				$replacementsArr[$placeholder] = KT_getRealValue("POST",$myPlaceholderName);
				if (!isset($replacementsArr[$placeholder])) {
					$replacementsArr[$placeholder] = KT_getRealValue("POST",$placeholderName);
				}
				break;
			case 'cookie':
				$replacementsArr[$placeholder] = KT_getRealValue("COOKIE",$placeholderName);
				break;
			case 'session':
				KT_session_start();
				$replacementsArr[$placeholder] = KT_getRealValue("SESSION",$placeholderName);
				break;
			case 'globals':
				$replacementsArr[$placeholder] = KT_getRealValue("GLOBALS",$placeholderName);
				break;
			case 'request':
				$replacementsArr[$placeholder] = KT_getRealValue("GLOBALS",$placeholderName);
				break;
			case 'server':
				$replacementsArr[$placeholder] = KT_getRealValue("SERVER",$placeholderName);
				break;
			case 'application':
				// CF only
				break;
			case 'csv':
				$replacementsArr[$placeholder] = KT_getRealValue("CSV",$placeholderName);
				break;	
			default :
				// recordset
				if (isset($GLOBALS[$placeholderType])) {
					$rs = $GLOBALS[$placeholderType];
					if (is_resource($rs)) {
						$placeholderValue = $GLOBALS["row_".$placeholderType][$placeholderName];
					} elseif (is_object($rs)) {
						$placeholderValue = $rs->Fields($placeholderName);
					} else {
						break;
					}
				} else {
					$placeholderValue = $placeholder;
				}
				$replacementsArr[$placeholder] = $placeholderValue;
				break;
		}
	}

	reset($replacementsArr);
	if ($escapeMethod == 'rawurlencode') {
		if (
			!array_key_exists ("{kt_login_redirect}", $replacementsArr) && 
			!array_key_exists ("{kt_referrer}", $replacementsArr) && 
			!array_key_exists ("{KT_REFERRER}", $replacementsArr)
			) {
			$replacementsArr = array_map($escapeMethod, $replacementsArr);
		}
	} elseif ($escapeMethod == 'expression') {
		$replacementsArr = array_map('KT_escapeExpression', $replacementsArr);
	}
	$newexpression = str_replace(array_keys($replacementsArr), array_values($replacementsArr), $expression);
	/*if ($escapeMethod == 'expression') {
		echo $newexpression."\n<br/>\n";
	}*/
	return $newexpression;
}

/**
 * Wrapper for stripslashes; it is used as callbackfunction; 
 * @param string $value the string to be searched; 
 * @param string $key the string to be searched; 
 * @return nothing;
 */
function KT_stripslashes($value, $key) {
	$value = stripslashes($value);
}

/**
 * Compiles a method and a reference into a value
 * Ex: method=GET and reference=test, return value=$_GET['test']
 * @param string $method The method (GET, POST, etc)
 * @param string $reference The reference (variable name)
 * @return object unknown The compiled value
 *         the return has stripped slashes if necessary
 * @access public
 */
function KT_getRealValue($method, $reference) {
	$needStrip = false;
	$ret = null;
	switch($method) {
		case 'GET':
			if (isset($_GET[$reference])) {
				$ret = $_GET[$reference];
			}
			$needStrip = true;
			break;
		case 'POST':
			if (isset($_POST[$reference])) {
				$ret = $_POST[$reference];
			}
			$needStrip = true;
			break;
		case 'COOKIE':
			if (isset($_COOKIE[$reference])) {
				$ret = $_COOKIE[$reference];
			}
			$needStrip = true;
			break;
		case 'SESSION':
			if (isset($_SESSION[$reference])) {
				$ret = $_SESSION[$reference];
			}
			break;
		case 'GLOBALS':
			if (isset($GLOBALS[$reference])) {
				$ret = $GLOBALS[$reference];
			}
			break;
		case 'SERVER':
			if (isset($_SERVER[$reference])) {
				$ret = $_SERVER[$reference];
			}
			break;
		case 'FILES':
			if (isset($_FILES[$reference])) {
				$ret = @$_FILES[$reference]['name'];
			}
			break;
		case 'VALUE':
			$ret = $reference;
			break;
		case 'CURRVAL':
			$ret = null;
			break;
		case 'CSV':
			if (isset($GLOBALS['KT_CSV'][$reference])) {
				$ret = $GLOBALS['KT_CSV'][$reference];
			}
			break;
		case 'XML':
			if (isset($GLOBALS['KT_XML'][$reference])) {
				$ret = $GLOBALS['KT_XML'][$reference];
			}
			break;
		default:
			die('KT_getRealValue:<br />Unknown method: '.$method.'.');
			break;
	}
	if ($needStrip && !is_null($ret)) {
		if (get_magic_quotes_gpc() || (isset($GLOBALS['KT_serverModel']) && $GLOBALS['KT_serverModel'] == 'adodb')) {
			if (is_array($ret)) {
				array_walk($ret, 'KT_stripslashes');
			} else {
				$ret = stripslashes($ret);
			}
		}
	}
	return $ret;
}



/**
 * Converts a date/time/datetime from one format to another
 * @param string $date The date
 * @param string $inFmt The input format (the format of the input date)
 * @param string $outFmt The output format (the format of the output date)
 * @return string the new date
 */
function KT_convertDate($date, $inFmt, $outFmt) {
	if (($inFmt == '') || ($outFmt == '') || ($inFmt == $outFmt)) {
		return $date;
	}
	
	if (!isset($date) || $date == '') {
		return;
	}
	
	if (strpos($inFmt, '%') !== false) {
		$inFmt = KT_format2newDF($inFmt);
	}
	
	if (strpos($outFmt, '%') !== false) {
		$outFmt = KT_format2newDF($outFmt);
	}
	
	$inFmtRule = KT_format2rule($inFmt);
	$outFmtRule = KT_format2rule($outFmt);
	$dateArr = KT_applyDate2rule($date, $inFmtRule);
	$outRule = KT_format2outRule($outFmt);
	$outdate = KT_applyOutRule2date($dateArr, $outFmtRule, $outRule);

	return $outdate;
}

/**
 * Convert a date format from an old type date format %Y-%m-%d into new ones; 
 * @param string $dateformat the string to be parsed (ex. %Y-%m-%d ); 
 * @return string $dateformat (ex. yyyy-mm-dd);
 */
function KT_format2newDF($dateformat) {
	$dateformat = str_replace('%Y', 'yyyy', $dateformat);
	$dateformat = str_replace('%m', 'mm', $dateformat);
	$dateformat = str_replace('%d', 'dd', $dateformat);
	$dateformat = str_replace('%H', 'HH', $dateformat);
	$dateformat = str_replace('%M', 'ii', $dateformat);
	$dateformat = str_replace('%S', 'ss', $dateformat);
	return $dateformat;
}

/**
 * Splits a date format into a chunked representation
 * @param string $format The format to be precessed
 * @return array the format in a chunked form (with chunks position and length)
 */
function KT_format2rule($format) {
	$rule = array();

	$rulePieces = preg_split('/([-\/\[\]\(\)\s\*\|\+\.:=,])/', $format);
	$count = count($rulePieces);

	for ($i=0; $i<$count; $i++) {
		$rulePiece = $rulePieces[$i];
		switch ($rulePiece) {
			case 'yyyy':
			case 'yy':
			case 'y':
				$rule['y']['piece'] = $i;
				$rule['y']['len'] = strlen($rulePiece);
				break;
			case 'mm':
			case 'm':
				$rule['m']['piece'] = $i;
				$rule['m']['len'] = strlen($rulePiece);
				break;
			case 'dd':
			case 'd':
				$rule['d']['piece'] = $i;
				$rule['d']['len'] = strlen($rulePiece);
				break;
			case 'HH':
			case 'H':
				$rule['H']['piece'] = $i;
				$rule['H']['len'] = strlen($rulePiece);
				break;
			case 'hh':
			case 'h':
				$rule['h']['piece'] = $i;
				$rule['h']['len'] = strlen($rulePiece);
				break;
			case 'ii':
			case 'i':
				$rule['i']['piece'] = $i;
				$rule['i']['len'] = strlen($rulePiece);
				break;
			case 'ss':
			case 's':
				$rule['s']['piece'] = $i;
				$rule['s']['len'] = strlen($rulePiece);
				break;
			case 'tt':
			case 't':
				$rule['t']['piece'] = $i;
				$rule['t']['len'] = strlen($rulePiece);
				break;
		}
	}
	return $rule;
}

/**
 * Splits a date into a chunked representation;
 * @param string $date The date to be precessed;
 * @param array $rule Associative array containing the date chunks order ('y'=> 1, 'm' => 3, etc);
 * @return array the date in a chunked form, containig yyyy, mm, dd, HH, ii and ss;
 */
function KT_applyDate2rule($date, $rule) {
	$dateArr = array();

	$dateArr['y'] = '';
	$dateArr['m'] = '';
	$dateArr['d'] = '';
	$dateArr['H'] = '00';
	$dateArr['i'] = '00';
	$dateArr['s'] = '00';

	$datePieces = preg_split('/([-\/\[\]\(\)\s\*\|\+\.:=,])/', $date, -1, PREG_SPLIT_NO_EMPTY);
	if (is_array($datePieces) && count($datePieces)>0) {
		foreach ($rule as $ruleKey => $ruleValue) {
			$index = $ruleValue['piece'];
			if (isset($datePieces[$index])) {
				$dateArr[$ruleKey] = $datePieces[$index];
			}
		}
	}

	$ruleKeys = array_keys($rule);
	if (in_array('h', $ruleKeys) && isset($dateArr['h'])) {
		$dateArr['H'] = $dateArr['h'];
		unset($dateArr['h']);
	}
	if (in_array('t', $ruleKeys)) {
		$value = isset($dateArr['t'])?$dateArr['t']:'A';
		if (strtoupper(substr($value, 0, 1)) == 'P') {
			if ($dateArr['H'] < 12) {
				$dateArr['H'] = $dateArr['H'] + 12;
			}
		} else {
			if ($dateArr['H'] == 12) {
				$dateArr['H'] = 0;
			}
		}
		unset($dateArr['t']);
	}

	foreach (array('y', 'm', 'd', 'H', 'i', 's') as $key => $piece) {
		if (strlen($dateArr[$piece]) == 1) {
			$dateArr[$piece] = '0' . $dateArr[$piece];
		}
	}

	if (strlen($dateArr['y']) == 2) {
		if ($dateArr['y'] < 70) {
			$dateArr['y'] = '20' . $dateArr['y'];
		} else {
			$dateArr['y'] = '19' . $dateArr['y'];
		}
	}

	return $dateArr;
}

/**
 * Removes extra chars from a date format, in order to obtain a parsable definition;
 * @param string $format The format to be stripped (ex. yyyy-mm-dd);
 * @return string $format (ex. y-m-d);
 */
function KT_format2outRule($format) {
	$format = str_replace('yyyy', 'y', $format);
	$format = str_replace('yy', 'y', $format);
	$format = str_replace('mm', 'm', $format);
	$format = str_replace('dd', 'd', $format);
	
	$format = str_replace('hh', 'h', $format);
	$format = str_replace('HH', 'H', $format);
	$format = str_replace('ii', 'i', $format);
	$format = str_replace('ss', 's', $format);
	$format = str_replace('tt', 't', $format);

	return $format;
}

/**
 * Processes a date array in a usable format;
 * @param array $dateArr Associative array containing date chunks ('y'=>'2004', 'm'=>'5', etc.);
 * @param array $formatRule Associative array containing the output date formatting rules ('y'=> 2 chars, 'm' => 1 char, etc);
 * @param string $outStringRule Defines the output date format;
 * @return string the date in the $outStringRule format;
 */
function KT_applyOutRule2date(&$dateArr, &$formatRule, &$outStringRule) {
	$date = '';

	$dateArrKeys = array_keys($dateArr);
	$formatRuleKeys = array_keys($formatRule);

	$preparedKeys = array_diff($formatRuleKeys, $dateArr);
	if (count($preparedKeys) > 0) {
		if (in_array('h', $preparedKeys)) {
			$value = $dateArr['H'];
			$dateArr['h'] = $value;
			if ($value == 0) {
				$dateArr['h'] = 12;
			}
			$dateArr['t'] = 'AM';
			if ($value > 12 && $value < 24) {
				$dateArr['h'] = $value - 12;
				$dateArr['t'] = 'PM';
			}
		}
		if (in_array('t', $preparedKeys)) {
			$value = $dateArr['H'];
			$dateArr['t'] = 'AM';
			if ($value > 11) {
				$dateArr['t'] = 'PM';
			}
		}
	}


	$formatRuleKeys = array_keys($formatRule);
	$count = count($formatRuleKeys);
	for ($i=0; $i<$count; $i++) {
		$key = $formatRuleKeys[$i];
		$len = $formatRule[$key]['len'];
		$value = $dateArr[$key];

		// convert from less digits to more
		// only for HH
		if (strlen($value) < $len) {
			if ($key == 'H') {
				$dateArr[$key] = '0' . $value;
			}
		}

		// convert from more digits to less
		if (strlen($value) > $len) {
			switch ($key) {
				case 'm':
				case 'd':
				case 'i':
				case 'h':
				case 'H':
				case 's':
					if (substr($value, 0, 1) == '0') {
						$dateArr[$key] = substr($value, 1);
					}
					break;
				case 'y':
					if (strlen($value) == 4) {
						$value = substr($value, 2);
					}
					if ($len == 1 && substr($value, 0, 1) == '0') {
						$value = substr($value, 1);
					}
					$dateArr[$key] = $value;
					break;
				case 't':
					$dateArr[$key] = substr($value, 0, 1);
					break;
			}
		}
	}

	$date = str_replace(array_keys($dateArr), array_values($dateArr), $outStringRule);
	$date = trim(preg_replace('/[-\/\[\]\(\)\s\*\|\+\.:=,]{2,}/', '', $date));

	return $date;
}

/**
 * Validates a date array
 * @param $dateArr the date array
 * @return boolean the date is valid or not
 */
function KT_isValidDate(&$dateArr, $fullDateTime = true) {
	if ($fullDateTime == true) {
		if (!isset($dateArr['y'])) {
			return false;
		}
		if (isset($dateArr['m'])) {
			if ($dateArr['m'] < 1 || $dateArr['m'] > 12) {
				return false;
			}
		} else {
			return false;
		}
	
		$maxday = KT_getDaysOfMonth($dateArr['m'], $dateArr['y']);
	
		if (isset($dateArr['d'])) {
			if ($dateArr['d'] < 1 || $dateArr['d'] > $maxday) {
				return false;
			}
		} else {
			return false;
		}
	}	
	
	if (isset($dateArr['H'])) {
		if ($dateArr['H'] < 0 || $dateArr['H'] > 23) {
			return false;
		}
	} else {
		return false;
	}
	if (isset($dateArr['i'])) {
		if ($dateArr['i'] < 0 || $dateArr['i'] > 59) {
			return false;
		}
	} else {
		return false;
	}
	if (isset($dateArr['s'])) {
		if ($dateArr['s'] < 0 || $dateArr['s'] > 59) {
			return false;
		}
	} else {
		return false;
	}

	return true;
}

/**
 * Return the last day of the given month for the given year; 
 * @param string $month the month in a numeric format (ex. 01 ); 
 * @param int $year the value of the year (ex. 2004) 
 * @return string the last day of the month (ex. 31 );
 */
function KT_getDaysOfMonth($month, $year) {
	$maxday = 1;
	
	switch (intval($month)) {
		case 1:
		case 3:
		case 5:
		case 7:
		case 8:
		case 10:
		case 12:
			$maxday = 31;
			break;
		case 4:
		case 6:
		case 9:
		case 11:
			$maxday = 30;
			break;
		case 2:
			$maxday = 28;
			if ((($year % 4 == 0) && ($year % 100 != 0)) || ($year % 400 == 0)) {
				$maxday = 29;
			}
			break;
	}
	return $maxday;
}

/**
 * Compares 2 date arrays
 * @param array $dateArr1
 * @param array $dateArr2
 * @return integer -1, 1 or 0
 */
function KT_compareDates(&$dateArr1, &$dateArr2) {
	$time1 = $dateArr1['y'] . $dateArr1['m'] . $dateArr1['d'] . $dateArr1['H'] . $dateArr1['i'] . $dateArr1['s'];
	$time2 = $dateArr2['y'] . $dateArr2['m'] . $dateArr2['d'] . $dateArr2['H'] . $dateArr2['i'] . $dateArr2['s'];

	if (floatval($time1) > floatval($time2)) {
		return -1;
	}
	if (floatval($time1) < floatval($time2)) {
		return 1;
	}
	return 0;
}

/**
 * Strips empty values from time expressions
 * @param string $date - datetime expression
 * @return new datetime without 0's
 */
function KT_stripTime($date) {
	if ( strstr($date, ' ') && strstr($date, ':') && (strpos($date, ' ') < strpos($date, ':')) ) {
		$dateArr = explode(' ', $date);
		$timeArr = explode(':', $dateArr[1]);
		for ($i = count($timeArr) - 1; $i >=0; $i--) {
			if ($timeArr[$i] != '0' && $timeArr[$i] != '00') {
				break;
			} else {
				unset($timeArr[$i]);
			}
		}
		// remove time when [time format is hh:mm:ss tt] and [time value is 12 A(M)]
		if ( count($timeArr) == 1 && trim($timeArr[0]) == '12'  &&  count($dateArr) == 3 && (strtolower(trim($dateArr[2])) == 'a' || strtolower(trim($dateArr[2])) == 'am')  ) {
			unset($timeArr[0]);
			unset($dateArr[2]);		
		}
		$dateArr[1] = implode(':', $timeArr);
		if ($dateArr[1] == '') {
			unset($dateArr[1]);
		}
		$date = implode(' ', $dateArr);
	}
	return $date;
}

/**
 * Expands time expressions to full screen format
 * @param string $date - datetime expression
 * @return new datetime with full time part
 */
function KT_expandTime($date) {
	$dateArr = explode(' ', $date);
	$timeArr = explode(':', $dateArr[1]);
	
	$hour = $timeArr[0];
	$min = '00';
	$sec = '00';
	if ( isset($timeArr[1]) ) {
		$min = $timeArr[1];
	}
	if ( isset($timeArr[2]) ) {
		$sec = $timeArr[2];
	}
	if ( isset($dateArr[2]) ) {
		if ( preg_match('/p/i', $dateArr[2]) && $hour < 12 ) {
			$hour += 12;
		}
		if ( preg_match('/a/i', $dateArr[2]) && $hour == 12 ) {
			$hour = '00';
		}
	}
	
	$date = $dateArr[0] . ' ' . KT_convertDate($hour . ':' . $min . ':' . $sec, 'HH:ii:ss', $GLOBALS['KT_screen_time_format_internal']);
	return $date;
}


/**
 * Wrapper for KT_convertDate
 * For internal use
 * @param string $date The date in input format
 * @param string $toScreen if $toScreen == 'toscreen' the date will be outputed in screen format; otherwise in database format
 * @return string the date in the new format
 */
function KT_convertDateCall($date, $toScreen = '') {
	if (!isset($GLOBALS['KT_screen_time_format_internal'])) {
		KT_getInternalTimeFormat();
	}

	if (!preg_match('/^([\d-\/\[\]\(\)\s\*\|\+\.:=,]|a|p|am|pm)+$/i', $date)) {
		return $date;
	}
	$date = str_replace('1900-01-01 ', '', $date);
	if (preg_match('/^\d+$/',$date)) {
		if (strlen(trim($date)) == 14) {
			$newDate = substr($date,0,4).'-'.substr($date,4,2).'-'.substr($date,6,2);
			$newDate .= ' '.substr($date,8,2).':'.substr($date,10,2).':'.substr($date,12,2);
			$date = $newDate;
			$from = 'yyyy-mm-dd HH:ii:ss';
			$to = $GLOBALS['KT_screen_date_format'] . ' ' . $GLOBALS['KT_screen_time_format_internal'];
		} else {
			return $date;
		}
	}
	
	if ( !preg_match('/^(\d+[-\/\[\]\(\)\s\*\|\+\.=,]\d+[-\/\[\]\(\)\s\*\|\+\.=,]\d+)+/i', $date) && 
		!preg_match('/^\d+:\d+(:\d+|\s+a|\s+p|\s+am|\s+pm)/i', $date) 
		) {
		return $date;
	}
	
	if ( $toScreen == '' && preg_match('/^\d+[-\/\[\]\(\)\s\*\|\+\.=,]\d+[-\/\[\]\(\)\s\*\|\+\.=,]\d+\s+\d+/i', $date) ) {
		$date = KT_expandTime($date);
	}
	
	if ( strstr($date, ' ') && strstr($date, ':') && (strpos($date, ' ') < strpos($date, ':')) ) {
		$from = $GLOBALS['KT_screen_date_format'] . ' ' . $GLOBALS['KT_screen_time_format_internal'];
		$to = $GLOBALS['KT_db_date_format'] . ' ' . $GLOBALS['KT_db_time_format_internal'];
	} elseif (strstr($date, ':')) {
		$from = $GLOBALS['KT_screen_time_format_internal'];
		$to = $GLOBALS['KT_db_time_format_internal'];
	} else {
		$from = $GLOBALS['KT_screen_date_format'];
		$to = $GLOBALS['KT_db_date_format'];
	}
	if ($toScreen == 'toscreen') {
		$tmp = $from;
		$from = $to;
		$to = $tmp;
	}
	$date = KT_convertDate($date, $from, $to);
	if ($toScreen == 'toscreen') {
		$date = KT_stripTime($date);
	}
	return $date;
}

/**
 * Converts mm to ii in time formats and sets globals
 * For internal use
 * maybe must be called from tng?
 * @return nothing
 */
function KT_getInternalTimeFormat() {
	$GLOBALS['KT_db_date_format'] = preg_replace("/[yY]/","y",$GLOBALS['KT_db_date_format']);
	$GLOBALS['KT_db_date_format'] = preg_replace("/[mM]/","m",$GLOBALS['KT_db_date_format']);
	$GLOBALS['KT_db_date_format'] = preg_replace("/[dD]/","d",$GLOBALS['KT_db_date_format']);
	$GLOBALS['KT_screen_date_format'] = preg_replace("/[yY]/","y",$GLOBALS['KT_screen_date_format']);
	$GLOBALS['KT_screen_date_format'] = preg_replace("/[mM]/","m",$GLOBALS['KT_screen_date_format']);
	$GLOBALS['KT_screen_date_format'] = preg_replace("/[dD]/","d",$GLOBALS['KT_screen_date_format']);
	
	$GLOBALS['KT_screen_time_format_internal'] = str_replace('m', 'i', $GLOBALS['KT_screen_time_format']);
	$GLOBALS['KT_db_time_format_internal'] = str_replace('m', 'i', $GLOBALS['KT_db_time_format']);
}

/**
 * Converts a date/time/datetime from screen format to database format
 * For internal use
 * @param string $date The date in screen format
 * returns string the date in database format
 */
function KT_formatDate2DB($date) {
	return KT_convertDateCall($date);
}

/**
 * Converts a date/time/datetime from database format to screen format
 * Used for date display
 * @param string $date The date in database format
 * @return string the date in screen format
 */
function KT_formatDate($date) {
	return KT_convertDateCall($date, 'toscreen');
}

/**
 * Escape the expression for \ and '; 
 * @param string $expr the expression to be escaped; 
 * @return string escaped expression if not null or null;
 */
function KT_escapeExpression($expr) {
	if ($expr !== null) {
		$expr = str_replace(array('\\','\''),array('\\\\','\\\''),$expr);
		$expr = "'".$expr."'";
	} else {
		$expr = "null";
	}
	return $expr;
}

/**
 * Return a strip string (without html tags and no longer than $maxchars); 
 * @param string $value the string wich will be parsed; 
 * @param int $maxChars -1 or bigger then 0; 
 * @return string the last day of the month (ex. 31 );
 */
function KT_FormatForList($value, $maxChars) {
	$isBigger = false;
	$value = preg_replace("/<head[^>]*>[\w\W]*?<\/head>[\n\r]*/i", '', $value);
	$value = preg_replace("/<link[^>]*>[\n\r]*/i", '', $value);
	$value = preg_replace("/<script[^>]*>[\w\W]*?<\/script>[\n\r]*/i", '', $value);
	$value = preg_replace("/<style[^>]*>[\w\W]*?<\/style>[\n\r]*/i", '', $value);
	$value = strip_tags($value);
	if ($maxChars != -1) {
		if (strlen(trim($value)) > $maxChars) {
			$value = substr($value, 0, $maxChars);
			$isBigger = true;
		}
	}
	$value = str_replace(array("<", ">"), array("&lt;", "&gt;"), $value);
	if ($value == "") {
		$value = "&nbsp;";
	}
	if ($isBigger) {
		$value .= "...";
	}
	return $value;
}


/**
 * Set the database type and server model in $GLOBALS and return the database type; 
 * @param object $connection the connection object; 
 * @return string the type of the database;
 */
function KT_setDbType($connection) {
	if (!isset($GLOBALS['KT_dataDbType'])) {
		$cname = strtolower($connection->databaseType);
		$GLOBALS['KT_dataDbType'] = $cname;
	}
	if (!isset($GLOBALS['KT_serverModel'])) {
		$GLOBALS['KT_serverModel'] = 'adodb';
		if (isset($connection->servermodel) && $connection->servermodel == 'mysql') {
			$GLOBALS['KT_serverModel'] = 'mysql';
		}
	}
	return $GLOBALS['KT_dataDbType'];
}

/**
 * Escapes a value against a specific type to be used in the transaction SQL
 * Ex: value=ab'b and type=STRING, result=ab\'b (escapes slashes)
 * @param object unknown $colValue The value to prepare
 * @param string $colType The type (STRING_TYPE, NUMERIC_TYPE, etc)
 * @param bool $forFakeRs if it should return values for an SQL query, or for an fake recordset
 * @return object unknown The escaped value
 * @access public
 */
function KT_escapeForSql($colValue, $colType, $forFakeRs = false) {
	$type2empty = array(
		'STRING_TYPE'=>'null',
		'NUMERIC_TYPE' => 'null',
		'DOUBLE_TYPE' => 'null',
		'DATE_TYPE' => 'null',
		'DATE_ACCESS_TYPE'=>'null',
		'FILE_TYPE'=>'null',
		'CHECKBOX_YN_TYPE'=>"'N'",
		'CHECKBOX_1_0_TYPE'=>'0',
		'CHECKBOX_-1_0_TYPE'=>'0',
		'CHECKBOX_TF_TYPE'=>"'f'"
	);
	$type2quote = array(
		'STRING_TYPE'=>'\'',
		'NUMERIC_TYPE' => '',
		'DOUBLE_TYPE' => '',
		'DATE_TYPE' => '\'',
		'DATE_ACCESS_TYPE'=>'#',
		'FILE_TYPE'=>'\'',
		'CHECKBOX_YN_TYPE'=>'\'',
		'CHECKBOX_1_0_TYPE'=>'',
		'CHECKBOX_-1_0_TYPE'=>'',
		'CHECKBOX_TF_TYPE'=>'\'',
	);
	// fake rs empty values
	if ($forFakeRs) {
		$type2empty = array(
			'STRING_TYPE'=>'',
			'NUMERIC_TYPE' => '',
			'DOUBLE_TYPE' => '',
			'DATE_TYPE' => '',
			'DATE_ACCESS_TYPE'=>'',
			'FILE_TYPE'=>'',
			'CHECKBOX_YN_TYPE'=>'N',
			'CHECKBOX_1_0_TYPE'=>'0',
			'CHECKBOX_-1_0_TYPE'=>'0',
			'CHECKBOX_TF_TYPE'=>'f'
		);
	}
	
	if (is_null($colValue) || $colValue === '') {
		$tmValue = $type2empty[$colType];
	} else {
		if ($colType == "NUMERIC_TYPE") {
			$colValue = intval($colValue);
		} elseif ($colType == "DOUBLE_TYPE") {
			$colValue = str_replace(',', '.', $colValue);
			$colValue = floatval($colValue);
		}
		if ($forFakeRs) {
			$tmValue = $colValue;
		} else {
			$quote = $type2quote[$colType];
			$tmValue = $quote . str_replace(array("'", "\\"), array("''", "\\\\"), $colValue) . $quote;
		}
	}
	return $tmValue;
}

/**
 * Escapes a value of a field name to be used in the transaction SQL
 * Ex: First Name gets translated into `First Name`
 * @param string $colName The DataBase field name
 * @return string The escaped field name
 * @access public
 */
function KT_escapeFieldName($colName) {
	return $colName;
	$startq = '"';
	$endq = '"';

	// if colname contains ., return as is
	if (preg_match('/\./', $colName)) {
		return $colName;
	}

	if (isset($GLOBALS['KT_dataDbType'])) {
		if (strpos($GLOBALS['KT_dataDbType'],'mysql') !== FALSE) {
			$startq = '`';
			$endq = '`';
		}
		
		// if name is of the form `name`, quote it
		if (preg_match('/^`(.+)`$/', $colName, $matches)) {
			return $startq . $matches[1] . $endq;
		}
		
		return $startq . $colName . $endq;
	}
	return $startq . $colName . $endq;
}

/**
 * Add the URL to the $_SESSION['KT_backArr'] array if this array not exist;
 * If the array $_SESSION['KT_backArr'] exist check if the last entry is identical with the argument; 
 * If this 2 url are identical the old one is replaced with the new one, if not the new one is append it.
 * @param string $newUrl the url to be add to the array; 
 * @return nothing;
 */
function KT_SessionKtBack($newUrl) {
	KT_session_start();
	$newUrl = KT_addReplaceParam($newUrl, 'KT_ajax_request');
	if (!isset($_SESSION['KT_backArr'])) {
		$_SESSION['KT_backArr'] = array();
		array_push($_SESSION['KT_backArr'],$newUrl);
	} elseif (count($_SESSION['KT_backArr'])>0) {
		$oldUrl = array_pop($_SESSION['KT_backArr']);
		
		$toldUrl = KT_addReplaceParam($oldUrl, '/^pageNum_.*$/i');
		$toldUrl = KT_addReplaceParam($toldUrl, '/^totalRows_.*$/i');
		$toldUrl = KT_addReplaceParam($toldUrl, '/^sorter_.*$/i');
		$toldUrl = KT_addReplaceParam($toldUrl, '/^show_all_.*$/i');
		$toldUrl = KT_addReplaceParam($toldUrl, '/^show_filter_.*$/i');
		$toldUrl = KT_addReplaceParam($toldUrl, '/^reset_filter_.*$/i');
		$toldUrl = KT_addReplaceParam($toldUrl, 'isFlash');
		$toldUrl = KT_addReplaceParam($toldUrl, session_name());

		$tnewUrl = KT_addReplaceParam($newUrl, '/^pageNum_.*$/i');
		$tnewUrl = KT_addReplaceParam($tnewUrl, '/^totalRows_.*$/i');
		$tnewUrl = KT_addReplaceParam($tnewUrl, '/^sorter_.*$/i');
		$tnewUrl = KT_addReplaceParam($tnewUrl, '/^show_all_.*$/i');
		$tnewUrl = KT_addReplaceParam($tnewUrl, '/^show_filter_.*$/i');
		$tnewUrl = KT_addReplaceParam($tnewUrl, '/^reset_filter_.*$/i');
		$tnewUrl = KT_addReplaceParam($tnewUrl, 'isFlash');
		$tnewUrl = KT_addReplaceParam($tnewUrl, session_name());
		
		
		if ($tnewUrl != $toldUrl) {
			array_push($_SESSION['KT_backArr'],$oldUrl);
		}
		array_push($_SESSION['KT_backArr'],$newUrl);
	} else {
		array_push($_SESSION['KT_backArr'],$newUrl);
	}
}

/**
 * Set the the permissions for folder/file setted in GLOBALS['KT_folder_mode']/GLOBALS['KT_file_mode']; 
 * @fileName string $fileName the path; 
 * @isFolder boolean $isFolder;
 * @return boolean true is ;
 */
function KT_setFilePermissions($fileName, $isFolder = false) {
	$ret = false;
	$tmp_perms = "";
	if ($isFolder == true && isset($GLOBALS['KT_folder_mode'])) {
		$tmp_perms = $GLOBALS['KT_folder_mode'];
	} elseif (isset($GLOBALS['KT_file_mode'])){
		$tmp_perms = $GLOBALS['KT_file_mode'];
	}
	if ($tmp_perms != "") {
		if ($fileName != '' && file_exists($fileName)) {
			@clearstatcache();
			$old_perms = @fileperms($fileName);
			if ($old_perms !== false) {
				$old_perms = decoct($old_perms);
				$old_perms = substr($old_perms,-3,3);
				$old_perms = octdec($old_perms);
				$new_perms = $old_perms | octdec($tmp_perms);
				$ret = @chmod($fileName, $new_perms);
			} else {
				$ret = false;
			}
		}
	}
	return $ret;
}

/**
 * return the type of request this page has been used
 * @return boolean true if the request is and Ajax Request
 */
function KT_is_ajax_request() {
	return isset($_GET['KT_ajax_request']);
}

/**
 * Leave just ASCII chars (alpha-numeric) and _ . , ; () [] 
 * @param string string to be parsed;
 * @param string if equal folder let /;
 * @return string;
 * @access public
 */
function KT_replaceSpecialChars($text, $what) {
	if ($what == 'folder') {
		return preg_replace("/[^\/0-9a-z\.,;\-_\(\)\[\]\s]/i", "_", $text);
	} else {
		return preg_replace("/[^0-9a-z\.,;\-_\(\)\[\]\s]/i", "_", $text);
	}
}

/**
 * overwrite the Pragma: no-cache header which might have been sent by the server
 * To mark a response as "never expires," an origin server sends an Expires date 
 * @param int number of seconds;
 * @return nothing;
 * @access public
 */
function KT_sendExpireHeader($seconds) {
	//overwrite the Pragma: no-cache header which might have been sent by the server
	header('Pragma: cache', true);

	//	To mark a response as "never expires," an origin server sends an Expires date 
	//approximately one year from the time the response is sent.
	//	HTTP/1.1 servers SHOULD NOT send Expires dates more than one year in the future.
	//	the Date format is: Fri, 16 Jun 2006 10:09:26 GMT
	header('Last-Modified: '.gmdate('D, d M Y H:i:s \G\M\T', time() - $seconds), true);
	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + $seconds), true);

	//Expires header is ignored by HTTP/1.1-compliant clients
	header('Cache-Control: max-age=' . $seconds, true);
}

/**
 * Remove denied tags.
 * @param string text to be cleaned
 * @param string denied tags
 * @param string allowed tags
 * @return cleaned text;
 * @access public
 */
function KT_cleanContent($text, $denied, $allowed) {
	$arrDenied = explode(',', trim($denied));
	$arrAllowed = explode(',', trim($allowed));
	array_walk($arrDenied, 'trim');
	array_walk($arrAllowed, 'trim');
	
	if (trim($denied)!='' && count($arrDenied) > 0) {
		preg_match_all('/<(\w+)[^>]*>/', $text, $contentTags);
		if (isset($contentTags[1])) {
			foreach($contentTags[1] as $k => $tag) {
				if (in_array(strtolower($tag), $arrDenied)) {
					$text = preg_replace('/<\/?' . $tag . '[^>]*>/i', '', $text);
				}
			}
		}	
	} else if (trim($allowed) && count($arrAllowed) > 0) {
		$allowed_tags_list = '<' . implode('>,<', $arrAllowed) . '>';
		$text = strip_tags($text, $allowed_tags_list);		
	}	
	// remove script;
	if (in_array('script', $arrDenied) || (count($arrAllowed)>0 && !in_array('script', $arrAllowed))) {
		while(preg_match_all("/<[^\/][\w\W]*?((on[a-z]+\s*=\s*\"[\w\W]*?\")|(on[a-z]+\s*=\s*'[\w\W]*?')|(on[a-z]+\s*=\s*[^\s]*))[\w\W]*?>/ims", $text, $m)) {
			for ($i=0; $i<count($m[0]); $i++) {
				$re = str_replace($m[1][$i], '', $m[0][$i]);
				$text = str_replace($m[0][$i], $re, $text);
			}
		}
		if (preg_match_all("/<[^>]+>/ims", $text, $m)) {
			for ($i=0; $i<count($m[0]); $i++) {
				while (preg_match("/(\W\w+\s*=\s*\"([^\"]*j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:[^\"]*)\"+)|(\W\w+\s*=\s*'([^']*j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:[^']*)'+)|(\W\w+\s*=\s*j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*[^\s>]*)/ims", $m[0][$i], $mm)) {
					$re = str_replace($mm[count($m)-1], '', $m[0][$i]);
					$text = str_replace($m[0][$i], $re, $text);
					$m[0][$i] = $re;
				}				
			}
		}
		// style
		if (preg_match_all("/<\s*style[\w\W]*>([\s\S]*)<\/style>/ims", $text, $m)) {
			for ($i=0; $i<count($m[0]); $i++) {
				if (preg_match_all("/:\s*(expression)\s*\(/ims", $m[0][$i], $mm)) {
					$re = str_replace($mm[1], '', $m[0][$i]);
					$text = str_replace($m[0][$i], $re, $text);
				}
			}
		}
		//inline style
		if (preg_match_all("/<(.*)style\s*=[^>]+>/ims", $text, $m)) {
			for ($i=0; $i<count($m[0]); $i++) {
				if (preg_match_all("/:\s*(expression)\s*\(/ims", $m[0][$i], $mm)) {
					$re = str_replace($mm[1], '', $m[0][$i]);
					$text = str_replace($m[0][$i], $re, $text);
				}
			}
		}
	}

	return $text;
}
?>