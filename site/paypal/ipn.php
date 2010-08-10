<?php

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

$fp = fsockopen ('ssl://sandbox.www.paypal.com', 443, $errno, $errstr, 30);



if (!$fp) {
// HTTP ERROR
} else {
fputs ($fp, $header . $req);
while (!feof($fp)) {
$res = fgets ($fp, 1024);
if (strcmp ($res, "VERIFIED") == 0) {
// check the payment_status is Completed
// check that txn_id has not been previously processed
// check that receiver_email is your Primary PayPal email
// check that payment_amount/payment_currency are correct
// process payment


// echo the response
echo "The response from IPN was: <b>" .$res ."</b><br><br>";

//loop through the $_POST array and print all vars to the screen.

foreach($_POST as $key => $value){

        echo $key." = ". $value."<br>";



}


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

