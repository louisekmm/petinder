<?php

/**
 *	Trait de métodos comuns utilizados por várias classses (admin)
 *
 *	@author Otávio Tralli <otavio@tralli.org>
 *	@version 1.0
*/
trait ComumAdmin {

	public static function insertVideoVars() {
		return array('video_midia', 'video_arquivo', 'video_show');
	}
	
	public function getCurrent() {
		$this->load();
		return $this->getPublicVars();
	}
	
	private function getPublicVars() {
		$vars = array();
		$props = (new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC);
		foreach($props as $prop) {
			$vars[$prop->getName()] = $prop->getValue($this);
		}
		
        return $vars;
	}
	
	public static function getFile($name, $sufix = '') {
		return self::STRING_REFERENCE.'_'.$name.$sufix.'.php';
	}
	
	public static function getTemplateFile($name) {
		return self::STRING_REFERENCE.'_'.$name.'.tpl';
	}
	
	public function loadData($arrDados) {
		foreach($arrDados as $prop => $dado) {
			$this->data[$prop] = $dado;
		}
	}
	
	public function loadOld() {
		if ($this->getId()) $this->old = $this->getCurrent();
		else $this->old = null;
	}
	
	public function loadNew($string) {
		$this->new = $this->getCurrent();
		
		$this->_di->getLog()->logAction($string.' '.self::STRING_LOG, self::STRING_LOG, $this->getId(), Log::compare($this->old, $this->new, $this));
		
	}
	
	public static function getBasicFiles($sufix = '') {
		$files = array();
		$files['controller'] = self::getFile('controller', $sufix);
		$files['form'] = self::getFile('form', $sufix);
		$files['list'] = self::getFile('list', $sufix);
		return $files;
	}
	
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function get() {
		return $this->data[self::STRING_REFERENCE];
	}
	
	public static function insertImagemVars() {
		return array('id_imagem', 'imagem_show');
	}
	
	public static function ordemVars() {
		return array('ordem');
	}
	
	public static function getImagem($di, $id) {
		$db = $di->getDb();
		$arr = array();
		$select = $db->prepare('SELECT * FROM '.self::TABLE.'_midia t INNER JOIN midia m ON m.id=t.idMidia WHERE '.self::FOREIGN_KEY.'=? AND tipo="img" ORDER by ORDEM ASC');
		$select->Execute(array($id));
		
		$dados = $select->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($dados as $dado) {
			$arr[] = array('id'=>$dado['id'], 'arquivo'=>$dado['arquivo'], 'local'=>$dado['local']);
		}
		return $arr;
	}
	
	
	public function insertImg() {
		$db = $this->_di->getDb();

		$del = $db->prepare('DELETE t.* FROM '.self::TABLE.'_midia t INNER JOIN midia m ON m.id=t.idMidia WHERE t.'.self::FOREIGN_KEY.' = ? AND m.tipo=?');

		$del->Execute(array($this->getId(), 'img'));
		$insert = $db->prepare('INSERT INTO '.self::TABLE.'_midia ('.self::FOREIGN_KEY.', idMidia, ordem) VALUES (?, ?, ?)');
		$update = $db->prepare('UPDATE '.FotoAdminController::TABLE.' SET local=? WHERE id=? OR idMae=?');
		$imagem =  $this->data['imagem'];
		$id = $imagem['id_imagem'];
		$imagem_show = $imagem['imagem_show'];
		
		$x = 0;
		if ($id) {
			foreach($id as $k=>$img) {
				$update->Execute(array(array_sum($imagem_show[$img]), $img, $img));
				$insert->Execute(array($this->getId(), $img, $x));
				$x++;
			}
		}
		
	}
	
	public static function getVideos($di, $id) {
		$db = $di->getDb();
		$arr = array();
		$select = $db->prepare('SELECT * FROM '.self::TABLE.'_midia t INNER JOIN midia m ON m.id=t.idMidia WHERE '.self::FOREIGN_KEY.'=? AND tipo="youtube" ORDER by ORDEM ASC');
		$select->Execute(array($id));
		
		$dados = $select->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($dados as $dado) {
			$arr[] = array($dado['id'], $dado['arquivo'], $dado['local']);
		}
		return $arr;
	}
	
	public function insertVideo() {
		$db = $this->_di->getDb();
		
		$addMidia = $db->prepare('INSERT INTO midia (arquivo, tipo) VALUES (?, "youtube")');
		$updateMidia = $db->prepare('UPDATE midia SET arquivo=? WHERE id=?');
		
		$del = $db->prepare('DELETE t.* FROM '.self::TABLE.'_midia t INNER JOIN midia m ON m.id=t.idMidia WHERE t.'.self::FOREIGN_KEY.' = ? AND m.tipo="youtube"');
	
		$del->Execute(array($this->getId()));
		$insert = $db->prepare('INSERT INTO '.self::TABLE.'_midia ('.self::FOREIGN_KEY.', idMidia, ordem) VALUES (?, ?, ?)');
		$update = $db->prepare('UPDATE '.FotoAdminController::TABLE.' SET local=? WHERE id=? OR idMae=?');
		$video =  $this->data['video'];
		$arquivo = $video['video_arquivo'];
		$midia = $video['video_midia'];
		$video_show = $video['video_show'];
		
		$x = 0;
		foreach($arquivo as $k=>$n) {
			$m = $midia[$k];
			
			if (!$m) {
				$addMidia->Execute(array($n));
				$m = $db->lastInsertId();
			} else {
				$updateMidia->Execute(array($n, $m));
			}
			
			$update->Execute(array(array_sum($video_show[$m]), $m, $m));
			$insert->Execute(array($this->getId(), $m, $x));
			$x++;
		}
	}
	public static function f_parse_csv($file, $longest, $delimiter) {
		ini_set("auto_detect_line_endings", "1");
		$mdarray = array();
		$file    = fopen($file, "r");
		$x = 1;
		while ($line = fgetcsv($file, $longest, $delimiter)) {
			array_push($mdarray, $line);
			$x++;
		}
		fclose($file);
		return $mdarray;
	}
	public static function insertCarrosselVars() {
		return array('carrossel');
	}
	
