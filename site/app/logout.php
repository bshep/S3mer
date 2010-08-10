<?php

	require_once('../util/session.php');
	require_once('../util/dbconfig.php');

	$_SESSION = array();
	
	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (isset($_COOKIE[session_name()])) {
	   setcookie(session_name(), '', time()-42000, '/');
	}
	
	session_destroy();
	
	print "OK";

?>