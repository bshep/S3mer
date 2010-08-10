<?php
	
	require_once('../util/session.php');
	require_once('../util/dbconfig.php');

	if(!isset($_GET['playerid'])) {
		die('Error #1: Invalid Player');
	}


	$mediaurl = "http://media1.s3mer.com.s3.amazonaws.com/";
	$heartbeaturl= "http://www.s3mer.com/heartbeat.php";
	$playerid=mysql_escape_string($_GET["playerid"]);
	
	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	
	$sql = "SELECT player.channel,channel.mediaurl,channel.configurl,player.pro FROM player INNER JOIN channel on channel.id=player.channel where player.id= " . $playerid . " AND player.owner = '" . $_SESSION['player']['userid'] . "'";
	
	$XML = "";
	
	$result = mysql_query($sql);	

	if(!$result) {
		die('Error #2');
	}
	$num=mysql_numrows($result);
	
	
	// RSS and Bakcgrounds dont have playlists or media, so we need to store info about these regions so we can generate the 
	// playlists and regions later.
	$playlist_rss = array();
	$playlist_background = array(); 
	$playlist_timedate = array();
	$playlist_podcast = array();
			
	$insider = "0";
	
	if ($num!=0){	//player found proceding to get xml configuration file for player
		$isPro = mysql_result($result,0,"pro");
		
		if($isPro == "1") {
			// Esto calcula el pro expiration date
			$sql2 = "SELECT ADDDATE(max(payments_received.`date`), INTERVAL 1 MONTH) as prodate, users.insider FROM users LEFT OUTER JOIN payments_received ON users.id = payments_received.userid WHERE users.id =".$_SESSION['player']['userid']." GROUP BY users.id";
			$result2 = mysql_query($sql2);

			$lastpayment="";
			// $XML .= $sql2."\n";

			if($result2!=null){
				$lastpayment=mysql_result($result2,0,"prodate");
				$insider = mysql_result($result2,0,"insider");
				if($insider == "1") { // An isider has no expiration date so just give it a week
					$lastpayment = strtotime("+1 week",strtotime(date("Y-m-d", time())));
					// echo "insider";

				} else {
					$lastpayment=strtotime($lastpayment);			
				}
			}
		}	

		//$mediaurl=mysql_result($result,0,"mediaurl");
		
		$configurl=mysql_result($result,0,"configurl");
		$channel=mysql_result($result,0,"channel");

		$XML.="\n<channel>\n";
		$XML.="\t<config isPro=\"" . $isPro . "\">\n";
		$XML.="\t\t<mediaurl>" . $mediaurl . "</mediaurl>\n";
		if(strlen($configurl)!=0){
			$XML.="\t\t<configurl>" . $configurl . "</configurl>\n";	
		}
		$XML.="\t\t<heartbeaturl>" . $heartbeaturl . "</heartbeaturl>\n";
		// $XML.="\t\t<expirationDate>" . date("U") . "</expirationDate>\n";
		if($isPro == "1") {
			$XML.="\t\t<proexpirationDate asText=\"".date("Y-m-d",$lastpayment)."\">" . $lastpayment . "</proexpirationDate>\n";
		}
		$XML.="\t</config>\n";
		

		$sql ='SELECT showid,`show`.showname,`show`.resx,`show`.resy, `show`.backgroundimage, `show`.clock,mon,tue,wed,thu,fri,sat,sun,startdate,enddate,starttime,endtime,am,pm,channelschedule.id FROM channelschedule  inner join `show` on `show`.id=channelschedule.showid WHERE channel=' . $channel;
		$show_list = mysql_query($sql);

		while( $show_row = mysql_fetch_array($show_list) ) {
			$showid= $show_row["showid"];
			
			if( $show_row["resx"] == 0 &&  $show_row["resy"] == 0) {
				$show_row["resx"] = 1280;
				$show_row["resy"] = 720;
			}
			
			
			$XML.= "\t<show id=\"sh" . $showid .
				"\" width=\"" . $show_row["resx"] .
				"\" height=\"" . $show_row["resy"] .
				"\" backgroundimage=\"" . $show_row["backgroundimage"] .
				"\">\n";
				
		
			
			
			$sql2="SELECT showregion.id, showregion.top, showregion.`left`, showregion.width, showregion.height, showregion.fontcolor, showregion.bgcolor1, showregion.rssid, showregion.rssurl, regiontype,url,rsscolor, showregion.mainmedia, if(regiontype=5,concat('" . $mediaurl . "' ,'user/', users.username , '/' , media.name),null) as backgroundurl FROM showregion INNER JOIN layoutregion ON layoutregion.id = showregion.templateregion INNER JOIN `show` on `show`.id = showregion.showid LEFT JOIN media on media.id = `show`.backgroundimage LEFT JOIN users ON users.id=media.ownerid WHERE showid='" . $showid . "' ORDER BY layoutregion.zindex ASC";
			

			$region_list=mysql_query($sql2);
			
			if(!$region_list) {
				echo mysql_error();
				return;
			}

			while( $region_row = mysql_fetch_array($region_list) ) {
				if($region_row['regiontype'] == 4) {
					if( $show_row["clock"] == "0") {
						continue;
					}
				}
				
				$regionid = $region_row["id"];
				
				$XML.="\t\t<region id=\"rg" . $regionid . 
					"\" top=\"" . $region_row["top"] . 
					"\" left=\"" . $region_row["left"] . 
					"\" width=\"" . $region_row["width"] . 
					"\" height=\"" . $region_row["height"] . 
					"\" fontcolor=\"" . $region_row["fontcolor"] . 
					"\" bgcolor1=\"" . $region_row["bgcolor1"] . 
					"\" mainmedia=\"" . $region_row["mainmedia"] .
					"\">\n";
					
				$sql3="SELECT regionid,playlist FROM regionplaylists WHERE regionid=" . $regionid;
				$region_playlist_list = mysql_query($sql3);

				while( $region_playlist_row = mysql_fetch_array($region_playlist_list) ) {
					$XML.="\t\t\t<playlist id=\"pl" . $region_playlist_row["playlist"] . 
							"\"></playlist>\n";
					
					if($region_row['rssid'] != 0 || $region_row['rssurl'] != '') {
						$playlist_rss[$region_playlist_row["playlist"]] = array();
						$playlist_rss[$region_playlist_row["playlist"]]['rssid'] = $region_row['rssid'];
						$playlist_rss[$region_playlist_row["playlist"]]['rssurl'] = $region_row['rssurl'];
						$playlist_rss[$region_playlist_row["playlist"]]['color'] = $region_row['rsscolor'];
					}
					
					if($region_row['url'] != '' && $region_row['regiontype'] == 5) {
						$playlist_background[$region_playlist_row["playlist"]] = array();
						
						if($region_row['backgroundurl']!=null){
							$playlist_background[$region_playlist_row["playlist"]]['url'] = $region_row['backgroundurl'];
						}
						else{
							$playlist_background[$region_playlist_row["playlist"]]['url'] = $region_row['url'];
						}
						
						
						
						//$XML .= "URL\n";
					}
					
					if($region_row['regiontype'] == 4) {
							$playlist_timedate[$region_playlist_row["playlist"]] = 4;
					}
					
				}
				$XML.="\t\t</region>\n";
			}
			$XML.="\t</show>\n";
			$XML.="\t<timeconditions id=\"tc". $show_row['id']. "\" show_id=\"sh" .$show_row["showid"] . "\">";
			$XML.="\t<condition type=\"daterange\" startdate=\"" . $show_row['startdate'] . "\" enddate=\"" . $show_row['enddate'] . "\"></condition>";
			$XML.="\t<condition type=\"timerange\" starttime=\"" . $show_row['starttime'] . "\" endtime=\"" . $show_row['endtime'] . "\"></condition>";
			$XML.="\t<condition type=\"dayweek\" day=\"Sun\" value=\"" . $show_row['sun'] . "\"></condition>";
			$XML.="\t<condition type=\"dayweek\" day=\"Mon\" value=\"" . $show_row['mon'] . "\"></condition>";
			$XML.="\t<condition type=\"dayweek\" day=\"Tue\" value=\"" . $show_row['tue'] . "\"></condition>";
			$XML.="\t<condition type=\"dayweek\" day=\"Wed\" value=\"" . $show_row['wed'] . "\"></condition>";
			$XML.="\t<condition type=\"dayweek\" day=\"Thu\" value=\"" . $show_row['thu'] . "\"></condition>";
			$XML.="\t<condition type=\"dayweek\" day=\"Fri\" value=\"" . $show_row['fri'] . "\"></condition>";
			$XML.="\t<condition type=\"dayweek\" day=\"Sat\" value=\"" . $show_row['sat'] . "\"></condition>";
			$XML.="\t<condition type=\"ampm\" AM=\"" .  $show_row['am'] ."\" PM=\"" . $show_row['pm'] . "\"></condition>";
			$XML.="\t</timeconditions>";
			
			
						
		}
		$sql='SELECT DISTINCT playlist from regionplaylists INNER JOIN showregion on regionplaylists.regionid=showregion.id INNER JOIN channelschedule ON showregion.showid=channelschedule.showid WHERE channelschedule.channel=' . $channel;
		$playlist_list=mysql_query($sql);
		
		while( $playlist_row = mysql_fetch_array($playlist_list) ) {
			$plid = $playlist_row["playlist"];
			
			
			$XML.="\t<playlist id=\"pl" . $plid . "\">\n";

			if (array_key_exists($plid,$playlist_rss)) {
				if($playlist_rss[$plid]['rssid']) {
					$sql = "SELECT * FROM rss_sources WHERE id = '" . $playlist_rss[$plid]['rssid'] . "'";
					$rss_result = mysql_query($sql);
					
					
				
					if($rss_result) {
						$rss_row = mysql_fetch_array($rss_result);
						
						if($playlist_rss[$plid]['color'] == "000000") {
							$logo = $rss_row['logo_dark'];
						} else {
							$logo = $rss_row['logo_light'];
						}
						
						
						$XML .= "<playlistitem type=\"rss\" rsscolor=\"". $playlist_rss[$plid]['color'] ."\" logoUrl=\"". $logo ."\">".$rss_row['URL']."</playlistitem>";
					}
					
				} else {
					if($playlist_rss[$plid]['color'] == "000000") {
						$logo = "http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf";
					} else {
						$logo = "http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf";
					}
					
					
					$XML .= "<playlistitem type=\"rss\" rsscolor=\"". $playlist_rss[$plid]['color'] ."\" logoUrl=\"". $logo ."\"
					>". $playlist_rss[$plid]['rssurl'] ."</playlistitem>";
				}
			} elseif (array_key_exists($plid,$playlist_background)){
				$XML .= "<playlistitem duration=\"0\" type=\"image\" >". $playlist_background[$plid]['url'] ."</playlistitem>";
			} elseif (array_key_exists($plid,$playlist_timedate)) {
			    // Check the timeformat from the user account
			    $tf_sql = "SELECT timeformat FROM users WHERE id='".$_SESSION['player']['userid']."'";
			    $tf_result = mysql_query($tf_sql);
			    $timeformat = mysql_fetch_row($tf_result);
			    if($timeformat[0]=='2'){
			        $theformat = "24hr";
			    } else {
			        $theformat = "12hr";
			    }
				$XML .= "\t\t<playlistitem duration=\"0\" type=\"timedate\" format=\"".$theformat."\"></playlistitem>\n";
			} else { 
				$sql2="SELECT media.name,mediatype.mediatype,duration,nomediatype,url,media.id FROM playlistitem LEFT OUTER JOIN media ON media.id=playlistitem.mediaid LEFT OUTER JOIN mediatype ON mediatype.id=media.mediatype WHERE playlistitem.playlistid=" . $plid . 
				" AND playlistitem.deleteflag = 0 ORDER BY playlistitem.order";
				$media_list = mysql_query($sql2);
				
				while( $media_row = mysql_fetch_array($media_list) ) {
					if($media_row['nomediatype'] != 0) {
							
						switch($media_row['nomediatype']) {
							case 1: //podcast
								$XML .= "\t\t<playlistitem duration=\"0\" type=\"podcast\">" . $media_row['url'] . "</playlistitem>\n";
								break;
							case 3: //livevideo
								$XML .= "\t\t<playlistitem duration=\"". $media_row["duration"] ."\" type=\"livevideo\">" . $media_row['url'] . "</playlistitem>\n";
								break;
							case 2: //html
								$XML .= "\t\t<playlistitem duration=\"". $media_row["duration"] ."\" type=\"url\">" . $media_row['url'] . "</playlistitem>\n";
								break;
							case 4: //timedate
								$XML .= "\t\t<playlistitem duration=\"0\" type=\"timedate\"></playlistitem>\n";
								break;
							default:
								$XML .= "\t\t<playlistitem duration=\"0\" type=\"UNKNOWN REGION\"></playlistitem>\n";
						}
					} else {
						switch($media_row["mediatype"]) {
/*
	
							case "rss":
								$XML.="\t\t<playlistitem logoUrl=\"http://www.s3mer.com/media/logos/cnn-b.swf\" duration=\"" . 
										$media_row["duration"] . 
									'" type="' . $media_row["mediatype"] . '">' . 
										$media_row["filename"] . 
									"</playlistitem>\n";
								break;
							case "timedate":
								$XML .= "\t\t<playlistitem duration=\"0\" type=\"timedate\"></playlistitem>\n";
								break;
*/
							default:
								$XML.="\t\t<playlistitem duration=\"" . $media_row["duration"] . 
									"\" type=\"" . $media_row["mediatype"] . "\" mediaid=\"" . $media_row['id']  . '">' . 
										"http://media1.s3mer.com.s3.amazonaws.com/" . 'user/' .  $_SESSION['player']['username'] . '/' . $media_row["name"] . 
									"</playlistitem>\n";
								break;
						}
					}
				}
			} 
			$XML.="\t</playlist>\n";
		}
		$XML .= "\t<timestamp>" . date("c") . "</timestamp>\n";
		$XML.= "</channel>\n";
		
		//This acts as a nonce on the message
		
		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>';

		$out = $XML;

		$out = "\n<data>\n";
		$out .= "\t<content>";	
		$out .= signConfig($XML);
		$out .= "</content>\n";
		$out .= "\t<timestamp>";	
		$out .= base64_encode(md5($XML));
		$out .= "</timestamp>\n";
		
		if($insider == "1") {
			$out .= "<insider />";
		}
		
		$out .= "</data>";
	
		echo $out;
		
		//echo ord("d")." ".ord("N");
		
		$sql="UPDATE player SET dirty=0 WHERE id=" . $playerid;
		$affr=mysql_query($sql);
		
	}
	else{			//no player found with specs configured
		die('Error #3: Unauthorized' );
	}

	
	function signConfig($config)
	{
        $key = "disuri301293rfbc,nWou1309rjfbckvjh085-4cnkn091()*&*%&%$()";
        $out = "";
        
        $key = mutateKey($key,md5($config)); 
        
        $out = simpleCrypt($config, $key);
        
        return base64_encode($out);
	}
	
	// Mutates the key based on the config file to avoid repetition attacks
	function mutateKey($key, $md5) {
        return simpleCrypt($key, $md5);
	}
	
	function simpleCrypt($data, $key) {
		$ret = "";
		
		for ($i=0; $i < strlen($data); $i++) { 
			$ret .= chr(ord($key[$i % strlen($key)]) ^ ord($data[$i]));
		}
		
        return $ret;
	}
		
?>