	public function insertCarrossel($tipo) {
		$db = $this->_di->getDb();
		
		$delCarrossel = $db->prepare('DELETE FROM carrossel WHERE tipo=? AND idExterno=?');
		
		
		$insertCarrossel = $db->prepare('INSERT IGNORE INTO carrossel (tipo, idExterno) VALUES (?, ?)');
		
		
		$carrossel =  $this->data['carrossel']['carrossel'];
		if ($carrossel) {
			$insertCarrossel->Execute(array($tipo, $this->getId()));
		} else {
			$delCarrossel->Execute(array($tipo, $this->getId()));
		}
		
		
	}
	
	
	public static function initImport($arr = array(), $dados) {
		$arrHead = array();
		
		foreach($arr as $ar) {
			$arrHead[$ar] = '';
		}
		
		foreach($dados as $k=>$v) {
			if (in_array($v, $arr)) {
				$arrHead[$v] = $k;
			}
		}
		return $arrHead;
	}
	
	public static function getDadosImport($arr, $arrHead, $convertToUtf8) {
		$arrResult = array();
		foreach($arrHead as $i=>$a) {
			
			$arrResult[$i] = trim($arr[$arrHead[$i]]);
		}
		return $arrResult;
	}
	
	public static function uploadFotos($db, $fotos, $table = '', $tipo = 1) {
		if (!$table) {
			$table = self::TABLE.'_midia';
		}
		
		$insertMidia = $db->prepare('INSERT INTO '.$table.' ('.self::FOREIGN_KEY.', idMidia, ordem) VALUES (?, ?, ?)');
		
		$zip = new ZipArchive;
		$res = $zip->open($fotos);
		$tempname = uniqid();
		if ($res === TRUE) {
		  // extract it to the path we determined above
		  $dir = '../tmp/'.$tempname.'/';
		  $zip->extractTo($dir);
		  $zip->close();
		  $getDe = $db->prepare('SELECT id FROM '.De::TABLE.' where slug=?');
		  $getAcomodacao = $db->prepare('SELECT id FROM '.Acomodacao::TABLE.' where slug=? AND idDe=?');
		  $getExperiencia = $db->prepare('SELECT id FROM '.Experiencia::TABLE.' where slug=? AND idDe=?');
		  $getPacote = $db->prepare('SELECT id FROM '.Pacote::TABLE.' where slug=? AND idDe=?');
		  
		  $del = $db->prepare('DELETE t.* FROM '.$table.' t INNER JOIN midia m ON m.id=t.idMidia WHERE t.'.self::FOREIGN_KEY.' = ? AND m.tipo=?');
		  
		  foreach(glob($dir.'*', GLOB_ONLYDIR) as $diretorio) {
		  
				$temp = explode('_', basename($diretorio));
				$slug = $temp[0];
				
				$getDe->Execute(array($slug));
				$idRef = $getDe->fetch(PDO::FETCH_COLUMN);
				
				
				if ($tipo == 1) {
					
				}
				
				if ($tipo == 2) { //acomodação
					$slug = basename($temp[1]);
					$getAcomodacao->Execute(array($slug, $idRef));
					$idRef = $getAcomodacao->fetch(PDO::FETCH_COLUMN);
				}
				
				if ($tipo == 3) { //experiencia
					$slug = basename($temp[1]);
					$getExperiencia->Execute(array($slug, $idRef));
					$idRef = $getExperiencia->fetch(PDO::FETCH_COLUMN);
				}
				
				if ($tipo == 4) { //pacote
					$slug = basename($temp[1]);
					$getPacote->Execute(array($slug, $idRef));
					$idRef = $getPacote->fetch(PDO::FETCH_COLUMN);
				}
				
				$del->Execute(array($idRef, 'img'));
	
				$x = 0;
				foreach(glob($diretorio.'/*.*') as $arquivo) {
					$arq = Upload::handleFileStatic($arquivo, Upload::PICTURE_DIRECTORY, 'original');
					$idMidia = FotoAdminController::handler1($db, $arq);
					@unlink($arquivo);
					$insertMidia->Execute(array($idRef, $idMidia, $x));
					$x++;
				}
				rmdir($diretorio);
		  }
		  rmdir($dir);
		  return true;
		} else {
			return false;
		}
	
	
	}
	
	public function order() {
		$db = $this->_di->getDb();
		
		try {
			$db->beginTransaction();
			$ordens = $this->data['ordem']['ordem'];

			$upordem = $db->prepare('UPDATE '.self::TABLE.' SET ordem=? WHERE id=?');
			foreach($ordens as $id=>$ordem) {
			
				$upordem->Execute(array($ordem, $id));
			}
			$db->commit();
			return true;
		} catch(Exception $e) {
			$db->rollBack();
			return false;
		}
	}
	
}