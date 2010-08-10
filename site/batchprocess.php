<?php

	$playerprice=60.00;
	
	require_once('util/application.php');
	
	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	
	$sql="SELECT batchrunning FROM batchcontrol WHERE id=1";
	$result=mysql_query($sql);
	$batchrunning=mysql_result($result,0);
	
	//print("running:" . $batchrunning . "<br>");
	
	
	if($batchrunning==0){
		
		$sql="UPDATE batchcontrol SET batchrunning=1 WHERE id=1";
		mysql_query($sql);
		
		$sql="SELECT * FROM paypal_transactions WHERE processed=0";
		$result=mysql_query($sql);
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			//print('processing id: ' . $row['id'] . "<br>");
			if($row['receiver_email']=='payments@s3mer.com'  && $row['business']=='payments@s3mer.com'){
				//print('receiver verified id: ' . $row['id'] . "<br>");
				
				switch($row['txn_type']){
					case 'subscr_payment':
						//print('processing payment<br>');
						$proplayers = $row['mc_gross'] / $playerprice;
						//print('proplayers paid: ' . $proplayers . "<br>");
						$sql="INSERT INTO payments_received(userid,`date`,amount,transaction_id) values(" . $row['custom'] . ",now()," . $row['mc_gross'] .  ",'" . $row['txn_id'] . "')";
						//print($sql . "<br>");
						mysql_query($sql);
						$sql="SELECT count(id) as existing FROM users WHERE subscriptionid='" . $row['subscr_id'] . "'";
						//print($sql . "<br>");
						$result2=mysql_query($sql);
						$existingsubscription = mysql_result($result2,0);
						//print('existing subscription: ' . $existingsubscription . "<br>");
						if($existingsubscription == 0){
							$space=$proplayers*5120;
							$sql = "UPDATE users SET prosubscriptions=" . $proplayers . ", subscriptionid='" . $row['subscr_id'] . "', quota=" . $space . " WHERE id=" . $row['custom'];
							//print($sql . "<br>");
							mysql_query($sql);
							$playerstoadd = $proplayers;
							while($playerstoadd>0){
								
								
								
								$sql="INSERT INTO channel(channelname,owner,createdon) VALUES('Pro'," .$row['custom'] . ",now())";
								mysql_query($sql);
								$channelid = mysql_insert_id();
								
								
								
								$sql="INSERT INTO player(owner,createdon,inactive,pro,np,channel) VALUES(" . $row['custom'] . ",now(),0,1,0," . $channelid .")";
								mysql_query($sql);
								
									
								
								
								//print($sql . "<br>");
								$playerstoadd--;
							}							
						}
						
						break;
						
					case 'subscr_cancel':
					
						$userid=0;
						
						$sql="SELECT id FROM users WHERE subscriptionid='" . $row['subscr_id'] . "'";
						//print($sql . "<br>");
						$result2=mysql_query($sql);
						if(mysql_num_rows($result2)!=0){
							$userid=mysql_result($result2,0);	
						}
						
						if($userid!=0){
						
							$sql="UPDATE users SET prosubscriptions=0, quota=250 WHERE id=" . $userid;
							//print($sql . "<br>");
							mysql_query($sql);						

							$sql="DELETE FROM player WHERE owner=" . $userid . " AND pro=1";
							//print($sql . "<br>");
							mysql_query($sql);
							
							$sql="UPDATE users SET prosubscriptions=0, subscriptionid=NULL";
							//print($sql . "<br>");
							mysql_query($sql);
								
						}
						
						break;
					
					case 'subscr_modify':
						$userid=0;
						$actualprosubscriptions=0;
						$sql="SELECT id, prosubscriptions FROM users WHERE subscriptionid='" . $row['subscr_id'] . "'";
						$result2=mysql_query($sql);
						//print($sql . "<br>");
						
						if(mysql_num_rows($result2)!=0){
							$userid=mysql_result($result2,0,0);	
							$actualprosubscriptions=mysql_result($result2,0,1);
						}
						
						if($userid!=0){
							
							$proplayers = $row['mc_amount3'] / $playerprice;
							
							$space=$proplayers*5120;
						
							$sql="UPDATE users SET prosubscriptions=" . $proplayers . ",quota=" . $space . " WHERE id=" . $userid;
							//print($sql . "<br>");
							mysql_query($sql);						

							if($proplayers<$actualprosubscriptions){
								$sql="DELETE FROM player WHERE owner=" . $userid . " AND pro=1 AND prodelete=1";
								//print($sql . "<br>");
								mysql_query($sql);	
							}
							
							if($proplayers>$actualprosubscriptions){
								$playerstoadd=$proplayers-$actualprosubscriptions;
								while($playerstoadd>0){
									
									
									$sql="INSERT INTO channel(channelname,owner,createdon) VALUES('Pro'," .$row['custom'] . ",now())";
									mysql_query($sql);
									$channelid = mysql_insert_id();
									
									
									
									$sql="INSERT INTO player(owner,createdon,inactive,pro,np,channel) VALUES(" . $row['custom'] . ",now(),0,1,0," . $channelid .")";
									mysql_query($sql);
									
									
									//print($sql . "<br>");
									$playerstoadd--;
								}
							}
							
								
						}
						break;
						
						
				}
				
				$sql="UPDATE paypal_transactions SET processed=1 WHERE id=" . $row['id'];
				mysql_query($sql);
				
				//print($sql . "<br>");
			}
			
		}
		
		$sql="UPDATE batchcontrol SET batchrunning=0 WHERE id=1";
		mysql_query($sql);	
	}
	
?>