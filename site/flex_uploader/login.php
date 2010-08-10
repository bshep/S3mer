<?php	
	require_once('../util/session.php');
	require_once('../util/dbconfig.php');
	// requite_once('util.php');
	
	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	
	$sql = "SELECT * FROM users WHERE username='" . GET_MYSQL_ESCAPED('username',$_REQUEST) . 
							   "' AND password=MD5('" . GET_MYSQL_ESCAPED('password',$_REQUEST) . "')";
	$result = mysql_query($sql);
	
	if(!$result) {
		print '0';
		reset_session();
		return;		
	}
	
	$num=mysql_numrows($result);
	
	if($num == 0) { // This means no users returned... lets check with the plain text password
		$sql3 = "SELECT * FROM users WHERE username='" . GET_MYSQL_ESCAPED('username',$_REQUEST) . 
								   "' AND password='" . GET_MYSQL_ESCAPED('password',$_REQUEST) . "'";
		$result = mysql_query($sql3);
		
		if(!$result) {
			print '0';
			reset_session();
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
	
	if( $num == 1)
	{
		$user_row = mysql_fetch_array($result);
		
		loadUserDataFromRow($user_row);
					
		if(isset($_POST['rememberme'])){
			$token = uniqid(md5($_POST['username'])."-",true);
			
			$sql = "UPDATE users SET token = '". mysql_escape_string($token)."' WHERE id = ".$user_row["id"];
			mysql_query($sql);
			
			if($result) {		
				setcookie("s3mer_token",$token,time()+1296000);
			}
	
		}
		
		//print_r($_SESSION);
		print checkPro($user_row["id"]);
		
	} else {		
		print '0';
		reset_session();
		return;
	}
	

	function checkPro($userid) {
		$sql = "SELECT `prosubscriptions`, `insider` FROM `users` WHERE `id` = '". mysql_real_escape_string($userid) ."'";
		
		$result = mysql_query($sql);

		$data = mysql_fetch_array($result, MYSQL_ASSOC);

		if ($data['prosubscriptions'] > 0 || $data['insider'] == 1) {
		    return '1';
		} else {
		    return '2';
			reset_session();
		}
	}
	
?>