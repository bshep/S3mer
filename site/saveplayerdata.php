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
	
	if(isset($_SESSION['playername'])){
		$sql="UPDATE player SET playername='" . str_replace("'", "''", $_SESSION['playername']) . "', np=0 WHERE id=" . $_GET['playerid'];
		
		$afr=mysql_query($sql);
		
	}
	
	if(isset($_SESSION['playerdesc'])){
		$sql="UPDATE player SET description='" . str_replace("'", "''", $_SESSION['playerdesc']) . "', np=0 WHERE id=" . $_GET['playerid'];
		
		$afr=mysql_query($sql);
	}
	
	
	if(isset($_SESSION['playermode'])){
		$sql="UPDATE player SET playertype='" . $_SESSION['playermode'] . "', np=0 WHERE id=" . $_GET['playerid'];
		
		$afr=mysql_query($sql);
	}
	
	
	if(isset($_SESSION['loggeduser'])){
	
		
		$loggeduser=$_SESSION['loggeduser'];
		
		$sql="SELECT channel FROM player WHERE id=" . $_GET['playerid'];

		$result=mysql_query($sql);
		$nr=mysql_numrows($result);
		
		if($nr>0){
			
			$channelnumber=mysql_result($result,0,"channel");
		
			$sql="DELETE FROM channelschedule WHERE channelschedule.deleteflag=1 AND `channelschedule`.channel=" . $channelnumber;
			$afr=mysql_query($sql);
			
			$sql="UPDATE channelschedule  SET channelschedule.ns=0 WHERE `channelschedule`.channel=" . $channelnumber;
			$afr=mysql_query($sql);		
			
			$sql="UPDATE channelschedule SET `order`=temporder WHERE channel=" . $channelnumber;
			$afr=mysql_query($sql);
			
			
		}
		
		
	}
	
	
	unset($_SESSION['playername']);
	unset($_SESSION['playerdesc']);
	unset($_SESSION['playermode']);
	
	
	
	
?>