<?php
	global $smarty;

	require_once('util/application.php');
	
	$_SESSION['language']=$_GET['lang'];
	$_SESSION['language_selected'] = true;
	
    header("Location:" . $_SESSION['lastpage']);
	
	
?>