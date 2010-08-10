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
	
	
	$userlang=$lang;

	
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
	
	
	
	$smarty->assign('topmenus', $topmenus);
	$smarty->assign('navmenus', $navmenus);
	$smarty->assign('bottommenus',$bottommenus);
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
	
	
	$actualplayers=0;
	
	$loggeduser=$_SESSION['loggeduser'];
	$sql="SELECT prosubscriptions FROM users WHERE id=" . $loggeduser['id'];
	
	$result=mysql_query($sql);
	
	$actualplayers=0;
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$actualplayers=$row['prosubscriptions'];
	}
	
	$proplayerstobuy=0;
	$proplayersdelete=0;
	
	if(isset($_POST['proPlayers'])){
		$proplayerstobuy=$_POST['proPlayers'];
		$proplayersnewtotal = $actualplayers+$proplayerstobuy;
		$amount=($actualplayers+$proplayerstobuy)*60;
		
	}
	
	if(isset($_POST['selectedplayers'])){
		
		$sql="UPDATE player SET prodelete=0 WHERE owner=" . $loggeduser['id'];
		mysql_query($sql);
		
		$selectedplayersremove=$_POST['selectedplayers'];
		$proplayersdelete=sizeof($selectedplayersremove);
		
		$proplayerstobuy=$actualplayers-$proplayersdelete;
		$amount=($actualplayers-$proplayersdelete)*60;
		
		
		
		foreach ($selectedplayersremove as $value){
			$sql="UPDATE player SET prodelete=1 WHERE id=" . $value;
			mysql_query($sql);
		}
		
	}
		
	$modifyflag=0;
	
	if($actualplayers==0){
		$modifyflag=0;
	}
	else{
		$modifyflag=2;
	}	
	
	
	$smarty->assign('amount',$amount);
	$smarty->assign('proplayerstobuy',$proplayerstobuy);
	
	
	if(isset($proplayersnewtotal)){
	
		$smarty->assign('proplayersnewtotal', $proplayersnewtotal);
		
	}
	
	
	
	$smarty->assign('modifyflag',$modifyflag);
	$smarty->assign('uid', $loggeduser['id']);
	
	
	$loggeduser=$_SESSION['loggeduser'];
	$smarty->assign('loggeduser', $loggeduser['username']);
	
	$smarty->assign('content','store');
	if(isset($_POST['selectedplayers'])){
		$smarty->display('checkoutunregister.html');
	}
	else{
		$smarty->display('checkout.html');
	}
	
	
?>