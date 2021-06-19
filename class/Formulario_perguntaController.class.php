<?php


class Formulario_perguntaController {
	
	const TABLE = 'formulario_pergunta';
	

	public static function setForeign(&$meta, $id) {
		//echo '<pre>';
		/*echo $id;
		echo '<pre>';
		print_r($meta);*/
	//	die;
		
		//die;
		
		//return $meta;
	}
	public static function preArray($name, &$arr) {
		global $di;
		
		
	}
	
	public static function preForm(&$meta, $args) {
		global $di;
		
/*		if (LoginController::getTipo($di) == 2) {
			
			$meta['fieldsToShow'][2]['info']['comment'][] = 'virtual';
			$meta['fieldsToShow'][2]['info']['comment'][] = 'read-only';
			$meta['fieldsToShow'][2]['info']['comment'][] = 'dont-send';

		}*/
	}
	
	public static function filterPost(&$meta, $args) {
		global $di;

	/*	if (LoginController::getTipo($di) == 2) {
			
			$meta['idAdmin_Tipo']['comment'][] = 'virtual';
			$meta['idAdmin_Tipo']['comment'][] = 'dont-send';
			

		}*/
	}
	
	public static function beforeSave(&$values, $args) {
		global $di;
		
		$values['idFormulario'] = $args[1];
		
	}
	
	public static function afterSave($id, $args) {
		global $di;
		
		$update = $di->getDb()->prepare('UPDATE '.FormularioController::TABLE.' f SET cache=(SELECT group_concat(fp.nome ORDER BY fp.ordem ASC SEPARATOR "|$|") FROM '.FormularioController::TABLE.'_pergunta fp WHERE fp.idFormulario=f.id GROUP BY fp.idFormulario) WHERE f.id=?');
		$update->Execute(array($args[1]));
		
	}
	
	public static function posOrder($id) {
		global $di;
		
		$update = $di->getDb()->prepare('UPDATE '.FormularioController::TABLE.' f SET cache=(SELECT group_concat(fp.nome ORDER BY fp.ordem ASC SEPARATOR "|$|") FROM '.FormularioController::TABLE.'_pergunta fp WHERE fp.idFormulario=f.id GROUP BY fp.idFormulario) WHERE f.id=?');
		$update->Execute(array($id));
	}
	
}