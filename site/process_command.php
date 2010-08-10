<?php
	
	require_once('util/application.php');

	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	
	checkToken();
	
	if (isset($_SESSION['loggeduser'])) {
		$loggeduser=$_SESSION['loggeduser'];
	}
	
	if(isset($_GET['command'])){
		
		if(!isLoggedIn()) {
			header('Location: login.php');
			return;
		}
		
		switch($_GET['command']){
			case 'del':
				if(isset($_GET['playerid'])){
					$sql="DELETE FROM player WHERE id=" . GET_MYSQL_ESCAPED('playerid');
					$afr=mysql_query($sql);		
				}
				elseif(isset($_GET['showid'])){
					$sql="DELETE FROM `show` WHERE id=" . GET_MYSQL_ESCAPED('showid');
					$afr=mysql_query($sql);
				}
				
				
				break;
			case 'ds':
				if(isset($_GET['playerid'])){
					$sql="UPDATE player SET disable=1 WHERE id=" . GET_MYSQL_ESCAPED('playerid');
		 			$afr=mysql_query($sql);
				}
				elseif(isset($_GET['showid'])){
					$sql="UPDATE `show` SET disable=1 WHERE id=" . GET_MYSQL_ESCAPED('showid');
					$afr=mysql_query($sql);
				}
				break;
			case 'en':
				if(isset($_GET['playerid'])){
					$sql="UPDATE player SET disable=0 WHERE id=" . GET_MYSQL_ESCAPED('playerid');
		 			$afr=mysql_query($sql);
				}
				elseif(isset($_GET['showid'])){
					$sql="UPDATE `show` SET disable=0 WHERE id=" . GET_MYSQL_ESCAPED('showid');
					$afr=mysql_query($sql);
				}
				break;
		}
		header("Location: " . $_SESSION['lastpage']);	
	}
	
	if(isset($_GET['commandnr'])){

		if(!isLoggedIn()) {
			header('Location: login.php');
			return;
		}
		
		switch($_GET['commandnr']){
			
			case 'setbackground':
				
				if(isset($_GET['mediaid']) && isset($_GET['showid'])){
					$sql="UPDATE `show` SET backgroundimage=" . $_GET['mediaid'] . " WHERE id=" . $_GET['showid'];
					$afr=mysql_query($sql);
				}
				break;
				
			case 'resetbackground':
				if(isset($_GET['showid'])){
					$sql="UPDATE `show` SET backgroundimage=0 WHERE id=" . $_GET['showid'];
					$afr=mysql_query($sql);
				}
			
			case 'del':
				if(isset($_GET['scheduleid'])){
					$sql="UPDATE `channelschedule` SET deleteflag=1 WHERE id = " . $_GET['scheduleid'];
					print($sql);
					$afr=mysql_query($sql);
				}
				break;
			case 'turnwizardon':
				$sql="UPDATE `users` SET tutorial=1 WHERE id=" . $loggeduser['id'];
				$afr=mysql_query($sql);
				break;
			case 'ashtp':
				if(isset($_GET['playerid']) && isset($_GET['showid'])){
					
						$sql="SELECT id,channel FROM player WHERE id=" . GET_MYSQL_ESCAPED('playerid');
						$result=mysql_query($sql);
						$nr=mysql_numrows($result);
						
						if($nr>0){
							
							$channel=mysql_result($result,0,"channel");
							
							$sql2="SELECT max(temporder) as maxorder FROM channelschedule WHERE channel=" . $channel;
							$result2=mysql_query($sql2);
							$nr2=mysql_numrows($result2);
							
							if($nr2=0){
								$neworder=1;
							}
							else{
								$neworder= 1 + mysql_result($result2,0,"maxorder");
							}
							
							$sql="INSERT INTO channelschedule(showid,channel,temporder) VALUES('" . GET_MYSQL_ESCAPED('showid') . "','" . mysql_escape_string($channel) . "','" . mysql_escape_string($neworder) . "')";
							$afr=mysql_query($sql);
							
						}
				}
				break;	
			case 'setserialized':

				if(isset($_GET['csvOrder'])){
					
					$orderarray=array();
					$orderarray = explode(',',$_GET['csvOrder']);
					
					foreach($orderarray as $i => $value){
						
						$j = intval($i) + 1;
						$sql="UPDATE channelschedule SET temporder=" . mysql_escape_string($j) . " WHERE id=" . mysql_escape_string($value);
						print($sql);
						$afr=mysql_query($sql);
					}
		
				}
				break;
			case 'setplaylistorder':
				if(isset($_GET['csvOrder'])){
					$orderarray=array();
					$orderarray=explode(',',$_GET['csvOrder']);
					foreach($orderarray as $i => $value){
						$j=intval($i) +1;
						$sql="UPDATE playlistitem SET `order`=" . $j . " WHERE id=" . mysql_escape_string($value);
						print($sql);
						$afr=mysql_query($sql);
					}
				}
			
			case 'saveschedule':
				
				if(isset($_GET['csvData'])){
					
					$dataarray=array();
					$dataarray=explode(',',$_GET['csvData']);
					
					
					$sql="UPDATE channelschedule SET Mon=" . mysql_escape_string($dataarray[3]) . ",Tue=" . mysql_escape_string($dataarray[4]) . 
					",Wed=" . mysql_escape_string($dataarray[5]) . ",Thu=" . mysql_escape_string($dataarray[6]) . ",Fri=" . mysql_escape_string($dataarray[7]) . 
					",Sat=" . mysql_escape_string($dataarray[8]) . ",Sun=" . mysql_escape_string($dataarray[2]) . ",am=" . mysql_escape_string($dataarray[9]) . 
					",pm=" . mysql_escape_string($dataarray[10]) . ",startdate='" . mysql_escape_string($dataarray[13]) . "-" . mysql_escape_string($dataarray[11]) . "-" . mysql_escape_string($dataarray[12]) .
					"',enddate='" . mysql_escape_string($dataarray[16]) . "-" . mysql_escape_string($dataarray[14]) . "-" . mysql_escape_string($dataarray[15]) . "',starttime='" .
					mysql_escape_string($dataarray[17]) . "',endtime='" . mysql_escape_string($dataarray[18]) ."', effect=" . mysql_escape_string($dataarray[1]) . 
					" WHERE id=" . mysql_escape_string($dataarray[0]);

					print($sql);
					
					$afr=mysql_query($sql);
					
				}
				
				break;
				
			case 'crrein':   //Create Region Instances
				
				if(isset($_GET['showid']) && isset($_GET['templateid'])){	
					
					$templateid=GET_MYSQL_ESCAPED('templateid');
					$sql="SELECT * FROM showregion WHERE template=" . mysql_escape_string($templateid) . " AND showid=" . GET_MYSQL_ESCAPED('showid');
					
					$result=mysql_query($sql);
					$nr=mysql_numrows($result);
					if($nr==0){
						
						$resx=0;
						$resy=0;
						
						$sql2="SELECT resx,resy FROM layout WHERE id=" . $templateid;
						$result2=mysql_query($sql2);
						$nr2=mysql_numrows($result2);
						if($nr2>0){
							$resx=mysql_result($result2,0,"resx");
							$resy=mysql_result($result2,0,"resy");
							
							$sql3="UPDATE `show` SET resx=" . $resx . ",resy=" . $resy . " WHERE id=" . GET_MYSQL_ESCAPED('showid');
							print($sql3);
							$afr=mysql_query($sql3);
						}
						
						
						$sql2="SELECT * FROM layoutregion WHERE layoutid=" . $templateid;
						$result2=mysql_query($sql2);
						$nr2=mysql_numrows($result2);
						
						while ($row = mysql_fetch_array($result2, MYSQL_ASSOC)) {

							srand(time());
							$verifier=(rand()%32767);
							$sql3="INSERT INTO showregion(showid,top,`left`,width,height,template,templateregion,verifier,mainmedia) VALUES(" . GET_MYSQL_ESCAPED('showid') . "," . mysql_escape_string($row['y']) . "," . mysql_escape_string($row['x']) . "," . mysql_escape_string($row['width']) . "," . mysql_escape_string($row['height']) . "," . mysql_escape_string($templateid) . "," . mysql_escape_string($row['id']) . "," . mysql_escape_string($verifier) . "," . $row['mainmedia'] .")";
							$afr=mysql_query($sql3);
							$sql3="INSERT INTO playlist(owner,verifier,playlistname,lastmodified) VALUES(" . $loggeduser['id'] . "," . $verifier . ",'Show " . mysql_escape_string($_GET['showid']) . " - tr: " . $row['id'] . "',now())"; 
							$afr=mysql_query($sql3);
							$sql3="SELECT max(id) as id FROM playlist WHERE verifier=" . $verifier . " AND owner=" . $loggeduser['id'];
							$result3=mysql_query($sql3);
							if($result3){
								$playlistid=mysql_result($result3,0,"id");
								$sql4="SELECT max(id) as id FROM showregion WHERE verifier=" . $verifier . " AND showid=" . $_GET['showid'];
								$result4=mysql_query($sql4);
								if($result4){
									$showregionid=mysql_result($result4,0,"id");
									$sql5="INSERT INTO regionplaylists(regionid,playlist) VALUES(" . $showregionid . "," . $playlistid . ")";
									$afr=mysql_query($sql5);
								}
							}
							$sql3="UPDATE `show` SET template=" . $templateid . " WHERE id=" . GET_MYSQL_ESCAPED('showid');
							$afr=mysql_query($sql3);
						}
						
						
					}
					else{
						
					}
				}
				break;
			case 'getshowregionid':
				if(isset($_GET['showid']) && isset($_GET['rid'])){
					$sql="SELECT template FROM `show` WHERE id=" . GET_MYSQL_ESCAPED('showid');
					$result=mysql_query($sql);
					if($result){
						$templateid=mysql_result($result,0,"template");
						$sql2="SELECT id FROM showregion WHERE showid=" . GET_MYSQL_ESCAPED('showid') . " AND template=" . $templateid . " AND templateregion=" . GET_MYSQL_ESCAPED('rid');
						$result2=mysql_query($sql2);
						$nr2=mysql_numrows($result2);
						if($nr2>0){
							print(mysql_result($result2,0,"id"));
						}
					}
				}
				break;
			case 'getrssurl':
				if(isset($_GET['urlid'])){
					$sql="SELECT URL FROM rss_sources WHERE id=" . GET_MYSQL_ESCAPED('urlid');
					$result=mysql_query($sql);
					$nr=mysql_numrows($result);
					if($nr>0){
						print(mysql_result($result,0,"URL"));
					}
				}
				break;
			case 'getregionplaylist':
				if(isset($_GET['regionid'])){
					$sql="SELECT playlist FROM regionplaylists WHERE regionid=" . GET_MYSQL_ESCAPED('regionid');
					$result=mysql_query($sql);
					$nr=mysql_numrows($result);
					if($nr>0){
						print(mysql_result($result,0,"playlist"));
					}
				}
				break;
			case 'delplitem':
				if(isset($_GET['plitid'])){
					$sql="UPDATE playlistitem SET deleteflag=1 WHERE id=" . GET_MYSQL_ESCAPED('plitid');
					$afr=mysql_query($sql);						
				}
				break;
			case 'addplitem':
				
				if(isset($_GET['playlistid']) && isset($_GET['type'])){
					

					$order=0;
					$sql="SELECT max(`order`) as od FROM playlistitem WHERE playlistid=" . GET_MYSQL_ESCAPED('playlistid');
					
					$result=mysql_query($sql);
					$nr=mysql_numrows($result);
					
					if($nr>0){
						$order=mysql_result($result,0,"od");
					}
					$order=$order+1;					
					$sql="INSERT INTO playlistitem(`order`,playlistid,nomediatype) VALUES(" . $order . "," . GET_MYSQL_ESCAPED('playlistid') . "," . GET_MYSQL_ESCAPED('type') . ")";
					$afr=mysql_query($sql);

				}
				break;
				
			case 'savephotodata':
				if(isset($_GET['ownerid']) && isset($_GET['name']) && isset($_GET['shared']) && isset($_GET['folder']) && isset($_GET['mediatype'])){
					$sql="INSERT INTO media(ownerid,postedon,filename,mediatype,shared,name) VALUES(" . GET_MYSQL_ESCAPED('ownerid') . ",now(),'" . mysql_escape_string($_GET['name']) . "'," . GET_MYSQL_ESCAPED('mediatype') . "," . GET_MYSQL_ESCAPED('shared') . ",'" . mysql_escape_string($_GET['name']) . "')";
					$afr=mysql_query($sql);
				}
				break;
			case 'set-library-folder':
				if(isset($_GET['folder'])) {
					$_SESSION['library_selected_folder'] = $_GET['folder'];
				}
			
				break;
			case 'save-media-data':
				if(isset($_GET['filename']) && isset($_GET['filesize']) && isset($_SESSION['library_selected_folder'])){

					
					$filename = $_GET['filename'];
					$filenamearr = explode('.', strrev($filename));
					$extension = (strrev($filenamearr[0]));
					
					$sql="SELECT mediatype FROM fileextensions WHERE extension='" . $extension . "'";
					$result=mysql_query($sql);
					$nr=mysql_numrows($result);
					if($nr>0){
						$mediatype=mysql_result($result, 0, "mediatype");
					}
					else{
						$mediatype=0;
					}
					
					if($_SESSION['library_selected_folder']=='Public'){
						$shared=1;
					}
					else{
						$shared=0;
					}
					
					$savefolder=$_SESSION['library_selected_folder'];
					
					if($savefolder=='All' || $savefolder=='Images' || $savefolder=='Video'){
						$savefolder='Main';
					}
					
					$sql="INSERT INTO media(ownerid,postedon,filename,mediatype,shared,name,filesize) VALUES('"  . $loggeduser['id'] . "',now(),'" . mysql_escape_string($savefolder) . "'," . mysql_escape_string($mediatype) . ",'" . mysql_escape_string($shared) . "','" . mysql_escape_string($filename) . "','" . $_GET['filesize'] . "')";
					
					$afr=mysql_query($sql);
				}
				break;
			case 'save-media-thumbnail':
					if (isset($_GET['filename'])) {
						require_once 'amazonS3/createThumbnails.php';
					
						$filename = $_GET['filename'];
					
						createOne($loggeduser['username'],$filename);				
					}
			
				break;
			case 'delmedia':
				if(isset($_GET['id'])){
					$sql="UPDATE media SET deleteflag=1 WHERE ownerid=" . $_SESSION['loggeduser']['id'] . " AND id=" . GET_MYSQL_ESCAPED('id');
					$afr=mysql_query($sql);
				}
				
				//require_once 'amazonS3/purgeDeleted.php';
				
				break;
				
			case 'saveRSS':
				if(isset($_GET['showregion']) && isset($_GET['SourceURL']) && isset($_GET['rssFeeds'])){
					$sql="UPDATE showregion SET rssid=" . GET_MYSQL_ESCAPED('rssFeeds') . ",rssurl='" . mysql_escape_string($_GET['SourceURL']) . "' WHERE id=" . GET_MYSQL_ESCAPED('showregion');
					$afr=mysql_query($sql);
				}
				break;
				
			case 'delfolder':
				if(isset($_GET['folder'])){
					$sql="DELETE FROM mediafolders WHERE CONCAT('mediafolder_',id)='" . mysql_escape_string($_GET['folder']) . "' AND owner=" . $loggeduser['id'];
					$afr=mysql_query($sql);
				}
			case 'save-playlist-item-duration':
				if(isset($_GET['duration']) && isset($_GET['playlistitem'])){
					$sql="UPDATE playlistitem SET duration=" . mysql_escape_string($_GET['duration']) . " WHERE id=" . mysql_escape_string($_GET['playlistitem']);
					$afr=mysql_query($sql);
				}
				break;
			
			case 'save-playlist-item-url':
				if(isset($_GET['url']) && isset($_GET['playlistitem'])){
					$sql="UPDATE playlistitem SET url='" . mysql_escape_string($_GET['url']) . "' WHERE id=" . mysql_escape_string($_GET['playlistitem']);
					$afr=mysql_query($sql);
					
				}
				break;
				
			case 'createfolder':
				if(isset($_GET['foldername'])){
					$sql="INSERT INTO mediafolders(folder,owner) VALUES('" . mysql_escape_string($_GET['foldername']) . "'," . $loggeduser['id'] . ")";
					$afr=mysql_query($sql);
				}
				break;
				
			
				
			case 'insert-item-playlist':
				if(isset($_GET['regionid']) && isset($_GET['mediaid'])){
					$sql="SELECT playlist FROM regionplaylists WHERE regionid=" . GET_MYSQL_ESCAPED('regionid');
					$result=mysql_query($sql);
					$nr=mysql_numrows($result);
					
					if($nr>0){
						$playlist=intval(mysql_result($result,0,"playlist"));
						//get next order number
						$sql2="SELECT max(`order`) as morder FROM playlistitem";
						$result2=mysql_query($sql2);
						$nr2=mysql_numrows($result2);
						if($nr2>0){						
							$ordernumber = intval(mysql_result($result2,0,"morder"));
							$ordernumber++;	
							
							
							$sql3="SELECT mediatype FROM media WHERE id=" . intval($_GET['mediaid']);
							$mediatype=0;
							$result3 = mysql_query($sql3);
							$nr3=mysql_numrows($result3);
																				
							if($nr3>0){
								$mediatype=mysql_result($result3,0,"mediatype");
							}
							
							$playlistduration=0;
							
							if($mediatype==2){
								$playlistduration=5;
							}
														
							
							$sql3="INSERT INTO playlistitem (mediaid, `order`, playlistid,duration) VALUES('" . intval($_GET['mediaid']) . "','" . $ordernumber ."','" . $playlist . "'," . $playlistduration . ")";					
 							$afr = mysql_query($sql3);	
							
	
						}
					}
				}
				break;
					
			}	
	}
		
	if(isset($_GET['infocmd'])){
		switch($_GET['infocmd']){
			case 'getlang':
				print($_SESSION['userlang']);
				break;
			case 'showuse':
				if(isset($_GET['showid'])){
					$sql="SELECT count(id) as qty FROM channelschedule WHERE showid=" . GET_MYSQL_ESCAPED('showid');
					$result=mysql_query($sql);
					$nr=mysql_numrows($result);
					if($nr>0){
						print(mysql_result($result,0,"qty"));
					}
					else{
						print 0;
					}
				}
				
				break;
			case 'mediause':
				if(isset($_GET['mediaid'])){
					$sql="SELECT count(id) as qty FROM playlistitem WHERE mediaid=" . GET_MYSQL_ESCAPED('mediaid');
					$result=mysql_query($sql);
					$nr=mysql_numrows($result);
					if($nr>0){
						print(mysql_result($result,0,"qty"));
					}
					else{
						print 0;
					}
				}
				break;
			case 'acc-count':
				if(isset($_GET['uname'])){
					$sql="SELECT count(id) as ct FROM users WHERE username='" . GET_MYSQL_ESCAPED('uname') . "'";
					$result=mysql_query($sql);
					$nr=mysql_numrows($result);
					if($nr>0){
						print(mysql_result($result,0,"ct"));
					}
				}
				break;
			case 'library-pages':
				if(isset($_GET['sw'])){
					$sql="SELECT count(id) AS cid FROM media WHERE ownerid=" . $loggeduser['id'] . " AND name LIKE '%" . GET_MYSQL_ESCAPED('sw') . "%'";
					$result=mysql_query($sql);
					$nr=mysql_numrows($result);
					if($nr>0){
						$pages=ceil(mysql_result($result,0,"cid") / 49);
						print($pages);
					}
				}
				break;
			case 'mini-library-pages':
				if(isset($_GET['sw'])){
					$sql="SELECT count(id) AS cid FROM media WHERE ownerid=" . $loggeduser['id'] . " AND name LIKE '%" . GET_MYSQL_ESCAPED('sw') . "%'";
					$result=mysql_query($sql);
					$nr=mysql_numrows($result);
					if($nr>0){
						$pages=ceil(mysql_result($result,0,"cid") / 21);
						print($pages);
					}
				}
				break;	
			case 'library-selected-folder':
				print($_SESSION['library_selected_folder']);
				break;
			case 'logged-userid':
				print($loggeduser.id);
				break;
			case 'files-in-folder':
				if(isset($_GET['folder'])){
					$sql="SELECT count(id) as files FROM media WHERE deleteflag=0 AND filename='" . GET_MYSQL_ESCAPED('folder') . "'";
					$result=mysql_query($sql);
					$nr = mysql_numrows($result);
					if($nr>0){
						print(intval(mysql_result($result,0,"files")));
					}
					else{
						print(0);
					}
				}
				break;
			case 'user-storage':
				$sql="SELECT sum(filesize) as occupied FROM media WHERE deleteflag=0 AND ownerid=" . $loggeduser['id'];
				$result=mysql_query($sql);
				$nr=mysql_numrows($result);
				if($nr>0){
					print(round(intval(mysql_result($result,0,"occupied"))/(1024*1024),2));
				}
				else{
					print(0);
				}
				break;
			case 'user-storage-total':
				$sql = "SELECT quota FROM `users` WHERE id=" . $loggeduser['id'];
				$result=mysql_query($sql);
				$nr=mysql_numrows($result);
				if($nr>0){
					print(mysql_result($result,0,"quota"));
				}
				else{
					print(0);
				}
				break;
			case 'get-region-template':
				if(isset($_GET['regionid'])){
					$sql="SELECT templateregion FROM showregion WHERE id=" . GET_MYSQL_ESCAPED('regionid');	
					$result=mysql_query($sql);
					$nr=mysql_numrows($result);
					
					if($nr!=0){
						print(mysql_result($result,0,"templateregion"));
					}
				}
				break;
			case 'get-region-type':
				if(isset($_GET['templateregion'])){
					$sql="SELECT regiontype FROM layoutregion WHERE id=" . GET_MYSQL_ESCAPED('templateregion');
					$result=mysql_query($sql);
					$nr=mysql_numrows($result);
					if($nr!=0){
						print(mysql_result($result,0,"regiontype"));
					}
				}
				break;
			case 'verpass':
				if(isset($_GET['user']) && isset($_GET['pwd'])){
					$sql="SELECT count(id) as uc FROM users WHERE username='" . mysql_escape_string($_GET['user']) . "' AND `password`='" . mysql_escape_string($_GET['pwd']) . "'";
					$result=mysql_query($sql);
			
					if(!$result) {
						print(0);
						return;
					}
					
					$nr=mysql_numrows($result);
					
					if($nr > 0 && mysql_result($result,0,"uc") == 0) {
						$sql="SELECT count(id) as uc FROM users WHERE username='" . mysql_escape_string($_GET['user']) . 
						                                      "' AND `password`=MD5('" . mysql_escape_string($_GET['pwd']) . "')";
						$result=mysql_query($sql);
						
						
						if(!$result) {
							print(0);
							return;
						}
						$nr=mysql_numrows($result);
					}
					
					if($nr>0){
						print(mysql_result($result,0,"uc"));
					}
					else{
						print(0);
					}
				}
				break;
				case 'tutorial':
					if(isset($_GET['tutstatus'])){
						$sql="SELECT tutorial FROM users WHERE id=" . $loggeduser['id'];
						$result=mysql_query($sql);
						$nr=mysql_numrows($result);
						if($nr!=0){
							print(mysql_result($result,0));
						}
					}
					break;
					
				case 'tutPos':
				
					$sql="SELECT tutorial FROM users WHERE id=" . $loggeduser['id'];
					$status=mysql_query($sql);
					$statusrow=mysql_fetch_array($status);
					if (!$statusrow) {
						return;
					}
					if ($statusrow['tutorial']=='0' && $_GET['tutPos']!='1') {
						return;
					}

					if (isset($_GET['tutPos'])){
						$sql="UPDATE users SET tutorial=". $_GET['tutPos'] ." WHERE id=" . $loggeduser['id'];
						$afr=mysql_query($sql);
					}
					break;
		}
	}
	
?>