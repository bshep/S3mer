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


	if (isset($_GET['layoutid'])){
		
		$sql2="SELECT * FROM layoutregion WHERE layoutid=" . $_GET['layoutid'] . " ORDER BY id";
		$result2=mysql_query($sql2);
		$layoutregions=array();
		
		while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
			$layoutregions[]=$row2;
		}
		
	}
	
	$smarty->assign('layoutregions',$layoutregions);
	$smarty->assign('showid', $_GET['showid']);
	$smarty->display('edit-show-template-region.html');
	

?>