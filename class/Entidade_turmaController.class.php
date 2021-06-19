<?php


class Entidade_turmaController {
	
	const TABLE = 'entidade_turma';
	const NAME = 'Turma';

	public static function getConstName($di){
		$palavra = Meta::getLangFile('NAME_entidade_agrupamento', $di);
		return($palavra);
		
	}
	
	public static function getNewActions() {
		//return array(1=> array('name'=>'Turmas', 'btn-class'=>'btn-warning', 'class'=>'', 'icon'=> 'th-list', 'href' => 'escola_turma/list/1/'), array('name'=>'Matriculados', 'btn-class'=>'btn-default', 'class'=>'', 'icon'=> 'th-list', 'href' => 'escola_matriculado/list/1/', 'target'=> ''));
	}
	
	public static function setForeign(&$meta, $id) {
		//echo '<pre>';
		
		
	//	die;
		
		//die;
		
		//return $meta;
		$meta['columns']['idEntidade']['valor'] = $id;
		
		$meta['fieldsToShow'][1]['info']['valor'] = $id;
		
	}
		
}

