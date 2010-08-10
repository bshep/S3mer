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
* Create the image used in captcha
* Only for PRO version	 
*/
class KT_CaptchaImage {
	
	/**
	* @param string name to use to store in session the captcha string;
	*/
	var $name;
	/**
	* @param string content of the captcha
	*/
	var $text;
	
	/**
	* @param string filename used to saved the image
	*/
	var $filename;
	/**
	* @param string lib to use to generate the image
	*/
	var $lib;
	
	/**
	* Constructor. 
	* @param string name to use to store in session the captcha string;
	* @return nothing
	*/
	function KT_CaptchaImage($name) {
		$this->name = 'KT_captcha_'.$name;
		$this->text = $this->getRandomText();
		$this->filename = substr(md5(uniqid(rand(),true)), 0, 8).'.png';
		$this->lib = $GLOBALS['KT_prefered_image_lib'];
	}
	
	/**
	* main class method; Create the image and return the url to the image file;
	* @param string relative path to site root
	* @return mixt image url or error
	*/
	function getImageURL($relpath) {
		$this->garbageCollector();
		// check write permissions on captcha folder
		$fld = new KT_folder();
		$fld->createFolder(KT_CAPTCHA_TEMP_FOLDER);
		if (!$fld->checkRights(KT_CAPTCHA_TEMP_FOLDER, 'write')) {
				if ($GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT') {
					$error = KT_getResource('FOLDER_ERROR_D', 'Captcha',array(KT_CAPTCHA_TEMP_FOLDER));
				} else {
					$error = KT_getResource('FOLDER_ERROR', 'Captcha');
				}
			return $this->formatError($error);
		}				
		// with gd
		if ($this->lib == "gd") {
			$arr["GD Version"] = 'GD not available';
			if (function_exists('gd_info')) {
				$arr = gd_info();
				preg_match("/(2)\.[\d]+/i", $arr["GD Version"], $matches);
			}			
			if (!isset($arr) || !isset($matches[1]) || (int)$matches[1] < 2) {
				$error = KT_getResource('PHP_GD_VERSION_ERROR', 'Captcha', array($arr["GD Version"]));
				return $this->formatError($error);
			}
			$im = imagecreatefrompng(dirname(__FILE__).'/captcha.png') or $error = KT_getResource('PHP_GD_ERROR', 'Captcha', array()); 
			if (isset($error)) {
				return $this->formatError($error);
			}
			$string = $this->getTextCaptcha();
                        $font = imageloadfont(dirname(__FILE__)."/fonts/Courier.gdf");
                        if($font === false) {
                          $font = 5;
                        }
			$fontFileName =  dirname(__FILE__).'/fonts/MyriadWebPro.ttf';
			$wFont =  24;
			$hFont =  24;
			// write the letters
			for ($i=0; $i<strlen($string); $i++) {
				$color1 = rand(0, 64);
				$color2 = rand(0, 64);
				$color3 = rand(0, 64);
				$text_color = imagecolorallocate($im, $color1, $color2, $color3); 
				$okttf = false;
				if(function_exists('imagettftext')) {
				    $okttf = @imagettftext($im, 14, rand(-25, 25), 10+$i*$wFont, $hFont+rand(4, 26), $text_color, $fontFileName, $string[$i]);
				}
				if ($okttf === false) {
					$fim = imagecreatetruecolor($wFont+9, $hFont+9);
					$back = imagecolorallocate($fim, 255, 255, 255); 
					imagefilledrectangle($fim, 0, 0, $wFont+8, $hFont+8, $back); 
					$transparent2 = imagecolorallocate($fim, 255, 255, 255);
					$text_color = imagecolorallocate($fim, $color1, $color2, $color3);  
					
					imagestring($fim, $font, 4, 4, $string[$i], $text_color);
					if(function_exists("imagerotate")) {
						$fim = imagerotate($fim, rand(-25, 25), $transparent2);
					}
					
					$iTranspa2 = imagecolortransparent($fim, $transparent2);
					imagecopymerge($im, $fim, 0+$i*$wFont, rand(4,26), 0, 0, $wFont+9, $hFont+9, 80);
					imagedestroy($fim);
				}
			}
			
			imagepng($im, KT_CAPTCHA_TEMP_FOLDER . $this->filename);
			imagedestroy($im);
		// with imagemagick
		} else {
			$sourceFileName =  dirname(__FILE__).'/captcha.png';
			$fontFileName =  dirname(__FILE__).'/fonts/MyriadWebPro.ttf';
			$destFileName = KT_CAPTCHA_TEMP_FOLDER . $this->filename;
			$arrCommands = array($GLOBALS['KT_prefered_imagemagick_path'].'convert');
			$shell = new KT_shell();
			$direction = rand(0, 10);
			if ($direction % 2 == 0) {
				$textRend = -rand(8,11).'x0+'.(5+(8-strlen($this->text))*20).'+'.(70-(8-strlen($this->text))*5);
			} else {
				$textRend = rand(8,11).'x0+'.(5+(8-strlen($this->text))*20).'+'.(35+(8-strlen($this->text))*5);
			}
			$arrArguments = array(
				'-font',
				$fontFileName,
				'-pointsize',
				'34',
				'-fill',
				'rgb('.rand(0,32).','.rand(0,32).','.rand(0,32).')',
//				'rgba('.rand(0,32).','.rand(0,32).','.rand(0,32).',0.2)',
				'-annotate',
				$textRend,
				$this->text,
				'-wave',
				'3x50',
				'-region',
				'100x70+'.rand(0,100).'+0',
				'-swirl',
				'25',
				'-region',
				'100x70+'.rand(0,100).'+0',
				'-swirl',
				'-25',
				$sourceFileName,
				$destFileName
				);
			$shell->execute($arrCommands, $arrArguments);
			
			if ($shell->hasError()) {
				$arr = $shell->getError();
				$ret = $this->formatError($arr[0]);
				return $ret;
			}
			
		}
		return $relpath . KT_CAPTCHA_TEMP_URL. $this->filename;
	}
	
