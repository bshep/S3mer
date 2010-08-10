<?php

    /**
     * This function gets all the files
     * of a directory.
     *
     * @param string
     * @param bool
     * @return array
     **/
    function directoryToArray($directory, $recursive) {
    	$array_items = array();
    	if ($handle = opendir($directory)) {
    		while (false !== ($file = readdir($handle))) {
    		    
    		    // Ignore system or hidden files / folders
    			if ($file != "." && $file != ".." && $file != ".svn" && $file != ".DS_Store") {
    				if (is_dir($directory. "/" . $file)) {
    					if($recursive) {
    						$array_items = array_merge($array_items, directoryToArray($directory. "/" . $file, $recursive));
    					}
    					$file = $directory . "/" . $file;
    					
                        // Check if is a folder
    					if(!is_dir($file)){
                            $array_items[] = preg_replace("/\.\.\//", "", $file);
                            // $array_items[] = $file;
    					}
    				} else {
    					$file = $directory . "/" . $file;
    					
    					// Check if is a folder
    					if(!is_dir($file)) {
                            $array_items[] = preg_replace("/\.\.\//", "", $file);
                            // $array_items[] = $file;
    					}
    				}
    			}
    		}
    		closedir($handle);
    	}
    	return $array_items;
    }
    
    
    
    
    // Get all the files
    $images_folder = directoryToArray('../images', true);
    $img_folder = directoryToArray('../img', true);
    $css_folder = directoryToArray('../styles', true);
    $js_folder = directoryToArray('../js', true);
    
    // Merge all files
    $files_to_add = array_merge($images_folder, $img_folder, $css_folder, $js_folder);
    // print_r($files_to_add);

    // Get SVN rev number
    $rev = exec("svn info | grep Revision | awk {'print$2'}");
    
    // Echo the info array as json
    $url = 'http://www.s3mer.com/';
    // Do json stuff by hand json_encode won't work on the server
    echo '{"betaManifestVersion":1,"version":"'.$rev.'","entries":';
    echo '[';
    for($i=0; $i<count($files_to_add); $i++) {
        if($i == (count($files_to_add)-1)){
            echo '{"url":"'.$url.$files_to_add[$i].'"}';
        } else {
            echo '{"url":"'.$url.$files_to_add[$i].'"},';
        }
    }
    echo ']}';
    
    


/* End of file gears_manifes.php */
/* Location: .gears/gears_manifes.php */