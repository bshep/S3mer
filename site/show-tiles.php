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
	
	$loggeduser=$_SESSION['loggeduser'];
	
	$smarty->assign('loggeduser', $loggeduser['username']);
	
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
		if($i==1){
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
	
	
	
	$sql="DELETE FROM `show` WHERE ns=1 AND owner=" . $loggeduser['id'];
	$afr=mysql_query($sql);
	
	
	
	$sql="SELECT count(id) FROM player WHERE pro=1 AND owner =" . $loggeduser['id'];
	$result=mysql_query($sql);
	if ($result != null) {
		$proplayers = mysql_result($result,0);
	}
	
	
	$smarty->assign('proplayers',$proplayers);
	
	
	
	$sql="SELECT * FROM `show` WHERE owner=" . $loggeduser['id'];
	$result=mysql_query($sql);
	$shows=array();
		
	$i=0;
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$nrow=array();
		
		
		
		foreach($row as $ind=>$r){
			$nrow[$ind]=utf8_encode($r);
		}
		
		
		if($i==0){
			$nrow['selected']=1;	
		}
		else{
			$nrow['selected']=0;
		}
		
		if($row['disable']==1){
			$nrow['negativepositive']='negative';
		}
		else{
			$nrow['negativepositive']='positive';
		}
		
		
		$shows[]=$nrow;
		
		$i++;
	}
	
	
	
	

	$smarty->assign('topmenus', $topmenus);
	$smarty->assign('navmenus', $navmenus);
	$smarty->assign('bottommenus',$bottommenus);
	$smarty->assign('shows',$shows);
	
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
	
	
	$smarty->display('show-tiles.html');
?>