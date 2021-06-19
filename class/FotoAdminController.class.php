<?php

class FotoAdminController  {
	//use ComumAdmin, ContentFilter;
	
	const TABLE = 'midia';
	
	
	public function _updatestatic() {
		global $di;
		$db = $di->getDb();
		$midia = array('name'=>'img', 'type'=>'img', 'width'=>$_POST['width'], 'height'=>$_POST['height']);
		$arr = array();
		if (Upload::isUpload($midia['name'])) {
				
				$arquivo = Upload::handleFile($midia, 'original');
				$r = $db->prepare('INSERT INTO midia (file, type, width, height, idParent, thumb) VALUES (?, ?, ?, ?, ?, ?)');
				
				$width = 0;
				$height = 0;
				if ($midia['type'] == 'img') {
					list($width, $height) = getimagesize(Upload::getDirectory($midia).$arquivo);
				}
				
				
				
				$r->Execute(array($arquivo, $midia['type'], $width, $height, NULL, 0));
				$id1 = $db->lastInsertId();

				if (!$midia['width'] || !$midia['height']) {
					$ratio = $width/$height;
					
					if (!$midia['width']) {
						$midia['width'] = intval($ratio*$midia['height']);
					} else {
						$midia['height'] = intval($midia['width']/$ratio);
					}
					
					FotoAdminController::resizeImage(Upload::getDirectory($midia).str_replace('original', 'thumb1', $arquivo), Upload::getDirectory($midia).$arquivo, $midia['width'], $midia['height']);
				} else {
					FotoAdminController::resizeCropImage(Upload::getDirectory($midia).str_replace('original', 'thumb1', $arquivo), Upload::getDirectory($midia).$arquivo, $midia['width'], $midia['height'], 0, 0, FotoAdminController::getWidthReal($width, $height, $midia['width'], $midia['height']), FotoAdminController::getHeightReal($width, $height, $midia['width'], $midia['height']));
				}
				$r->Execute(array(str_replace('original', 'thumb1', $arquivo), $midia['type'], $midia['width'], $midia['height'], $id1, 1));
				$id = $db->lastInsertId();
				$arr['id'] = $id;
				$arr['idpai'] = $id1;
				$arr['file'] = 	str_replace('../', '', Upload::getDirectory($midia)).str_replace('original', 'thumb1', $arquivo);
				$arr['width'] = $width;
				$arr['height'] = $height;
				$r = $db->prepare('UPDATE staticimg SET idMidia=? WHERE id=?');
				$r->Execute(array($id1, $_POST['static']));
				echo json_encode($arr);
			}
	
	}
	

	public function _upload() {
		$upload_dir = FotoAdminController::getContentDirAdmin('upload', '');

		// HERE PERMISSIONS FOR IMAGE
		$imgsets = array(
		 'maxsize' => 2000,          // maximum file size, in KiloBytes (2 MB)
		 'maxwidth' => 9000,          // maximum allowed width, in pixels
		 'maxheight' => 8000,         // maximum allowed height, in pixels
		 'minwidth' => 10,           // minimum allowed width, in pixels
		 'minheight' => 10,          // minimum allowed height, in pixels
		 'type' => array('bmp', 'gif', 'jpg', 'jpe', 'png')        // allowed extensions
		);

		$re = '';
		
		if(isset($_FILES['upload']) && strlen($_FILES['upload']['name']) > 1) {
		  $upload_dir = trim($upload_dir, '/') .'/';
		  $img_name = basename($_FILES['upload']['name']);

		  // get protocol and host name to send the absolute image path to CKEditor
		  $protocol = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
		  $site = $protocol. $_SERVER['SERVER_NAME'] .'/';

		  $uploadpath =  getcwd().'/'.$upload_dir . $img_name;       // full file path
		  
		  $sepext = explode('.', strtolower($_FILES['upload']['name']));
		  $type = end($sepext);       // gets extension
		  list($width, $height) = getimagesize($_FILES['upload']['tmp_name']);     // gets image width and height
		  
		  $err = '';         // to store the errors

		  // Checks if the file has allowed type, size, width and height (for images)
		  if(!in_array($type, $imgsets['type'])) $err .= 'The file: '. $_FILES['upload']['name']. ' has not the allowed extension type.';
		  if($_FILES['upload']['size'] > $imgsets['maxsize']*1000) $err .= '\\n Maximum file size must be: '. $imgsets['maxsize']. ' KB.';
		  if(isset($width) && isset($height)) {
			if($width > $imgsets['maxwidth'] || $height > $imgsets['maxheight']) $err .= '\\n Width x Height = '. $width .' x '. $height .' \\n The maximum Width x Height must be: '. $imgsets['maxwidth']. ' x '. $imgsets['maxheight'];
			if($width < $imgsets['minwidth'] || $height < $imgsets['minheight']) $err .= '\\n Width x Height = '. $width .' x '. $height .'\\n The minimum Width x Height must be: '. $imgsets['minwidth']. ' x '. $imgsets['minheight'];
		  }

		  // If no errors, upload the image, else, output the errors
		  if($err == '') {
		  	$palavra = Meta::getLangFile('nao-foto', $di);
			if(move_uploaded_file($_FILES['upload']['tmp_name'], $uploadpath)) {
			  $CKEditorFuncNum = $_GET['CKEditorFuncNum'];
			  $url = str_replace('../', '', $upload_dir) . $img_name;
			  //$message = $img_name .' successfully uploaded: \\n- Size: '. number_format($_FILES['upload']['size']/1024, 3, '.', '') .' KB \\n- Image Width x Height: '. $width. ' x '. $height;
			  $message = '';
			  $re = "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$message')";
			}
			else $re = 'alert("'.$palavra.'Unable to upload the file")';
		  }
		  else $re = 'alert("'. $err .'")';
		}
		echo "<script>$re;</script>";
		
	}
	
	
	public static function getWidthReal($width, $height, $mwidth, $mheight) {
		// mwidth = 90
		// mheight = 122
		
		// $height > $width
		/*echo $mheight.'<br>';
		echo $mwidth.'<br>';
		echo $height.'<br>';
		echo $width.'<br>';*/
		$mdif = abs($mheight-$mwidth)/($mheight+$mwidth)*1000;
		$dif = abs($height-$width)/($height+$width)*1000;
		if (($mheight >= $mwidth && $height >= $width) || ($height>=$width && $mheight < $mwidth) || ($mwidth >= $mheight && $width>=$height && $mheight<=$height && $dif<=$mdif)) {
			
			return $width;
		} else {
			
			$ratio = $mwidth/$mheight;
			
			return $height*$ratio;
		}
		
	}
	
