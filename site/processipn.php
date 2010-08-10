<?php

	global $smarty;
	global $userlang;

	require_once('util/application.php');

	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	
	$req = 'cmd=_notify-validate';

	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}

	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

	$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

	if (!$fp) {
		// HTTP ERROR
	} 
	else {
		fputs ($fp, $header . $req);
		while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 0) {
			// check the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment
			//loop through the $_POST array and print all vars to the screen.
			$myFile = "testFile.txt";
			$fh = fopen($myFile, 'a+') or die("can't open file");

			if(isset($_POST)){
				
				//check if database is complete in order to accept transaction information
				
				foreach($_POST as $key => $value){
					$sql="SELECT count(column_name) as columnexists FROM information_schema.`COLUMNS` where table_name = 'paypal_transactions' and column_name='" . $key . "'";
					$result=mysql_query($sql);
					
					$columnexists=mysql_result($result,0);
					
					if($columnexists == 0){
						
						$sql = "alter table paypal_transactions add column " . $key . " varchar(255);";
						mysql_query($sql);
						
					}
					
					
					
				}
				
				$stringData = '';
				$fieldlist='';
				$datalist='';
				
				if(isset($_POST['payment_date'])){
					$timestr=strtotime($_POST['payment_date']);
					$transdt=strftime("%Y-%m-%d %H:%M:%S",$timestr);	
				}
				
				if(isset($_POST['subscr_date'])){
					$timestr=strtotime($_POST['subscr_date']);
					$transsub=strftime("%Y-%m-%d %H:%M:%S",$timestr);
				}
				
				foreach($_POST as $key => $value){
					
					$stringData .= "[" . $key . "] - " . $value . "\n";
					
					if($key=='payment_date'){
						$value = $transdt;
					}
					
					if($key=='subscr_date'){
						$value = $transsub;
					}
										
					if(strlen($fieldlist)==0){
						$fieldlist = $key;						
						$datalist .= "'" . $value . "'";
					}
					else{
						$fieldlist .= ',' . $key;						
						$datalist .= ",'" . $value . "'";
					}
					
										
				}
				
				
				if(isset($_POST['payment_date'])){
					
					$stringData .= $_POST['payment_date'] . " - " . $transdt;	
					
				}
								
				$sql="INSERT INTO paypal_transactions(" . $fieldlist . ") VALUES(" . $datalist . ")";
				
				mysql_query($sql);
						
				$stringData .= "\n" . $sql;
			}
			
			
			fwrite($fh,print_r($_POST, true)."\n");
			fwrite($fh, $stringData);
			fclose($fh);
		}
	
		else if (strcmp ($res, "INVALID") == 0) {
		// log for manual investigation
		// echo the response
		echo "The response from IPN was: <b>" .$res ."</b>";

	  	}

	}
	fclose ($fp);
}
	


?>