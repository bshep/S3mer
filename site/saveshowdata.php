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
	
	if(isset($_SESSION['lastpage'])){
		$_SESSION['prevlastpage']=$_SESSION['lastpage'];
	}
	$_SESSION['lastpage']=$_SERVER["REQUEST_URI"];
	
	if(isset($_SESSION['userlang'])){
		$lang=$_SESSION['userlang'];
		
	}
	else{
		$lang='en';	
	}
	
	$userlang=$lang;
	
	
	if(isset($_SESSION['showname'])){
		
		$sql="UPDATE `show` SET showname='" . str_replace("'", "''", $_SESSION['showname']) . "', ns=0 WHERE id=" . $_GET['showid'];		
		$afr=mysql_query($sql);
		
	}
	
	if(isset($_SESSION['showdesc'])){
		
		$sql="UPDATE `show` SET description='" . str_replace("'", "''", $_SESSION['showdesc']) . "', ns=0 WHERE id=" . $_GET['showid'];		
		$afr=mysql_query($sql);
		
	}
	
	if(isset($_SESSION['showtype'])){
		$sql="UPDATE `show` SET showtype=" . $_SESSION['showtype'] . " WHERE id=" . $_GET['showid'];
		$afr=mysql_query($sql);
	}
	
	if(isset($_GET['clock'])){
		$sql = "UPDATE `show` SET clock = " . $_GET['clock'] . " WHERE id=" . $_GET['showid'];
		$afr = mysql_query($sql);
	}
	
	
		
	unset($_SESSION['showname']);
	unset($_SESSION['showdesc']);
	unset($_SESSION['showtype']);
	
	
	

	
	
?>