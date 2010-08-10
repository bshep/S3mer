<?php
	global $smarty;
	global $userlang;
	
	require_once('util/application.php');

	force_https();	
	
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
	
	$sql="SELECT id,country FROM countries ORDER BY country";
	$result=mysql_query($sql);
	$countries=array();
	$countryids=array();
	$countrynames=array();
		
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$nrow=array();
		
		foreach($row as $ind=>$r){
			$nrow[$ind]=htmlentities($r);
		}
		
		$countryids[]=$nrow['id'];
		$countrynames[]=$nrow['country'];
		$countries[]=$nrow;
		
	}
	
	$sql="SELECT id,venue" . $lang . " as venue FROM venuetypes ORDER BY venue" . $lang;
	$result=mysql_query($sql);
	
	$venueids=array();
	$venuenames=array();
		

	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$nrow=array();
		
		foreach($row as $ind=>$r){
			$nrow[$ind]=htmlentities($r);
		}
				
		$venueids[]=$nrow['id'];
		$venuenames[]=$nrow['venue'];
		
	
	}
	
	
	
	$smarty->assign('topmenus', $topmenus);
	$smarty->assign('navmenus', $navmenus);
	$smarty->assign('bottommenus',$bottommenus);
	$smarty->assign('lang',$lang);
	$smarty->assign('countries',$countries);
	$smarty->assign('countryids',$countryids);
	$smarty->assign('countrynames',$countrynames);
	$smarty->assign('venuenames',$venuenames);
	$smarty->assign('venueids',$venueids);
	
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
	
	$smarty->display('register.html');
	
	
?>