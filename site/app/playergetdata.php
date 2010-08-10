<?php

	require_once('../util/session.php');
	require_once('../util/dbconfig.php');


	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");

	if(isset($_SESSION['player'])){
		if(isset($_GET['data'])){
			switch($_GET['data']){
				case 1:
					$sql="SELECT * FROM player WHERE owner=" . $_SESSION['player']['userid'];
					$result=mysql_query($sql);

					if($result){
						$xml="<players>\n";
						$is_pro = false;
						while ($player_row = mysql_fetch_array($result)) {
							$xml.="\t" . '<player name="' . $player_row["playername"] . '" pro="' . $player_row["pro"] . '">' . "\n";
							$xml.="\t\t<id>" . $player_row["id"] . "</id>\n";
							$xml.="\t\t<channel>" . $player_row["channel"] . "</channel>\n";
							$xml.="\t\t<createdon>" . $player_row["createdon"] . "</createdon>\n";
							$xml.="\t\t<inactive>" . $player_row["inactive"] . "</inactive>\n";
							$xml.="\t\t<venuetype>" . $player_row["venuetype"] . "</venuetype>\n";
							$xml.="\t" . '</player>' . "\n";
							
							if($player_row["pro"]) {
								$is_pro = true;
							}
						}
						
						if($is_pro) {
							$xml .= "<user isPro=\"true\"/>";
						} else {
							$xml .= "<user isPro=\"false\"/>";
							
						}
						
						$xml.="</players>";
					}
					break;
				case 2:
					$sql="SELECT * FROM users WHERE id=" . $_SESSION['player']['userid'];
					$result=mysql_query($sql);

					if($result){
						$row = mysql_fetch_array($result);
						
						$xml="<user>\n";
						$xml.="\t<firstname>" . $row["firstname"] . "</firstname>\n";
						$xml.="\t<lastname>" . $row["lastname"] . "</lastname>\n";
						$xml.="\t<accounttype>" . $row["accounttype"] . "</accounttype>\n";
						$xml.="\t<address1>" . $row["address1"] . "</address1>\n";
						$xml.="\t<address2>" . $row["address2"] . "</address2>\n";
						$xml.="\t<city>" . $row["city"] . "</city>\n";
						$xml.="\t<stateprovince>" . $row["stateprovince"] . "</stateprovince>\n";
						$xml.="\t<postalcode>" . $row["postalcode"] . "</postalcode>\n";
						$xml.="\t<country>" . $row["country"] . "</country>\n";
						$xml.="\t<businessname>" . $row["businessname"] . "</businessname>\n";
						$xml.="</user>";
					}
					break;	
			}
			header('Content-Type: text/xml');
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo $xml;	

			
		}
		else{
			echo '?';
		}	
	}
	else{
		echo 'NL';
	}

?>