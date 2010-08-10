<?php
	
	
	global $smarty;
	global $userlang;
	
	require_once('util/application.php');

	if(!isLoggedIn()) {
		header('Location: login.php');
		return;
	}
	
	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	
	
	
	if(isset($_SESSION['userlang'])){
		$lang=$_SESSION['userlang'];
		
	}
	else{
		$lang='en';	
	}

	$userlang=$lang;
	
	$loggeduser=$_SESSION['loggeduser'];	
	
	$offset = 21 * (intval($_GET['page'])-1);
	
	$sql="SELECT * FROM media WHERE ownerid=" . $loggeduser['id'] . " AND name LIKE '%" . $_GET['sw'] . "%' AND mediatype LIKE '" . $_GET['mediatype'] . "' AND filename LIKE '" . $_GET['folder'] . "' AND deleteflag=0 AND `shared`=" . $_GET['shareditem'] . " order by `name` LIMIT " . $offset . ",21";
	
	$result=mysql_query($sql);
	$mediaitems=array();
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$mediaitems[]=$row;
	}
	
	$smarty->assign('mediaitems',$mediaitems);
	$smarty->assign('loggeduser',$loggeduser);
	
	$smarty->assign('buttonselect', $buttonselect);
	
	$smarty->display('edit-show-library-contents.html');
	
	
	
?>