<?php
	
	require("util/session.php");

	$_SESSION = array();

	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), '', time()-42000, '/');
	}

	session_destroy();
	
	setcookie("s3mer_user",'',time()-1296000);	
	setcookie("s3mer_pwd",'',time()-1296000);
	setcookie("s3mer_token",'',time()-1296000);


	header("Location: index.php");
	
?>