<?php
	
	global $smarty;
	global $userlang;
	
	require_once('util/application.php');
	
	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	
	
	$sql = "SELECT * FROM users WHERE username='" . GET_MYSQL_ESCAPED('username',$_REQUEST) . 
							   "' AND password=MD5('" . GET_MYSQL_ESCAPED('password',$_REQUEST) . "')";
	$result = mysql_query($sql);
	
	if(!$result) {
		$_SESSION['error_message']='InvalidAccount';
		header("Location: login.php");
		return;		
	}
	
	$num=mysql_numrows($result);
	
	
	if($num == 0) { // This means no users returned... lets check with the plain text password
		$sql3 = "SELECT * FROM users WHERE username='" . GET_MYSQL_ESCAPED('username',$_REQUEST) . 
								   "' AND password='" . GET_MYSQL_ESCAPED('password',$_REQUEST) . "'";
		$result = mysql_query($sql3);
		
		if(!$result) {
			$_SESSION['error_message']='InvalidAccount';
			header("Location: login.php");
			return;		
		}
		
		$num=mysql_numrows($result);
		
		if($num == 1) {
			$id = mysql_result($result,0,'id');
			$old_pass = mysql_result($result,0,'password');
			
			$sql2 = "UPDATE users SET password = MD5('". mysql_escape_string($old_pass) ."') WHERE id = '" . $id . "'";
			
			mysql_query($sql2);
			$result = mysql_query($sql);
			
		}
		
	}	
	
	if($num==0 || $num > 1)
	{
		$_SESSION['error_message']='InvalidAccount';
		header("Location: login.php");
		return;
	}
	else
	{
		$user_row = mysql_fetch_array($result);
		
		$loggeduser=array();
		
		$loggeduser['id'] = $user_row["id"];
		$loggeduser['firstname'] = $user_row["firstname"];
		$loggeduser['lastname'] = $user_row["lastname"];
		$loggeduser['username'] = $user_row["username"];
		$loggeduser['quota'] = $user_row["quota"];
		$_SESSION['error_message']='';
		$_SESSION['loggeduser']=$loggeduser;
		
		if(isset($_POST['rememberme'])){
			$token = uniqid(md5($_POST['username'])."-",true);
			
			$sql = "UPDATE users SET token = '". mysql_escape_string($token)."' WHERE id = ".$user_row["id"];
			mysql_query($sql);
			
			if($result) {		
				setcookie("s3mer_token",$token,time()+1296000);
			}
	
		}
		
			
		//print_r($_SESSION);
		header("Location: player-tiles.php");
		
	}
	
?>