	/**
	* get the text for captcha
	* @return string
	*/
	function getTextCaptcha() {
		return $this->text;
	}
	
	/**
	* setter; can set the captcha string;
	* @param string
	* @return nothing
	*/
	function setTextCaptcha($string) {
		$this->text = $string;
		$_SESSION[$this->name] = $this->text;
	}	
	
	/**
	* garbage collector; delete all images older than 10 minutes;
	* @return nothing
	*/
	function garbageCollector() {
		if (!file_exists(KT_CAPTCHA_TEMP_FOLDER)) {
			mkdir(KT_CAPTCHA_TEMP_FOLDER);
			return;
		}
		$d = dir(KT_CAPTCHA_TEMP_FOLDER); 
		while (false !== ($entry = $d->read())) { 
			if ( is_file(KT_CAPTCHA_TEMP_FOLDER . $entry) && filemtime(KT_CAPTCHA_TEMP_FOLDER . $entry) < (time() - 60*10) ) {
				@unlink(KT_CAPTCHA_TEMP_FOLDER . $entry);
			}
		} 
		$d->close(); 
	}
	
	/**
	* format the error string to display in page;
	* @param string error string
	* @return string
	*/
	function formatError($error) {
		$ret = '" style="display:none"/>';
		$ret .= $error;
		$ret .= '<span style="display:none';
		return $ret;
	}
	
	/**
	* generata a radom text and set in session; return this text;
	* @return string
	*/
	function getRandomText() {
		$letters = 'abcdefhjkmnpqrstuvwxyz2345678';
		$str = '';
		for($i=0;$i<strlen($letters);$i++) {
			$str .= str_repeat(substr($letters,$i,1),2);
		}
		$str = str_shuffle($str);
		$_SESSION[$this->name] = substr($str, 0, rand(5,8));
		return $_SESSION[$this->name];		
	}
}
?>