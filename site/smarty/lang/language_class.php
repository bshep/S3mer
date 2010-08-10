<?php
$xml = array();
$xml_file = "";

function smarty_t($params, $string, &$smarty) {
   foreach($params as $key => $value) {
      $params["%$key"] = $value;
      unset($params[$key]);
   }
   
   $file = $smarty->get_template_vars("filename")."_";
   
   print(t($string, $params, $file));
}

function t($string,	$args =	array(), $file ) {
  global $xml, $xml_g,	$userlang, $xml_file;
  global $dbserver,$dbuser,$dbpassword,$db;
  
	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	

	$sql="SELECT content FROM contents WHERE language='{$userlang}' AND contentname='" . $string . "';";
	

	
	$result=mysql_query($sql);
	$nr=mysql_numrows($result);
	
	
	
	if($nr!=0){
		//return htmlentities(mysql_result($result,0,"content"));
		 return mysql_result($result,0,"content");
	}

   return strtr($string, $args);
   
}

function getChildren($vals, &$i) {
   $children = array();
   
   if(!isset($vals[$i]['attributes'])) {
      $vals[$i]['attributes'] = "";
   }
   
   while(++$i < count($vals)) {
      if(!isset($vals[$i]['attributes'])) {
         $vals[$i]['attributes'] = "";
      }

      if(!isset($vals[$i]['value'])) {
         $vals[$i]['value'] = "";
      }

      switch ($vals[$i]['type']) {
      case 'complete':
         array_push($children, array('tag' => $vals[$i]['tag'], 'attributes' => $vals[$i]['attributes'], 'value' => $vals[$i]['value']));
         break;
      case 'open':
         array_push($children, array('tag' => $vals[$i]['tag'], 'attributes' => $vals[$i]['attributes'], 'children' => getChildren($vals, $i)));
         break;
      case 'close':
         return $children;
         break;
      }
   }

   return $children;
}

function getXmlTree($file) {
   $data = implode("", file($file));
   $xml  = xml_parser_create();
   xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE, 1);
   xml_parse_into_struct($xml, $data, $vals, $index);
   xml_parser_free($xml);

   $tree = array();
   $i = 0;
   array_push($tree, array('tag' => $vals[$i]['tag'], 'attributes' => $vals[$i]['attributes'], 'children' => getChildren($vals, $i)));

   return $tree;
}
?>