<?php

class ImageProxy {
	var $storage_path = "img/storage/";

	function save($identifier, $subtype = 'photo',$hashfunction = 'md5') {
		$uploadfile = $this->storage_path . call_user_func($hashfunction, $identifier)."_".$subtype;

		if (move_uploaded_file($_FILES[$subtype]['tmp_name'], $uploadfile)) {
			thumb($uploadfile,$uploadfile."_t",100,100, false);
		} else {
			return "Upload failed, try again or with a smaller file.\n";
		}
		
		return true;
	}	
	
	function output($identifier, $thumb = false, $subtype = 'photo',$hashfunction = 'md5') {
		$image = call_user_func($hashfunction, $identifier)."_".$subtype;
		$image = "e4da3b7fbbce2345d7772b0674a318d5"."_".$subtype;
		
		if($thumb == true) {
			$image = $image . "_t";
		}

		$imagepath = $this->storage_path . $image ;

		$imageinfo = getimagesize( $imagepath );
		if ($imageinfo[2] == 1) {
			$imagetype = "gif" ;
		}
		elseif ($imageinfo[2] == 2) {
			$imagetype = "jpeg" ;
		}
		elseif ($imageinfo[2] == 3) {
			$imagetype = "png" ;
		}
		else {
			header( "HTTP/1.0 404 Not Found" );
			exit ;
		}

		header( "Content-type: image/$imagetype" );
		@readfile( $imagepath );	
	}
	
}
?>