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
	
	if(isset($_SESSION['loggeduser'])){

		
		$loggeduser=$_SESSION['loggeduser'];
		
		
		$sql="INSERT INTO `show`(owner,createdon,showtype) VALUES('" . $loggeduser['id'] . "',now()," . $_GET['showtype'] . ")";
		$afr=mysql_query($sql);
	
		$sql="SELECT id FROM `show` WHERE owner=" . $loggeduser['id'] . " ORDER BY id DESC LIMIT 1";
		$result=mysql_query($sql);
		$nr=mysql_numrows($result);
		
		if($nr>0){
			$showid=mysql_result($result,0,'id');
			
				header("Location: edit-show.php?showid=" . $showid);
		
		}
		else{
			header("Location: show-tiles.php");
		
		}
		
		
		
	}
	
?>