<?php
	
	global $smarty;
	global $userlang;
	
	require_once('util/application.php');
	
	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	
	$sql="INSERT INTO qsubmit(question) VALUES('" . $_POST['question'] . "')";
	$afr=mysql_query($sql);

	header("Location: faq.php");
	
?>