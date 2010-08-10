<?php

	global $smarty;
	global $userlang;
	
	require_once('util/application.php');
	
	setup_navmenus();
	
	if(!( isset($_POST['token']) && isset($_POST['password1']) )) {
		$smarty->assign('error_message','Error Processing Request. Please try again1.');
		$smarty->display('passwordresetsuccess.html');
		return;
	}

	if($_POST['token'] == '') {
		$smarty->assign('error_message','Error Processing Request. Please try again1.');
		$smarty->display('passwordresetsuccess.html');
		return;
	}

	
	$sql = "SELECT * FROM users WHERE pass_reset_token = '" .  mysql_escape_string($_POST['token']) . "'";
	$result = mysql_query($sql);
	
	if(!$result || mysql_num_rows($result) == 0) {
		$smarty->assign('error_message','Error Processing Request. Please try again1.');
	} else {	
		
		$user_row = mysql_fetch_array($result);
		
		$sql = "UPDATE users SET pass_reset_token = '', password = MD5('" . mysql_escape_string($_POST['password1']) . "') WHERE pass_reset_token = '" .  mysql_escape_string($_POST['token']) . "' AND id = '" . $user_row['id'] . "'";
		$result = mysql_query($sql);
		
		if(!$result) {
			//echo $sql;
			//echo mysql_error();
			$smarty->assign('error_message','Error Processing Request. Please try again2.');
		} else {
			//echo $sql;
			$smarty->assign('error_message','');
		}
	}	

	$smarty->display('passwordresetsuccess.html');


?>