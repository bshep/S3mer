<?php	

	global $smarty;
	global $userlang;
	
	require_once('util/application.php');
	
	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	
	
	require_once('recaptchalib.php');
	$privatekey = "6Ld-sQAAAAAAACT_DPogOJP8HiFXacJ8T9SKK3Ss";
	$resp = recaptcha_check_answer ($privatekey,
	                                $_SERVER["REMOTE_ADDR"],
	                                $_POST["recaptcha_challenge_field"],
	                                $_POST["recaptcha_response_field"]);
	
	

	if($_SESSION['language']=='en'){
		$accountexists='An account with this email already exists, if you forgot your password go to the retrieve lost password page';
		$captchaerror='Captcha Error';
	}
	elseif($_SESSION['language']=='es'){
		$accountexists='Existe una cuenta con esta direcci—n de correo, si olvid— su contrase–a vaya a la p‡gina de contrase–as perdidas';
		$captchaerror='Error en confirmaci—n de Captcha';		
	}
	elseif($_SESSION['language']=='pt'){
		$accountexists='Existe uma conta para este endereo eletr—nico';
		$captchaerror='Error na confirma‹o de Captcha';
	}



	if (!$resp->is_valid) {
	  
		$_SESSION['registrationerror']=$captchaerror;
		header('Location: register.php');
	
	}
	else{
	
	

		$sql="SELECT * FROM users where username='" . $_POST['email1'] . "'";
		$result=mysql_query($sql);
		$numrows = mysql_numrows($result);

	

		if(numrows==0){

			$sql1="INSERT INTO `users`(username,password,firstname,lastname,
			joinedon,accounttype,address1,address2,stateprovince,postalcode,
		country,businessname,venue,city,newsletter,quota,tutorial) VALUES(" .
								 "'" . mysql_escape_string($_POST['email1']) . "'," . 
								 "MD5('" . mysql_escape_string($_POST['password1']) . "'), " . 
								 "'" . mysql_escape_string($_POST['firstname']) . "', " . 
								 "'" . mysql_escape_string($_POST['lastname']) . "', " . 
								 " now(), 1 ," . 
								 "'" . mysql_escape_string($_POST['address1']) . "', " . 
								 "'" . mysql_escape_string($_POST['address2']) . "', " . 
								 "'" . mysql_escape_string($_POST['state']) . "', " . 
								 "'" . mysql_escape_string($_POST['zip']) . "', " . 
								 "'" . mysql_escape_string($_POST['country']) . "', " . 
								 "'" . mysql_escape_string($_POST['businessname']) . "', " . 
								 "'" . mysql_escape_string($_POST['venue']) . "', " . 
								 "'" . mysql_escape_string($_POST['city']) . "', " . 
								 "'" . mysql_escape_string($_POST['newsletter']) . "', " . 
								 " 50, 1 )";

			$afr=mysql_query($sql1);

			unset($_SESSION['registrationerror']);
			
			umask(0);
			

			if($lang=='en'){
				$subjectstring='Your new s3mer account';
				$messagestring='<html>Hi ' . $_POST['firstname'] . ',<br><p>Thank you for openning your account for s3mer. Your username is: ' . 
				$_POST['email1'] . ' and your password is:' . $_POST['password1'] . '</p></html>';
			}
			elseif($lang=='es'){
				$subjectstring='Su nueva cuenta de s3mer';
				$messagestring='<html>Estimado ' . $_POST['firstname'] . ',<br><p>Gracias por abrir tu nueva cuenta en s3mer. Su nombre de usuario es: ' . 
				$_POST['email1'] . ' y su contrase–a es:' . $_POST['password1'] . '</p></html>';
			}
			elseif($lang=='pt'){
				$subjectstring='Sua nova conta de s3mer';
				$messagestring='<html>Oi ' . $_POST['firstname'] . ',<br><p>Obrigado por abrir a sua conta no s3mer. Seu nome de usuario Ž: ' . 
				$_POST['email1'] . ' e sua senha Ž:' . $_POST['password1'] . '</p></html>';
			}

			mail($_POST['email'],$subjectstring,$messagestring,"From: noreply@s3mer.com");	



			header("Location: regsuccess.php?email=" . $_POST['email1']);


		}
		else{
			$_SESSION['registrationerror']=$accountexists;
			header("Location: register.php");
		}
	
		
	}
	
	
	
?>