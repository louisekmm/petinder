<?php

class Upload {

	const UPLOAD_DIRECTORY = 'content/';
	
	public static function isUpload($file, $x = false) {
	
		if ($x === false) {
			return (isset($_FILES[$file]) && $_FILES[$file]['tmp_name']);
		} else {
			return (isset($_FILES[$file]) && $_FILES[$file]['tmp_name'][$x] != '');
		}
	}

	public static function getDirectory($file, $x=false) {
		return self::UPLOAD_DIRECTORY.$file['type'].'/';
	}
	
	public static function handleFile($file, $affix = '', $x=false) {
		global $di;
		$name = $file['name'];

		if ($x === false) {
			$filename = $_FILES[$name]['name'];
			$tmp = $_FILES[$name]['tmp_name'];
		} else {
			$filename = $_FILES[$name]['name'][$x];
			$tmp = $_FILES[$name]['tmp_name'][$x];
		}
	
		if (!self::isUpload($name, $x)) {
			throw new Exception("Erro ao enviar arquivo");
		} else {
			$arquivo = self::generateFileName(self::getFileExtension($filename), $affix);
			//chmod($tmp, 777);
			if (isset($_FILES[$name]['rename'])) {
				if (!rename($tmp, self::getDirectory($file, $x) . $arquivo)) {
					$palavra = Meta::getLangFile('erro-upload', $di);
					throw new Exception($palavra);
				}
			} else {
				if (!move_uploaded_file($tmp, self::getDirectory($file, $x) . $arquivo)) {
					$palavra = Meta::getLangFile('erro-upload', $di);
					throw new Exception($palavra);
				}
			}
			return $arquivo;
		}
	}
	
	public static function handleFileStatic($file, $directory, $affix = '') {
		global $di;
		$fileName = self::generateFileName(self::getFileExtension($file), $affix);
		
		if (!rename($file, $directory . $fileName)) {
			$palavra = Meta::getLangFile('erro-upload', $di);
			throw new Exception($palavra);
		}
		
		return $fileName;
		
	}
	
	public static function generateFileName($fileExtension = '', $affix = '') {
		return uniqid('', PROJECT_NAME) . $affix . '.' . $fileExtension;
	}
	
	public static function getFileExtension($file) {
		return pathinfo($file, PATHINFO_EXTENSION);
	}
}