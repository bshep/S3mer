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
	
	if(isset($_SESSION['prevlastpage'])){
		
		
		if(isset($_SESSION['editingplayer'])){
			$editingplayer=$_SESSION['editingplayer'];
		}
		else{
			$editingplayer=0;
		}
		
		
		
		if($editingplayer==1){
			header("Location: " . $_SESSION['prevlastpage']);
		}
		else{
			header("Location: show-tiles.php");	
		}
		
		
	}
	else{
		header("Location: show-tiles.php");
	}
	
	
?>