	public static function getHeightReal($width, $height, $mwidth, $mheight) {
		
		$mdif = abs($mheight-$mwidth)/($mheight+$mwidth)*1000;
		$dif = abs($height-$width)/($height+$width)*1000;

		if (($mheight >= $mwidth && $height >= $width) || ($height>=$width && $mheight < $mwidth) || ($mwidth >= $mheight && $width>=$height && $mheight<=$height && $dif<=$mdif)) {
			$ratio = $mheight/$mwidth;
			return $width*$ratio;
		} else {
			return $height;
		}
		
	}
	
	public static function getContentDirAdmin($type, $file) {
		return Upload::UPLOAD_DIRECTORY.$type.'/'.$file;
		
	}
	public function _miniatura_save(){
		global $di;
		
		$db = $di->getDb();
		$values = Post::doPost(array('id', 'secao', 'x1', 'y1', 'x2', 'y2', 'w', 'h', 'extra', 'principal', 'pag','tipo','stamp', 'w_thumb', 'h_thumb', 'w_original', 'h_original', 'thumb'));
		
		
		$updateFile = $db->prepare("UPDATE midia SET file=?  WHERE id=?");
		
		$r = $db->prepare("SELECT m2.id, m.file as arquivo, m2.file thumb, m.type from midia m INNER JOIN midia m2 ON m2.idParent=m.id where m2.thumb=? AND m2.idParent=?");
		
		$r->Execute(array($values['thumb'], $values['id']));
		
		if ($r->RowCount()) {
			$dados = $r->fetch(PDO::FETCH_ASSOC);
			
			$r2 = $db->prepare("UPDATE midia SET width=?, height=? WHERE id=?");
			$r2->Execute(array($values['w_thumb'], $values['h_thumb'], $dados['id']));

			
			$original = $dados['arquivo'];
			$thumb = $dados['thumb'];
			
			$final = Upload::generateFileName(Upload::getFileExtension($thumb), 'thumb'.$values['thumb']);
			
			//$scale = $values['w_thumb']/$values['w'];
			//print_r(array(self::getContentDirAdmin($dados['type'], $final), self::getContentDirAdmin($dados['type'], $original), $values['w_thumb'], $values['h_thumb'], $values['x1'], $values['y1'], $values['w'], $values['h']));
			//die;
			
			$r1 = $values['h_thumb']/$values['w_thumb'];
			$r2 = $values['h']/$values['w'];
			
			if ($r1 != $r2) {
				if ($r1 > $r2) {
					$values['w'] = round($values['h']/$r1);
				} else {
					
					$values['h'] = round($values['w']*$r1);
				}
				
			}
			
			$cropped = self::resizeCropImage(self::getContentDirAdmin($dados['type'], $final), self::getContentDirAdmin($dados['type'], $original), $values['w_thumb'], $values['h_thumb'], $values['x1'], $values['y1'], $values['w'], $values['h']);
			$updateFile->Execute(array($final, $dados['id']));
			
			echo '<script>window.close()</script>';
		} else {
			/*$r = $db->prepare("SELECT m.file as arquivo from midia m WHERE id=?");
			$r->Execute(array($values['id']));
			
			
			
			$arquivo = $r->fetch(PDO::FETCH_COLUMN);
			
			echo 'd';
			$r = $db->prepare('INSERT INTO midia (file, type, width, height, idParent, thumb) VALUES (?, ?, ?, ?, ?, ?)');
			
			$width = 0;
			$height = 0;
			$midia = array('type'=>'img');
			if ($midia['type'] == 'img') {
				list($width, $height) = getimagesize(Upload::getDirectory($midia).$arquivo);
			}

			FotoAdminController::resizeCropImage(Upload::getDirectory($midia).str_replace('original', 'thumb'.$values['w_thumb'].$values['h_thumb'], $arquivo), Upload::getDirectory($midia).$arquivo, $values['w_thumb'], $values['h_thumb'], $values['x1'], $values['y1'], $values['w'], $values['h']);
			$r->Execute(array(str_replace('original', 'thumb'.$values['w_thumb'].$values['h_thumb'], $arquivo), $midia['type'], $values['w_thumb'], $values['h_thumb'], $values['id'], $values['w_thumb'].$values['h_thumb']));
				
			*/
		}
		
		
	}
	
	
	
