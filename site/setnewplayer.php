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
		
		
		$sql="INSERT INTO player(owner,createdon,channel,dirty) VALUES('" . $loggeduser['id'] . "',now(),0,1)";
		$afr=mysql_query($sql);
	
		$sql="SELECT id FROM player WHERE owner=" . $loggeduser['id'] . " ORDER BY id DESC LIMIT 1";
		$result=mysql_query($sql);
		$nr=mysql_numrows($result);
		
		if($nr>0){
			
			$playerid=mysql_result($result,0,'id');
			
			$channelname='';
			
			if($lang=='en'){
				$channelname='Player' . $playerid . "-channel";
			}
			elseif($lang=='es'){
				$channelname='Canal del Reproductor - ' . $playerid;
			}
			elseif($lang=='pt'){
				$channelname='Canal do Reprodutor - ' . $playerid;
			}
			
			$sql="INSERT INTO channel(channelname,owner,createdon,shared) VALUES('" . $channelname . "','" . $loggeduser['id'] . "',now(),0)";
			$afr=mysql_query($sql);

			$sql="SELECT id FROM channel WHERE owner=" . $loggeduser['id'] . " ORDER BY id DESC LIMIT 1";
			
			$result2=mysql_query($sql);
			$nr2=mysql_numrows($result2);
			
			
			
			if($nr2>0){
				
				$channelid=mysql_result($result2,0,'id');
				$sql="UPDATE player SET channel=" . $channelid . " WHERE id=" . $playerid;
				
				$afr=mysql_query($sql);
				
				
				header("Location: edit-player.php?playerid=" . $playerid);
				
			}
			else{
				header("Location: player-tiles.php");
			}
		}
		
		
		
	}
	
	
		
?>