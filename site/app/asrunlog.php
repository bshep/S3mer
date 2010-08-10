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
	
	$fieldlist = "";
	$datalist = "";

	
	if($result){
		if(isset($_POST['data'])){
			
			$data = unserialize($_POST['data']);
			//print_r($data);
			
			foreach($data as $row){
				
				mysql_query("begin");
				
				foreach($row as $key => $val){
					if(strlen($fieldlist)==0){
						$fieldlist = "`" . mysql_real_escape_string($key) . "`";						
						$datalist .= "'" . mysql_real_escape_string($val) . "'";
					}
					else{
						$fieldlist .= ',`' . mysql_real_escape_string($key) . "`";						
						$datalist .= ",'" . mysql_real_escape_string($val) . "'";
					}
					
				}
				
				$fieldlist .= ',`player_id`';
				$datalist .= ',\''. mysql_real_escape_string($_GET['playerid']) .'\'';
				
				$sql="INSERT INTO asrunlog (" . $fieldlist . ") VALUES(" . $datalist . ")";
				mysql_query($sql);
				//print($sql);
				$fieldlist="";
				$datalist="";
				
			}
			
			if(mysql_error()){
				print(mysql_error());
				mysql_query("rollback");
				die('ERROR');
			}
			else{
				mysql_query("commit");
				print('OK');
			}
			
		}
	}
	else{
		echo 'NL';
	}
	
	
?>