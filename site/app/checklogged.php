<?php


	require_once('../util/session.php');

	if(isset($_SESSION['player'])){	
		echo 'OK';
	}
	else{
		echo 'NL';
	}
	
?>