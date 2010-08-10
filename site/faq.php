<?php

header("Location: http://docs.s3mer.com/");
/*	global $smarty;
	global $userlang;
	
	require_once('util/application.php');
	
	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	
	if(isset($_SESSION['lastpage'])){
		$_SESSION['prevlastpage']=$_SESSION['lastpage'];
	}
	$_SESSION['lastpage']=$_SERVER["REQUEST_URI"];
	
	if(isset($_SESSION['language'])){
		$lang=$_SESSION['language'];
	}
	else{
		$lang='en';	
	}	
	
	$userlang=$lang;

	$sql="SELECT " . $lang . " AS menu, link FROM menus WHERE menuid=1 ORDER BY id";
	$result=mysql_query($sql);
	$topmenus=array();
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {		
		$nrow=array();
		foreach($row as $ind=>$r){
			$nrow[$ind]=htmlentities($r);
		}
		$topmenus[]=$nrow;
	}
	
	$sql="SELECT " . $lang . " AS menu, link FROM menus WHERE menuid=2 ORDER BY id";
	$result=mysql_query($sql);
	$navmenus=array();
		
	$i=0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$nrow=array();
		foreach($row as $ind=>$r){
			$nrow[$ind]=htmlentities($r);
		}
		$nrow['selected']=0;
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
	
	
	$sql="SELECT id,q" . $lang . " as q, ans" . $lang . " as ans FROM faq";
	$result=mysql_query($sql);
	$qans=array();
	$i=0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$nrow=array();
		foreach($row as $ind=>$r){
			$nrow[$ind]=htmlentities($r);
		}
		$qans[]=$nrow;
		$i++;
	}
	
	
	$smarty->assign('topmenus', $topmenus);
	$smarty->assign('navmenus', $navmenus);
	$smarty->assign('bottommenus',$bottommenus);
	$smarty->assign('qans',$qans);
	$smarty->assign('lang',$lang);
	
	if($lang=='en'){
		$language='English';
	}
	else if($lang=='es'){
		$language='Espa&ntilde;ol';
	}
	else if($lang=='pt'){
		$language='Portugu&ecirc;s';
	}
	
	$smarty->assign('language',$language);
	$smarty->display('faq.html');*/

?>