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
	
	
	if(!isset($_GET['playerid'])) {
    header("Location: player-tiles.php");
	}
	
	
	
	$loggeduser=$_SESSION['loggeduser'];
	
	
	
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
	

	
	
	$sql="SELECT * FROM player WHERE id=" . $_GET['playerid'];
	$result=mysql_query($sql);
	$player=array();
	
	
	
	if ($result != null) {
	    $i=0;
	    
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	      
	      $nrow=array();
	      
	      foreach($row as $ind=>$r){
	        $nrow[$ind]=htmlentities($r);
	      }
	          
	     
	      $player=$nrow;
	      
	      $i++;
	    }
	}	
	
	
	
	$sql="SELECT id,ptype" . $lang ." AS ptype FROM playertypes";
	$result=mysql_query($sql);
	$ptypeids=array();
	$ptypes=array();
	
	if ($result != null) {
		$i=0;
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			
			$ptypeids[]=$row['id'];
			$ptypes[]=htmlentities($row['ptype']);
			
			$i++;
		}
	}
	
	
	
	$sql="SELECT * FROM `show` WHERE owner=" . $loggeduser['id'];
	$result=mysql_query($sql);
	$showids=array();
	$shownames=array();
	
	if ($result != null) {
		$i=0;
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			
			$showids[]=$row['id'];
			$shownames[]=htmlentities($row['showname']);
			
			$i++;
		}
	}
	
	
	
		$sql="SELECT channelschedule.id, channelschedule.showid, channelschedule.Mon, channelschedule.Tue, channelschedule.Wed, channelschedule.Thu, channelschedule.Fri, channelschedule.Sat, channelschedule.Sun, year(channelschedule.startdate) as startyear, month(channelschedule.startdate) as startmonth, day(channelschedule.startdate) as startday, year(channelschedule.enddate) as endyear, month(channelschedule.enddate) as endmonth, day(channelschedule.enddate) as endday, DATE_FORMAT(channelschedule.starttime,'%r') as starttime, DATE_FORMAT(channelschedule.endtime,'%r') as endtime, channelschedule.channel, channelschedule.effect, channelschedule.`order`, channelschedule.AM, channelschedule.PM, channelschedule.ns,  channelschedule.temporder, channelschedule.deleteflag, `show`.showname,`show`.id as showid, effects.effect" . $lang . " AS effectname FROM channelschedule LEFT OUTER JOIN `show` ON `show`.id= channelschedule.`showid` LEFT OUTER JOIN effects on effects.id=channelschedule.effect WHERE deleteflag=0 AND channel=" . $player['channel'] . "  ORDER BY `order` ASC";
		
	$result=mysql_query($sql);
	$channelschedule=array();
	
	if ($result != null) {
	    $i=0;
	    
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	      
	      $nrow=array();
	      
	      foreach($row as $ind=>$r){
	        $nrow[$ind]=htmlentities($r);
	      }
	      
	      
	      if($nrow['Mon']==1){
	      	$nrow['MCH']='checked';
	      }
	      else{
	      	$nrow['MCH']='';
	      }
	      
	    if($nrow['Tue']==1){
	      	$nrow['TCH']='checked';
	      }
	      else{
	      	$nrow['TCH']='';
	      }
	      
	    if($nrow['Wed']==1){
	      	$nrow['WCH']='checked';
	      }
	      else{
	      	$nrow['WCH']='';
	      }
	          
	    if($nrow['Thu']==1){
	      	$nrow['ThCH']='checked';
	      }
	      else{
	      	$nrow['ThCH']='';
	      }
	      
	    if($nrow['Fri']==1){
	      	$nrow['FCH']='checked';
	      }
	      else{
	      	$nrow['FCH']='';
	      }
	      
	    if($nrow['Sat']==1){
	      	$nrow['SaCH']='checked';
	      }
	      else{
	      	$nrow['SaCH']='';
	      }
	      
	    if($nrow['Sun']==1){
	      	$nrow['SCH']='checked';
	      }
	      else{
	      	$nrow['SCH']='';
	      }
	      
	      if($nrow['AM']==1){
	      	$nrow['AMCH']='checked';
	      }
	      else{
	      	$nrow['AMCH']='';
	      }
	      
	      if($nrow['PM']==1){
	      	$nrow['PMCH']='checked';
	      }
	      else{
	      	$nrow['PMCH']='';
	      }
	      
	      
	      
	      $channelschedule[]=$nrow;
	      
	      $i++;
	    }
	}	
	

	
	$sql="SELECT id,effect" . $lang . "  AS effect FROM effects ORDER BY effect" . $lang ;
	$result=mysql_query($sql);
	$effectids=array();
	$effects=array();
	
	if ($result != null) {
		$i=0;
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			
			$effectids[]=$row['id'];
			$effects[]=htmlentities($row['effect']);
			
			$i++;
		}
	}
	
	$sql = "SELECT id,mon" . $lang . " AS month FROM months ORDER BY id";
	$result=mysql_query($sql);
	$monthids=array();
	$monthnames=array();
	if($result!=null){
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			$monthids[]=$row['id'];
			$monthnames[]=$row['month'];
		}
	}
	
	
	
	$smarty->assign('topmenus', $topmenus);
	$smarty->assign('navmenus', $navmenus);
	$smarty->assign('bottommenus',$bottommenus);
	$smarty->assign('ptypeids',$ptypeids);
	$smarty->assign('ptypes',$ptypes);
	$smarty->assign('shownames',$shownames);
	$smarty->assign('showids',$showids);
	$smarty->assign('effectids', $effectids);
	$smarty->assign('effects',$effects);
	$smarty->assign('channelschedule',$channelschedule);
	$smarty->assign('monthids',$monthids);
	$smarty->assign('monthnames',$monthnames);
	
	
	$smarty->assign('lang',$lang);
	
	$smarty->assign('player',$player);
	
	$loggeduser=$_SESSION['loggeduser'];
	
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
	
	$smarty->assign('language',$language);
	
	
	unset($_SESSION['playername']);
	unset($_SESSION['playerdesc']);
	unset($_SESSION['playermode']);
	
	
	$smarty->display('edit-player-schedule.html');
	
	
?>