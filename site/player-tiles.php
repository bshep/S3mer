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
	
	$_SESSION['editingplayer']=0;
	
	
	$userlang=$lang;
	
	
	$loggeduser=$_SESSION['loggeduser'];	
	$smarty->assign('loggeduser', $loggeduser['username']);
	
	
	$sql="DELETE FROM player WHERE np=1 AND owner=" . $loggeduser['id'];
	$afr=mysql_query($sql);
	
	$sql="UPDATE `channelschedule` INNER JOIN `show` ON `show`.id=channelschedule.`showid` SET deleteflag=0 WHERE deleteflag=1 AND `show`.owner=" . $loggeduser['id'];
	$afr=mysql_query($sql);
	
	$sql="DELETE FROM channelschedule USING channelschedule,`show` WHERE `show`.id=channelschedule.showid AND channelschedule.ns=1 AND `show`.owner=" . $loggeduser['id'];
	$afr=mysql_query($sql);
	
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
	
	
	$sql="SELECT * FROM player WHERE owner='" . $loggeduser['id'] ."'";
	$result=mysql_query($sql);
	
	if(!$result) {
		echo mysql_error();
		return;
	}
	
	$players=array();
		
	$i=0;
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$nrow=array();
		
		foreach($row as $ind=>$r){
			$nrow[$ind]=html_entity_decode($r);
			
			//get time diff
			$nowDate = time();
			
			if($row['lastuptime']=='0000-00-00 00:00:00'){
				$nrow['neverplayed']=1;
			}
			else{
				$nrow['neverplayed']=0;
			}
			
			$lastDate = strtotime($row['lastuptime']);
			$diff = $nowDate-$lastDate;
			$diffInSecs = floor($diff);
			
			$nrow['timediff'] = $diffInSecs;
						
	
			
		}
		
		if($row['disable']==1){
			$nrow['negativepositive']='negative';
		}
		else{
			$nrow['negativepositive']='positive';
		}

		$players[]=$nrow;
		
		$i++;
	}
	
	$comingpaypal = 0;
	
	
	if(isset($_GET['paypal'])){
		$comingpaypal=1;
	}
		

	$smarty->assign('topmenus', $topmenus);
	
	$smarty->assign('comingpaypal', $comingpaypal);
	$smarty->assign('navmenus', $navmenus);
	$smarty->assign('bottommenus',$bottommenus);
	$smarty->assign('players',$players);
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
	$smarty->display('player-tiles.html');
?>
