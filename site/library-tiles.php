<?php
	global $smarty;
	global $userlang;
	
	require_once('util/application.php');
	
	force_https("undo");
	
	if(!isLoggedIn()) {
		header('Location: login.php');
		return;
	}
	
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
	
	$_SESSION['library_selected_folder']='All';
	
	$userlang=$lang;
	$loggeduser=$_SESSION['loggeduser'];
	
	
	$sql="SELECT " . $lang . " AS menu, link FROM menus WHERE menuid=4 ORDER BY id";
	$result=mysql_query($sql);
	$topmenus=array();
	
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$nrow=array();
		foreach($row as $ind=>$r){
			$nrow[$ind]=htmlentities($r);
		}
		$topmenus[]=$nrow;
		
	}
	
	
	$sql="SELECT " . $lang . " AS menu, link FROM menus WHERE menuid=5 ORDER BY id";
	$result=mysql_query($sql);
	$navmenus=array();
		
	$i=0;
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$nrow=array();
		
		foreach($row as $ind=>$r){
			$nrow[$ind]=htmlentities($r);
		}
		if($i==2){
			$nrow['selected']=1;	
		}
		else{
			$nrow['selected']=0;
		}
		
		$navmenus[]=$nrow;
		
		$i++;
	}
	
	
	$sql="SELECT " . $lang . " AS menu, link FROM menus WHERE menuid=3 ORDER BY id";
	$result=mysql_query($sql);
	$bottommenus=array();
		
	$i=0;
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$nrow=array();
		
		foreach($row as $ind=>$r){
			$nrow[$ind]=htmlentities($r);
		}
		if($i==0){
			$nrow['selected']=1;	
		}
		else{
			$nrow['selected']=0;
		}
		
		$bottommenus[]=$nrow;
		
		$i++;
	}
	
	
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
	
	
	
	$sql="SELECT * FROM media WHERE deleteflag=0 AND ownerid=" . $loggeduser['id'] . " order by `name`";
	
	$result=mysql_query($sql);
	$mediaitems=array();
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$mediaitems[]=$row;
	}
	
	
	
	
	$smarty->assign('mediaitems',$mediaitems);
	$smarty->assign('topmenus', $topmenus);
	$smarty->assign('navmenus', $navmenus);
	$smarty->assign('bottommenus',$bottommenus);
	$smarty->assign('mediafolders',$mediafolders);
	$smarty->assign('totalspace', $loggeduser['quota']);
	$smarty->assign('lang',$lang);
	$smarty->assign('loggeduser', $loggeduser['username']);
	
	if($lang=='en'){
		$language='English';
	}
	else if($lang=='es'){
		$language='Espa&ntilde;ol';
	}
	else if($lang=='pt'){
		$language='Portugu&ecirc;s';
	}
	
	require('amazonS3/sign.php');
	
	$smarty->assign('loggedusername', $_SESSION['loggeduser']['username']);
	$smarty->assign('uploaderPostParams',s3_signature());
	$smarty->assign('language',$language);
	$smarty->display('library-tiles.html');
	
?>