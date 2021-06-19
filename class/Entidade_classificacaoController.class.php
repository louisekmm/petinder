<?php


class Entidade_classificacaoController {
	
	const TABLE = 'entidade_classificacao';
	
	public static function getNewActions() {
		//return array(1=> array('name'=>'Turmas', 'btn-class'=>'btn-warning', 'class'=>'', 'icon'=> 'th-list', 'href' => 'escola_turma/list/1/'), array('name'=>'Matriculados', 'btn-class'=>'btn-default', 'class'=>'', 'icon'=> 'th-list', 'href' => 'escola_matriculado/list/1/', 'target'=> ''));
	}
	public static function listFilter(&$where, &$values, &$inner, &$args) {
		//print_r($where);
		$where[] = 't.idEntidade=?';
		$values[] = $args[2];
		
		
	}
	public static function setForeign(&$meta, $id) {
		$meta['columns']['idEntidade']['valor'] = $id;
		
		$meta['fieldsToShow'][1]['info']['valor'] = $id;
		
	}
	
	public static function getForeign() {
		return 'Entidade';
	}
		
}

