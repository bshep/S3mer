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
	
	if($_GET['page'] < 1) {
		$_GET['page'] = 1;
	}

	$userlang=$lang;
	$loggeduser=$_SESSION['loggeduser'];	
	
	$offset = 49 * (intval($_GET['page'])-1);
	
	$sql="SELECT * FROM media WHERE ownerid=" . $loggeduser['id'] . " AND name LIKE '%" . $_GET['sw'] . "%' AND mediatype LIKE '" . $_GET['mediatype'] . "' AND filename LIKE '" . $_GET['folder'] . "' AND deleteflag=0 AND `shared`=" . $_GET['shareditem'] . " order by `name` LIMIT " . $offset . ",50";
	
	$result=mysql_query($sql);
	$nr=mysql_numrows($result);
	
	$mediaitems=array();
	$i=0;
	
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$i++;
		if($i<50){
			$mediaitems[]=$row;
		}
	}
	
	if($i==50){
		$shownext=1;
	}
	else{
		$shownext=0;
	}
	
	$smarty->assign('shownext',$shownext);
	$smarty->assign('mediaitems',$mediaitems);	
	$smarty->assign('loggeduser',$loggeduser);
	
	$smarty->display('library-tile-contents.html');
	
	
?>