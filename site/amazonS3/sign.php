<?php


require_once('util/session.php');
function s3_signature() {
    //$bucket = "bruce-backup.sheplan.com";
    $bucket = "media1.s3mer.com";

    if(!isset($_SESSION['loggeduser'])) {
      return '0';
    }

    $username = $_SESSION['loggeduser']['username'];

    $key = "S3_KEY_SECRET";

	$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
	$expirationdate = date('Y-m-d',$tomorrow) . 'T12:00:00.000Z';


    $policy = '{ "expiration": "' . $expirationdate .  '",
      "conditions": [
        {"bucket": "'.$bucket.'"},
        ["starts-with", "$key", "user/'.$username.'/"],
        {"acl": "public-read"},
        ["starts-with", "$Content-Type", ""],
        ["starts-with", "$Filename", ""],
        ["starts-with", "$success_action_status", ""],
        ["starts-with", "$x-amz-meta-tag", ""]
      ]
    }';

    $ret = "";

    $ret .= "\"key\": \"user/$username/\",";
    $ret .= "\"acl\": \"public-read\",";
    $ret .= "\"AWSAccessKeyId\": \"S3_KEY_ID\",";
    $ret .= "\"Policy\": \"".base64_encode($policy)."\",";
    $ret .= "\"Signature\": \"".base64(hasher(base64_encode($policy), $key))."\",";
    $ret .= "\"Content-Type\": \"image/jpeg\",";
    $ret .= "\"x-amz-meta-tag\": \"testing\",";
    $ret .= "\"success_action_status\": \"201\"";
    
    return $ret;
}

function hasher($data, $key)
{
    // Algorithm adapted (stolen) from http://pear.php.net/package/Crypt_HMAC/)
    if(strlen($key) > 64)
            $key = pack("H40", sha1($key));
    if(strlen($key) < 64)
            $key = str_pad($key, 64, chr(0));
    $ipad = (substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
    $opad = (substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));
    return sha1($opad . pack("H40", sha1($ipad . $data)));
}

function base64($str)
{
    $ret = "";
    for($i = 0; $i < strlen($str); $i += 2)
            $ret .= chr(hexdec(substr($str, $i, 2)));
    return base64_encode($ret);
}

if(isset($_GET['uploader'])) {
	print s3_signature();
	return;
}

//DEBUGGING STUFF
if(isset($_GET['test'])) {
  $bucket = "bruce-backup.sheplan.com";

  if(!isset($_SESSION['loggeduser'])) {
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    return;
  }

  $username = $_SESSION['loggeduser']['username'];

  $key = "S3_KEY_SECRET";

	$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
	$expirationdate = date('Y-m-d',$tomorrow) . 'T12:00:00.000Z';
	
 
  $policy = '{ "expiration": "' . $expirationdate  . '",
    "conditions": [
      {"bucket": '.$bucket.'},
      ["starts-with", "$key", "user/'.$username.'/"],
      {"acl": "public-read"},
      ["starts-with", "$Content-Type", ""],
      ["starts-with", "$x-amz-meta-tag", ""]
    ]
  }
  ';

  if(isset($_GET['debug'])) {
    echo "<pre>";
    echo "Base64: ".$policy."\n";
    echo "Base64: ".base64_encode($policy)."\nend\n";
    echo "Base64: ".base64(hasher(base64_encode($policy), $key))."\nend\n";
    echo "</pre>";
  } else {
    if(isset($_GET['xml'])) {
      header('Content-Type: text/xml');
      echo '<?xml version="1.0" encoding="UTF-8"?>';
      echo "<aws_post>";
      echo "<policy>";
      echo base64_encode($policy);
      echo "</policy>";
      echo "<signature>";
      echo base64(hasher(base64_encode($policy), $key));
      echo "</signature>";
      echo "</aws_post>";
    } else {
      echo "\"policy\": \"".base64_encode($policy)."\",\n";
      echo "\"signature\": \"".base64(hasher(base64_encode($policy), $key))."\",\n";
      echo "\"acl\": \"public-read\",\n";
      echo "\"key\": \"user/$username/{\$filename}/\"";
    }
  }
}


?>