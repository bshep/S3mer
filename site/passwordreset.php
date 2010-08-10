<?php

	global $smarty;
	global $userlang;
	
	require_once('util/application.php');
	
	setup_navmenus();
	
	
	if(isset($_SESSION['userlang'])){
		$lang=$_SESSION['userlang'];
		
	}
	else{
		$lang='en';	
	}
	
	if(!isset($lang)) {
		$lang = 'en';
	}
	
	if($lang=='en'){
		$language='English';
	}
	else if($lang=='es'){
		$language='Espa&ntilde;ol';
	}
	else if($lang=='pt'){
		$language='Portugu&ecirc;s';
	}
	
	$sql = "SELECT * FROM users WHERE pass_reset_token = '" .  mysql_escape_string($_GET['token']) . "'";
	$result = mysql_query($sql);
	
	if(!$result || mysql_num_rows($result) == 0) {
		$smarty->assign('error_message','Error Processing Request. Please try again.');
		$smarty->assign('resetToken', '');	
	} else {	
		$newToken = $_GET['token'] . '-'. rand(0,100);
		
		$sql = "UPDATE users SET pass_reset_token = '" . mysql_escape_string($newToken) . 
				"' WHERE pass_reset_token = '" .  mysql_escape_string($_GET['token']) . "'";
		$result = mysql_query($sql);
		
		if(!$result) {
			$smarty->assign('error_message','Error Processing Request. Please try again.');
		} else {
			$smarty->assign('error_message','');
		}
		$smarty->assign('resetToken', $newToken);
	}
		
	
	$smarty->assign('language',$language);
	$smarty->display('passwordreset.html');

?>