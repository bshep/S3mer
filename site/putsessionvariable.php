<?php

	global $smarty;
	global $userlang;
	
	require_once('util/application.php');
	
	if(!isLoggedIn()) {
		header('Location: login.php');
		return;
	}

	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	
	if(isset($_GET['vname'])){
		if(isset($_GET['vvalue'])){
			$_SESSION[$_GET['vname']]=$_GET[vvalue];
		}
	}
	
?>