<?php

	global $smarty;
	global $userlang;

	require_once('util/application.php');

	force_https();

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
	
	
	
	$loggeduser = $_SESSION['loggeduser'];
	
	
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
	
	
	$sql="SELECT * FROM users WHERE ID=" . $loggeduser['id'];
	$result=mysql_query($sql);
	$userinfo=array();
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$userinfo=$row;
		$newsletter='';
		if($row['newsletter']==1){
			$newsletter='checked';
		}
		$userinfo['newsletterchecked']=$newsletter;
		$userinfo['paymentamount']=$userinfo['prosubscriptions']*60;
	}
	
	if($userinfo['prosubscriptions']>0){
		$prouser=1;
	}
	else{
		$prouser=0;
	}
	
	
	$sql="SELECT DATE_FORMAT(max(`date`), '%m/%d/%Y') FROM payments_received WHERE userid=" . $loggeduser['id'];
	$result=mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$lastpayment = mysql_result($result,0);
	}
	
	
	$sql="SELECT * FROM timeformats";
	$result=mysql_query($sql);
	$timeformatids=array();
	$timeformats=array();
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$timeformatids[]=$row['id'];
		$timeformats[]=$row['format'];
	}
	
	$sql="SELECT * FROM dateformats";
	$result=mysql_query($sql);
	$dateformatids=array();
	$dateformats=array();
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$dateformatids[]=$row['id'];
		$dateformats[]=$row['format'];
	}
	
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
	$smarty->assign('lang',$lang);
	
	
	$loggeduser=$_SESSION['loggeduser'];
	$smarty->assign('loggeduser', $loggeduser['username']);
	
	
	$smarty->assign('lastpayment', $lastpayment);
	$smarty->assign('topmenus', $topmenus);
	$smarty->assign('navmenus', $navmenus);
	$smarty->assign('bottommenus',$bottommenus);
	$smarty->assign('countryids',$countryids);
	$smarty->assign('countrynames',$countrynames);
	$smarty->assign('venuenames',$venuenames);
	$smarty->assign('venueids',$venueids);
	$smarty->assign('userinfo',$userinfo);
	$smarty->assign('prouser',$prouser);
	
	$smarty->assign('timeformatids',$timeformatids);
	$smarty->assign('timeformats', $timeformats);
	$smarty->assign('dateformatids',$dateformatids);
	$smarty->assign('dateformats', $dateformats);
	$smarty->assign('lastpage',$_SESSION['lastpage']);
	
	$smarty->display('settings.html');

?>