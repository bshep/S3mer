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
	
	$_SESSION['library_selected_folder']='All';
	$userlang=$lang;
	$loggeduser=$_SESSION['loggeduser'];
	
	
	$sql="SELECT * FROM mediafolders WHERE owner=" . $loggeduser['id'] . " order by folder";
	$result=mysql_query($sql);
	$mediafolders=array();
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$nrow=array();
		
		foreach($row as $ind=>$r){
			$nrow[$ind]=htmlentities($r);
		}
				
		$mediafolders[]=$nrow;
		
	}
	
	$smarty->assign('mediafolders',$mediafolders);
	$smarty->display('folderlist.html');

?>