<?php

/**
 *	Classe para manipulação de dados enviados pelo formulário (admin)
 *
 *	@author Otávio Tralli <otavio@tralli.org>
 *	@version v 1.0
*/
class Postadmin extends Post {

	public static function doMidia($di, $midias, $x=false) {
		
		$arr = array();
		try {
			$db = $di->getDb();
			
			foreach($midias as $midia) {
				
				if (Upload::isUpload($midia['name'], $x)) {
					
					$arquivo = Upload::handleFile($midia, 'original', $x);
					$r = $db->prepare('INSERT INTO midia (file, type, width, height, idParent, thumb, size, mime) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
					
					$width = 0;
					$height = 0;
					$size = 0;
					$mime = '';
					
					if ($midia['type'] == 'img') {
						list($width, $height) = getimagesize(Upload::getDirectory($midia, $x).$arquivo);
						$result = getimagesize(Upload::getDirectory($midia, $x).$arquivo);
						
						$mime = $result['mime'];
						$size = intval(filesize(Upload::getDirectory($midia, $x).$arquivo)/1024);
					}
					
					//print_r(getimagesize(Upload::getDirectory($midia, $x).$arquivo));
					//die;
					
					$r->Execute(array($arquivo, $midia['type'], $width, $height, NULL, 0, $size, $mime));
					$id = $db->lastInsertId();
					$arr[$midia['name']] = $id;

					$y = 1;
					
					foreach($midia['dimension'] as $dimension) {
						if (isset($dimension[0])) {
							
							$nome = Upload::generateFileName(Upload::getFileExtension(Upload::getDirectory($midia, $x).$arquivo));

							if (!$dimension[0] || !$dimension[1]) {
								$ratio = $width/$height;
								
								if (!$dimension[0]) {
									$dimension[0] = $ratio*$dimension[1];
								} else {
									$dimension[1] = $ratio*$dimension[0];
								}
								FotoAdminController::resizeImage(Upload::getDirectory($midia, $x).$nome, Upload::getDirectory($midia, $x).$arquivo, $dimension[0], $dimension[1]);
							} else{
								FotoAdminController::resizeCropImage(Upload::getDirectory($midia, $x).$nome, Upload::getDirectory($midia, $x).$arquivo, $dimension[0], $dimension[1], 0, 0, FotoAdminController::getWidthReal($width, $height, $dimension[0], $dimension[1]), FotoAdminController::getHeightReal($width, $height, $dimension[0], $dimension[1]));
							}

							$r->Execute(array($nome, $midia['type'], $dimension[0], $dimension[1], $id, $y, 0, $mime));
						}
						$y++;
					}
				} else {
					
				}
			}
			return $arr;
		} catch(Exception $e) {
			throw($e);
		}

	}

	
}