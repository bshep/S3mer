<?php

	require_once('../util/session.php');
	require_once('../util/dbconfig.php');
	
	if(!isset($_GET['playerid'])) {
		echo '?';
		die;	
	}
		
	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
		
	$sql="SELECT id,dirty FROM player WHERE id=" . mysql_escape_string($_GET["playerid"]) . " AND owner = '" . $_SESSION['player']['userid'] . "'";
	$result=mysql_query($sql);

	
	if($result){
		$row = mysql_fetch_array($result);
		if($row['dirty'] == 1){
			echo("R");
		}
		else{
			if($row) {
				echo("O");
			} else {
				echo("U");
			}
		}
		
		$sql="UPDATE player SET lastuptime=now() WHERE id = '" . $row['id'] . "'";
		mysql_query($sql);
	}
	else{
		echo("U");
	}
	
?>