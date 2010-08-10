<?php
	
	global $smarty;
	global $userlang;
	
	require_once('util/application.php');
	
	setup_navmenus();
	
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
		
	$sql="SELECT * FROM `users` WHERE `username`='" . GET_MYSQL_ESCAPED('email',$_POST) . "'";
	$result=mysql_query($sql);
	
	if(!$result) {
		//Maybe we should write some error into a log?
		$smarty->assign('customeremail',$_POST['email']);
		$smarty->display('sendpasswordsuccess.html');
		return;		
	}
	
	$nr=mysql_numrows($result);
	
	if($nr==0){	
		//Lie to the user about sending his password, since we dont have an account why send himhis password?
		$smarty->assign('customeremail',$_POST['email']);
		$smarty->display('sendpasswordsuccess.html');
		return;		
	}
	else{
		$user_row = mysql_fetch_array($result);
		$reset_token = uniqid(md5($_POST['email']),true);
		
		$sql = "UPDATE users SET pass_reset_token ='" . mysql_escape_string($reset_token) . 	
					"' WHERE id = '" . $user_row['id'] . "'";
		$result = mysql_query($sql);
		
		if(!$result) {
			echo mysql_error();
		}
			
		if($lang=='en'){
			$subjectstring='Your s3mer lost password request';
			$messagestring="Please go to the following address to reset your password \r\n" . 
                      "https://" . $_SERVER['HTTP_HOST'] . "/passwordreset.php?token=".$reset_token."\r\n\r\n\r\n" .
                      "Thank You,\r\n".
                      "The S3mer Team";
		}
		elseif($lang=='es'){
			$subjectstring='Su pedido de contrase–a de s3mer';
//			$messagestring='Su contrase–a para la cuenta de s3mer ' . $_POST['email'] . ' es ' . mysql_result($result,0,'password');
			$messagestring="Please go to the following address to reset your password \r\n" . 
                      "https://" . $_SERVER['HTTP_HOST'] . "/passwordreset.php?token=".$reset_token."\r\n\r\n\r\n" .
                      "Thank You,\r\n".
                      "The S3mer Team";
		}
		elseif($lang=='pt'){
			$subjectstring='Seu pedido de senha para s3mer';
//			$messagestring='Sua senha para a conta de s3mer ' . $_POST['email'] . ' Ž ' . mysql_result($result,0,'password');
			$messagestring="Please go to the following address to reset your password \r\n" . 
                      "https://" . $_SERVER['HTTP_HOST'] . "/passwordreset.php?token=".$reset_token."\r\n\r\n\r\n" .
                      "Thank You,\r\n".
                      "The S3mer Team";
		}
		
		mail($_POST['email'],$subjectstring,$messagestring,"From: do.not.reply@s3mer.com");	
		$smarty->assign('customeremail',$_POST['email']);
			
		$smarty->display('sendpasswordsuccess.html');
		
	}
	
	
?>