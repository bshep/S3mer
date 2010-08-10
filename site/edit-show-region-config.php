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
	
		
	$loggeduser=$_SESSION['loggeduser'];
	
	
	if(isset($_GET['templateregion']) && isset($_GET['showregion'])){
		
		$sql="SELECT regionname,regiontype FROM layoutregion WHERE id=" . $_GET['templateregion'];
		$result=mysql_query($sql);
		if($result){
			
			$regionname=mysql_result($result,0,"regionname");
			$regiontype=mysql_result($result,0,"regiontype");
		
		}
		
		$smarty->assign('regiontype',$regiontype);
		$smarty->assign('regionname',$regionname);
		$smarty->assign('templateregion', $_GET['templateregion']);
		$smarty->assign('showregion', $_GET['showregion']);
		
		switch($regiontype){
			case 1:
			case 3:
				
				$sql="SELECT playlist FROM regionplaylists WHERE regionid=" . $_GET['showregion'];
				
		
				$result=mysql_query($sql);
				$nr=mysql_numrows($result);
				if($nr>0){
					$playlistid=(mysql_result($result,0,"playlist"));
					$smarty->assign('playlistid',$playlistid);
					$sql2="select playlistitem.*,media.name,media.description,media.mediatype FROM playlistitem LEFT OUTER JOIN media  ON media.id=playlistitem.mediaid where playlistitem.deleteflag=0 AND playlistid=" . $playlistid . " ORDER BY playlistitem.`order`";
					
				
					
					$result2=mysql_query($sql2);
					$playlistitems=array();
					while ($row = mysql_fetch_array($result2, MYSQL_ASSOC)) {
						$nrow=array();
						foreach($row as $ind=>$r){
							$nrow[$ind]=htmlentities($r);
						}
						$playlistitems[]=$nrow;
					}
					
					$smarty->assign('playlistitems',$playlistitems);
				}
				
				
				$smarty->display('region-type-main-sidebar.html');
				break;
				
			case 2:
				
				$sql="SELECT * FROM rss_sources order by Name";
				$result=mysql_query($sql);
				
				$rssids=array();
				$rssnames=array();
				$rssurls=array();
				
				while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
					$rssids[]=$row['id'];
					$rssnames[]=htmlentities($row['Name']);
					$rssurls[]=$row['URL'];
				}
				
				
				$sql="SELECT rssid,rssurl FROM showregion WHERE id=" .$_GET['showregion'];
				$result=mysql_query($sql);
				$nr=mysql_numrows($result);
				
				$rssFeed=0;
				$rssURL='';
				
				if($nr>0){
					
					$rssFeed=mysql_result($result,0,"rssid");
					$rssURL=mysql_result($result,0,"rssurl");
					
				}
				
				
				$smarty->assign('rssids',$rssids);
				$smarty->assign('rssnames',$rssnames);
				$smarty->assign('rssurls',$rssurls);
				$smarty->assign('rssFeed',$rssFeed);
				$smarty->assign('rssURL',$rssURL);
				$smarty->display('region-type-rss.html');
				
				break;
			
		}	
	}
?>