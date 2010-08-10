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
	
	
	if(isset($_POST['newsletter'])){
		$newsletter=1;
	}
	else{
		$newsletter=0;
	}
	
	$loggeduser=$_SESSION['loggeduser'];
	
	
	$sql="UPDATE users SET firstname='" . $_POST['firstname'] . "',lastname='" . $_POST['lastname'] . "',address1='" . $_POST['address1'] . "',address2='" . $_POST['address2'] . "',city='" . $_POST['city'] . "',stateprovince='" . $_POST['state'] . "',postalcode='" . $_POST['zip'] . "',country='" . $_POST['country'] . "',venue='" . $_POST['venue'] . "',othervenue='" . $_POST['explain'] . "',newsletter='" . $newsletter . "',dateformat='" . $_POST['dateformat'] . "',timeformat='" . $_POST['timeformat'] ."' WHERE id=" . $loggeduser['id'];
	
	$afr=mysql_query($sql);
	
	
	
	if(strlen($_POST['password1'])>0){
		$sql="UPDATE users SET `password`='" . $_POST['password1'] . "' WHERE id=" . $loggeduser['id'];
		$afr=mysql_query($sql);
	}
	
	
	
	
	header("Location:" . $_SESSION['lastpage']);

?>