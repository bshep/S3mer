<?php

// Here we do all session handling functions

ini_set("session.use_only_cookies","1");

session_start();

function reset_session() {
	// Unset all of the session variables.
	$_SESSION = array();

	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (isset($_COOKIE[session_name()])) {
	setcookie(session_name(), '', time()-42000, '/');
	}

	// Finally, destroy the session.
	session_destroy();
}

function isLoggedIn() {
	if(isset($_SESSION['loggeduser'])) {
		return true;
	}
	
	return false;
}

function checkToken() {
	if(isset($_COOKIE['s3mer_token']) && !isset($_SESSION['loggeduser'])){
		$token = $_COOKIE['s3mer_token'];

		$sql = "SELECT * FROM users WHERE token ='".mysql_escape_string($token)."'";

		$result = mysql_query($sql);

		if($result) {	
			$user_row = mysql_fetch_array($result);

			loadUserDataFromRow($user_row);
			return true;
		}
	}
	
	return false;
}

function loadUserDataFromRow($user_row) {
	$loggeduser=array();
	
	$loggeduser['id'] = $user_row["id"];
	$loggeduser['firstname'] = $user_row["firstname"];
	$loggeduser['lastname'] = $user_row["lastname"];
	$loggeduser['username'] = $user_row["username"];
	$loggeduser['quota'] = $user_row["quota"];
	$_SESSION['error_message']='';
	$_SESSION['loggeduser']=$loggeduser;		
}

?>