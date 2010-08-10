<?php

	global $smarty;
	global $userlang;
	
	require_once('util/application.php');
	
	force_https("undo");

	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");	
	
//process login if cookie exists
	
	if(isset($_COOKIE['s3mer_token']) && !isset($_SESSION['loggeduser'])){
		$token = $_COOKIE['s3mer_token'];
		
		//echo $token;
		
		$sql = "SELECT * FROM users WHERE token ='".mysql_escape_string($token)."'";
		
		
		//print($sql);
		
		$result = mysql_query($sql);

		if($result) {	
			//echo $sql;
			
			$user_row = mysql_fetch_array($result);
			
			$loggeduser=array();
			
			$loggeduser['id'] = $user_row["id"];
			$loggeduser['firstname'] = $user_row["firstname"];
			$loggeduser['lastname'] = $user_row["lastname"];
			$loggeduser['username'] = $user_row["username"];
			$loggeduser['quota'] = $user_row["quota"];
			$_SESSION['error_message']='';
			$_SESSION['loggeduser']=$loggeduser;
			
			
			header("Location: player-tiles.php");
			setcookie("s3mer_token",$token,time()+1296000);
			return;	
		} else {
			echo mysql_error();
		}
	}
	
//end process login if cookie exists

	
//redirect to player-tiles si esta logged in
	if( isset($_SESSION['loggeduser'])) {
		header('Location: player-tiles.php');
	}
//end redirect to player-tiles si esta logged in
	
	

	
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
	
	if($lang =='English'){
		$lang='en';
	}
	

	
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
		if($i==0){
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
	
	
	$smarty->assign('topmenus', $topmenus);
	$smarty->assign('navmenus', $navmenus);
	$smarty->assign('bottommenus',$bottommenus);
	
	
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
	
	$smarty->display('index.html');

?>
