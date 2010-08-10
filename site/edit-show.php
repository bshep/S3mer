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
	
	if(!isset($_GET['showid'])) {
    	header("Location: show-tiles.php");
	}
	
	
	$loggeduser=$_SESSION['loggeduser'];
	
	$sql="SELECT count(id) as count FROM `show` WHERE id=" . $_GET['showid'] . " AND owner=" . $loggeduser['id'];
	
	$result=mysql_query($sql);
	$showcount=0;
	if($result!=null){
		$showcount=mysql_result($result,0);
	}	
	
	if($showcount==0){
		header("Location: show-tiles.php");
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
		
	$sql="SELECT * FROM layout";
	$result=mysql_query($sql);
	$layouts=array();
	
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$nrow=array();
		foreach($row as $ind=>$r){
			$nrow[$ind]=htmlentities($r);
		}
		
		$sql2="SELECT * FROM layoutregion WHERE layoutid=" . $nrow['id'] . " ORDER BY id";
		$result2=mysql_query($sql2);
		$layoutregions=array();
		while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
			$layoutregions[]=$row2;
		}
		
		$nrow['layoutregions']=$layoutregions;
		$layouts[]=$nrow;
		
	}
	
	
	$sql="SELECT layoutregion.*,regiontype.regiontype AS regiontypet FROM layoutregion LEFT OUTER JOIN regiontype ON regiontype.id=layoutregion.regiontype ORDER BY id";
	$result=mysql_query($sql);
	$allregions=array();
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$nrow=array();
		foreach($row as $ind=>$r){
			$nrow[$ind]=htmlentities($r);
		}
			
		$allregions[]=$nrow;
		
	}
	
	
	
	$sql="SELECT * FROM `show` WHERE id=" . $_GET['showid'];
	$result=mysql_query($sql);
	$show=array();
	
	if ($result != null) {
	    $i=0;
	    
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	      
	      $nrow=array();
	      
	      foreach($row as $ind=>$r){
	        $nrow[$ind]=htmlentities($r);
	      }
	          
	      $show=$nrow;
	      
	      $i++;
	    }
	}	
	
	$proplayers=0;
	
	if($show['backgroundimage']!=0){
	
		$sql = "SELECT name FROM media WHERE id=" . $show['backgroundimage'];
		$result = mysql_query($sql);
		
		if($result != null){
			$backgroundthumbnailimage = mysql_result($result,0);
			$smarty->assign('backgroundthumbnailimage',$backgroundthumbnailimage);
		}
	
	}
	
	
	
	$sql="SELECT count(id) FROM player WHERE pro=1 AND owner =" . $loggeduser['id'];
	$result=mysql_query($sql);
	if ($result != null) {
		$proplayers = mysql_result($result,0);
	}
	
	
	$smarty->assign('proplayers',$proplayers);
	
		
	$sql="SELECT id,name FROM `rss_sources` ORDER BY name";
	$result=mysql_query($sql);
	$rss_sourceids=array();
	$rss_sourcenames=array();
	
	if ($result != null) {
	    $i=0;
	    
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	      
	      $nrow=array();
	      
	      foreach($row as $ind=>$r){
	        $nrow[$ind]=htmlentities($r);
	      }
	          
	      $rss_sourceids[]=$nrow['id'];
	      $rss_sourcenames[]=$nrow['name'];
	      
	      $i++;
	      
	    }
	}
	
	$sql="SELECT * FROM mediafolders WHERE `owner`=" . $loggeduser['id'] . " order by folder";
	$result=mysql_query($sql);
	$mediafolderids=array();
	$mediafoldernames=array();
	if ($result != null) {
	    
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	      
	      $nrow=array();
	      
	      foreach($row as $ind=>$r){
	        $nrow[$ind]=htmlentities($r);
	      }
	          
	      $mediafolderids[]='mediafolder_' . $nrow['id'];
	      $mediafoldernames[]=$nrow['folder'];
	      
	    }
	}	
	
	$sql="SELECT * FROM media WHERE deleteflag=0 AND ownerid=" . $loggeduser['id'] . " LIMIT 0,21";
	$result=mysql_query($sql);
	$mediaitems=array();
	
	if($result!=null){
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$mediaitems[]=$row;
		}
	}
	
	$sql="SELECT tutorial FROM users WHERE id=" . $loggeduser['id'];
	$result=mysql_query($sql);
	$tutStatus = mysql_fetch_array($result, MYSQL_ASSOC);
	
	$uname = $_SESSION['loggeduser']['username'];
	
	$smarty->assign('uname',$uname);
	$smarty->assign('tutStatus', $tutStatus);
	$smarty->assign('topmenus', $topmenus);
	$smarty->assign('navmenus', $navmenus);
	$smarty->assign('bottommenus',$bottommenus);
	$smarty->assign('layouts',$layouts);
	$smarty->assign('show',$show);
	$smarty->assign('showid',$_GET['showid']);
	$smarty->assign('current_template',$show['template']);
	$smarty->assign('allregions',$allregions);

	if (isset($rss_sources)) {
		$smarty->assign('rss_sourceids',$rss_sources);
	}

	$smarty->assign('mediafolderids',$mediafolderids);
	$smarty->assign('mediafoldernames',$mediafoldernames);
	$smarty->assign('mediaitems',$mediaitems);
	
		
	
	
	$smarty->assign('lang',$lang);
	
	if (isset($player)) {
		$smarty->assign('player',$player);
	}
	
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
	$smarty->assign('showid',$_GET['showid']);
	
	
	$smarty->display('edit-show.html');
	
?>