	public function _miniatura(){
		global $di;
		LoginController::checkLogged();
		$db = $di->getDb();
		$args = func_get_args();
		
		
		
		$r = $db->prepare("SELECT m.file as original, m.type, m.width, m.height, m2.file as thumb from  midia m inner join midia m2 ON m2.idParent=m.id where m.id=?");
		$r->Execute(array($args[1]));
		
		if ($r->RowCount()) {
			$tudo = $r->fetchAll(PDO::FETCH_ASSOC);
			$dados = $tudo[0];
			
			$di->getView()->thumb = FotoAdminController::getContentDirAdmin($dados['type'], $dados['thumb']);
			$di->getView()->original = FotoAdminController::getContentDirAdmin($dados['type'], $dados['original']);
			$di->getView()->width_original = $dados['width'];
			$di->getView()->height_original = $dados['height'];
			$di->getView()->id = $args[1];
			
			$x = 2;

			$dimension = array();
			$y=0;
			while (isset($args[$x])) {
				$dimension[] = array($args[$x++], $args[$x++], $tudo[$y]['thumb']);
				$y++;
			}
		
			$di->getView()->dimension = $dimension;
			$di->getView()->lng = LoginController::getLng($di);
			$di->getView()->load('miniatura', true, true, false);
		}
		
		
		
	}
	
	
	public function resizeImage($thumb, $image, $width, $height) {
		$magicianObj = new imageLib($image);
		/*if (!$width || !$height) {
			
		}*/
		$magicianObj->resizeImage($width, $height, 'landscape');
		$magicianObj->saveImage($thumb, 90);
		$magicianObj = null;
		
	}
	
	public static function resizeCropImage($thumb, $image, $width, $height, $startX, $startY, $cropX, $cropY, $circle = false) {
		$magicianObj = new imageLib($image); 		
		if ($cropX && $cropY) $magicianObj->cropImage($cropX, $cropY, $startX.'x'.$startY);
		//echo $cropY;
		$magicianObj->resizeImage($width, $height, 'landscape');
		//die;
		if ($circle && !extension_loaded('imagick')) {
			$magicianObj->roundCorners($width);
		}
		
		$magicianObj->saveImage($thumb, 90);
		
		
		$magicianObj = null;
		
		if ($circle && extension_loaded('imagick')) {
			
			$base = new Imagick($thumb);
			
			if ($width == 64) $mask = new Imagick('mask64.png');
			else if ($width == 48) $mask = new Imagick('mask48.png');
			else if ($width == 32)  $mask = new Imagick('mask32.png');
			
			$base->compositeImage($mask, Imagick::COMPOSITE_COPYOPACITY, 0, 0);
			$base->writeImage($thumb);
			
			
		}
	}
	
	public static function returnExt($file) {
		list($imagewidth, $imageheight, $imageType) = getimagesize($file);
		$imageType = image_type_to_mime_type($imageType);
		
		switch($imageType) {
			case "image/gif":
				return '.gif';
				break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				return '.jpg';
				break;
			case "image/png":
			case "image/x-png":
				return '.png';
				break;
		}
		
	}
	
	public static function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		switch($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image); 
				break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image); 
				break;
			case "image/png":
			case "image/x-png":
				imagealphablending($newImage, false);
				imagesavealpha($newImage, true);
				$source=imagecreatefrompng($image); 
				break;
		}
		imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
		switch($imageType) {
			case "image/gif":
				imagegif($newImage,$thumb_image_name); 
				break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				imagejpeg($newImage,$thumb_image_name,90); 
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$thumb_image_name);  
				break;
		}
		chmod($thumb_image_name, 0777);
		return $thumb_image_name;
	}


	
	
	
	public static function getFoto($di, $id) {
		$db = $di->getDb();
		
		$r = $db->prepare('SELECT m.arquivo FROM midia m  WHERE  m.miniatura=0  and m.id = ?  LIMIT 1');
		
		
			
		$r->Execute(array($id));
		if ($r->rowCount()) {
			return $r->fetch(PDO::FETCH_COLUMN);

		} else {
			return false;
		}
	}
}


