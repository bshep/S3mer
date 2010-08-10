<?php

	require_once('../util/session.php');
	require_once('../util/dbconfig.php');

	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	
	
	
	if (isset($_GET['username']) && isset($_GET['password'])){
	
		//echo "Username: " . GET_MYSQL_ESCAPED('username');

		$sql="SELECT * FROM users WHERE username='" . GET_MYSQL_ESCAPED('username') . 
								 "' AND password=MD5('" . GET_MYSQL_ESCAPED('password') . "')";
								 
		$result=mysql_query($sql);
		
		if(!$result) {
			
			echo "ERROR";
			return;
		}
		
		$row = mysql_fetch_array($result);
		
		if($row){
			$_SESSION['player'] = array();
			$_SESSION['player']['userid'] = $row["id"];
			$_SESSION['player']['username'] = $row["username"];
			
/*
			echo "<pre>";
			print_r($_SESSION);
			echo "</pre>";
*/
			
			echo "OK";		
		}
		else{
			//echo $sql;
			echo "ERROR";
		}
	}
	
?>