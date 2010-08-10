<?

	if(!function_exists("scandir")) {    
		function scandir($dir) {
			$dh  = opendir($dir);
			while (false !== ($filename = readdir($dh))) {
				$files[] = $filename;
			}

			sort($files);
			
			return $files;
		}
	}

	if(!function_exists("file_put_contents")) {    
		function file_put_contents($file, $string) {
			$f=fopen($file, 'w');
			fwrite($f, $string);
			fclose($f);
		}
	}


    $target_dir = "../../locale";
    
    $target_languages = array( array("es","Espanol"),
								array("it","Italiano"),
								array("pt","Portugues"),
                               array("en","English"));
                               
	$default_language = "en";
    
    $template_dir = "../../templates";
    
    $template_files = array();
    
    if( false == $template_files = scandir($template_dir) ) {
        die("Could not read directory: $template_dir\n");
    }
    
    foreach ($template_files as $file) {
        if( preg_match("/^.*\.tpl$/", $file) ){
            $locStrings = getLocalizableStrings($template_dir."/".$file);
            
            foreach ($target_languages as $lang) {
				if( $lang[0] == $default_language ) {
					continue;	
				}
                $fileXML = split("\.",$file);
                $fileXML = $fileXML[0];
                
                $fileXML = split("_",$fileXML);
                $fileXML = $fileXML[0]."_".$lang[0].".xml";
            
                $locStrings_old = getLocalizedStrings($target_dir."/".$fileXML);
            
                $mergedStrings = mergeLocalizedStrings($locStrings,$locStrings_old);

                saveXMLLocalization($target_dir."/".$fileXML, $mergedStrings, $lang);
                //print_r($mergedStrings);
                
            
            }
        }
    }


    function saveXMLLocalization($filename, $strings, $lang) {
        $fileOut  = "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\n";
        $fileOut .= "<locale lang=\"$lang[0]\">\n";
        $fileOut .= "   <header>\n";
        $fileOut .= "       <project>OptiPartnerWeb</project>\n";
        $fileOut .= "       <create>2007-02-02 21:54-0400</create>\n";
        $fileOut .= "       <revise>". date("Y-M-D H:iO") ."</revise>\n";
        $fileOut .= "       <trans>Arnaldo Rivera</trans>\n";
        $fileOut .= "       <lang>$lang[1]</lang>\n";
        $fileOut .= "   </header>\n";
        
        if( $strings != "" ) {
            foreach( $strings as $key => $value ) {
                $fileOut .= "   <message>\n";
                $fileOut .= "       <id>$key</id>\n";
                $fileOut .= "       <string>$value</string>\n";
                $fileOut .= "   </message>\n";
            }   
            $fileOut .= "</locale>\n";
        
            if( file_exists($filename)) {
                rename($filename,$filename.time());
            }
            file_put_contents($filename,$fileOut);
        }
    }

    function mergeLocalizedStrings($newStrings, $oldStrings) {
        $mergedStrings = "";
        
        if( is_array($newStrings) ) {
            foreach( $newStrings as $locStr ) {
                $mergedStrings[$locStr] = $locStr;
            }
        }
        
        if( is_array($oldStrings) ) {
            foreach( $oldStrings as $key => $value ) {
                $mergedStrings[$key] = $value;
            }
        }
        
        return $mergedStrings;
    }

    function getLocalizableStrings( $filename ) {
        $file_contents = file_get_contents($filename);
        
        $loc_strings = "";
        
        preg_match_all ( "|{t}(.*){\/t}|i", $file_contents, $loc_strings );

        $loc_strings = $loc_strings[1];
        
        if( is_array($loc_strings) ) {
			$loc_strings = array_unique($loc_strings);
		}

        return $loc_strings;
    }
    
    
    
    function getLocalizedStrings( $filename ) {
        $loc_strings = "";
        
        if(file_exists($filename)) {
            $xml = getXmlTree($filename);
            foreach($xml[0]['children'] as $tag) {
                if($tag['tag'] == "MESSAGE") {
                    $loc_strings[$tag['children'][0]['value']] = $tag['children'][1]['value'];
                }
            }
        } else {
            return "";
        }
        
        if( is_array($loc_strings) ) {
			$loc_strings = array_unique($loc_strings);
		}
                
        return $loc_strings;
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
                    array_push($children, array('tag' => $vals[$i]['tag'], 'attributes' =>$vals[$i]['attributes'], 'value' => $vals[$i]['value']));
                    break;
                case 'open':
                    array_push($children, array('tag' => $vals[$i]['tag'], 'attributes' =>$vals[$i]['attributes'], 'children' => getChildren($vals, $i)));
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
        $xml = xml_parser_create();
        
        xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($xml, $data, $vals, $index);
        xml_parser_free($xml);

        $tree = array();
        $i = 0;
        array_push($tree, array('tag' => $vals[$i]['tag'],'attributes' => $vals[$i]['attributes'], 'children' => getChildren($vals,$i)));

        return $tree;
    }
?>