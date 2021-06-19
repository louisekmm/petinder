<?php


class Formulario_cadastradorController {
	
	public static function getNewActions() {
		//return array(1=> array('name'=>'Perguntas', 'btn-class'=>'btn-warning', 'class'=>'', 'icon'=> 'th-list', 'href' => 'formulario_pergunta/list/1/'));
	}
	
	public static function setForeign(&$meta, $id) {
		//echo '<pre>';
		
		
	
		
		//die;
		
		//return $meta;
	}
	
	public static function preArray($name, &$arr) {
		global $di;
		//echo $name;
		
		if ($name == 'idAdmin') {
			
			if (LoginController::getTipo($di) == 2) {
				$arr = array();
				$sel = $di->getDb()->prepare('SELECT nome, id FROM admin WHERE idAdmin=?');
				$sel->Execute(array(LoginController::getUserId($di)));
				
				$dados = $sel->fetchAll();
				
				foreach($dados as $obj) {
					$arr[$obj['id']] = $obj['nome'];
				}
			}
		}
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
	
}