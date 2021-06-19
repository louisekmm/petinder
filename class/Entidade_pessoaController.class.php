<?php


class Entidade_pessoaController {
	
	const TABLE = 'entidade_pessoa';
	public static function f_parse_csv($file, $longest, $delimiter) {
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

	
	public static function getDadosImport($arr, $arrHead, $convertToUtf8) {
		$arrResult = array();
		
		foreach($arrHead as $i=>$a) {
			if(!mb_check_encoding($arr[$arrHead[$i]], 'UTF-8') OR !($arr[$arrHead[$i]] === mb_convert_encoding(mb_convert_encoding($arr[$arrHead[$i]], 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32'))) {

					$arr[$arrHead[$i]] = mb_convert_encoding($arr[$arrHead[$i]], 'UTF-8', 'pass'); 
				}
					
			$arrResult[$i] = trim((isset($arr[$arrHead[$i]]) ? $arr[$arrHead[$i]] : ''));
		}
		return $arrResult;
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
	public static function getNewActions() {
		//return array(1=> array('name'=>'Turmas', 'btn-class'=>'btn-warning', 'class'=>'', 'icon'=> 'th-list', 'href' => 'escola_turma/list/1/'), array('name'=>'Matriculados', 'btn-class'=>'btn-default', 'class'=>'', 'icon'=> 'th-list', 'href' => 'escola_matriculado/list/1/', 'target'=> ''));
	}
	
	public static function setForeign(&$meta, $id) {
		$meta['columns']['idEntidade']['valor'] = $id;
		
		$meta['fieldsToShow'][1]['info']['valor'] = $id;
		
	}
	
	public static function _import($a=1, $id = 0) {
		global $di;
		$di->getView()->id = $id;
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('import_entidadepessoa');
	}
	
	public static function _doimport($a, $b) {
		global $di;
		//$extensao = Upload::getFileExtension($files['arquivo']['name']);
		$file = ($_FILES['arquivo']) ? $_FILES['arquivo']['tmp_name'] : '';
		ini_set("auto_detect_line_endings", "1");
		$arr_csv = self::f_parse_csv($file, 9999999, ';');
		$selEntidade = $di->getDb()->prepare('SELECT id FROM '.'entidade WHERE nome=?');
		$selAgrupamento = $di->getDb()->prepare('SELECT id FROM '.'entidade_agrupamento WHERE nome=? AND idEntidade=?');
		//$selForm = $di->getDb()->prepare('SELECT id FROM formulario WHERE nome=?');
		$selGrupo = $di->getDb()->prepare('SELECT id FROM '.'grupo_envio WHERE nome=?');
		
		$add = $di->getDb()->prepare('INSERT INTO '.'entidade_pessoa (idEntidade, idEntidade_agrupamento, idGrupo_envio, nome, ra, turno, responsavel, celular) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
		
		try {
			$di->getDb()->beginTransaction();
			$x = 0;
			
			$arrImport = array('Entidade', 'Agrupamento', 'Grupo', 'Nome', 'RA', 'Turno', 'Responsavel', 'Celular');
			
			$form_temp = '';
			$usr_temp = '';
			$ent_temp = '';
			$idEntidade = 0;
			$gr_temp = '';
			foreach($arr_csv as $k=>$arr) {
				
				$x++;
				if ($k == 0) {
					$arrHead = self::initImport($arrImport, $arr);
					continue;
				}
				
				$dadosImport = self::getDadosImport($arr, $arrHead, false);
				
				if ($ent_temp != $dadosImport['Entidade']) {
					$ent_temp = $dadosImport['Entidade'];
					$selEntidade->Execute(array($dadosImport['Entidade']));
					

					$idEntidade = $selEntidade->fetch(PDO::FETCH_COLUMN);
					if (!$idEntidade) {
						$palavra = Meta::getLangFile('entidade-admin', $di);
						throw new Exception($palavra);
					}
					
					
				} 
				
				if ($usr_temp != $dadosImport['Agrupamento']) {
					$usr_temp = $dadosImport['Agrupamento'];
					$selAgrupamento->Execute(array($dadosImport['Agrupamento'], $idEntidade));
					

					$idAgrupamento = $selAgrupamento->fetch(PDO::FETCH_COLUMN);
					if (!$idAgrupamento) {
						$palavra = Meta::getLangFile('agrupamento-entidade-pessoa', $di);
						throw new Exception($palavra);
					}
					
					
				}
				
				if ($gr_temp != $dadosImport['Grupo']) {
					$gr_temp = $dadosImport['Grupo'];
					$selGrupo->Execute(array($dadosImport['Grupo']));
					$idGrupo = $selGrupo->fetch(PDO::FETCH_COLUMN);
					if (!$idGrupo) {
						$palavra = Meta::getLangFile('grupo-entidade-pessoa', $di);
						throw new Exception($palavra);
					}
				}
				
				$add->Execute(array($idEntidade, $idAgrupamento, $idGrupo, $dadosImport['Nome'], $dadosImport['RA'], $dadosImport['Turno'], $dadosImport['Responsavel'], $dadosImport['Celular']));

			}
			
			$di->getDb()->commit();
			$palavra = Meta::getLangFile('operacao-admin', $di);
			$di->getSession()->setMessage($palavra, true);
			
		} catch (Exception $e) {
			$di->getDb()->rollBack();
			$palavra = Meta::getLangFile('erro-entidade-pessoa', $di);
			$palavra1 = Meta::getLangFile('mensagem-entidade-pessoa', $di);
			$di->getSession()->setMessage($palavra.' '.$x.' - '.$palavra1.' '.$e->getMessage(), false);
				
		}
			
			header('Location: '.URL.'/entidade_pessoa/import/'.$b.'/');
	}
	
	public static function listFilter(&$where, &$values, &$inner, &$args) {
		//print_r($where);
		$where[] = 't.idEntidade=?';
		$values[] = $args[2];
		
		
	}
	public static function editList(&$where, &$values, &$inner, $args, &$show, &$columns) {
		global $di;
		
		//$sel = $di->getDb()->prepare('SELECT g.nome, g.id, gv.valor FROM '.Entidade_grupoController::TABLE.' g LEFT JOIN '.self::TABLE.'_grupo p ON p.idEntidade_pessoa=? AND p.idEntidade_grupo=g.id LEFT JOIN '.Entidade_grupoController::TABLE.'_valor gv ON gv.id=p.idEntidade_grupo_valor WHERE g.idEntidade=? ORDER BY g.ordem ASC');
		/*$sel = $di->getDb()->prepare('SELECT g.nome, g.id  FROM '.Entidade_classificacaoController::TABLE.' g WHERE g.idEntidade=? ORDER BY g.ordem ASC');
		$sel->Execute(array($args[2]));
		
		
		$dados = $sel->fetchAll();
		foreach($dados as $dado) {
			
			
			//$columns[] = '"'.$dado['valor'].'" as '.$dado['nome'];
			$columns[] = '(SELECT nome as valor FROM '.Entidade_classificacaoController::TABLE.'_valor ev INNER JOIN '.self::TABLE.'_classificacao gv ON gv.idEntidade_classificacao_valor=ev.id WHERE idEntidade_pessoa=t.id AND ev.idEntidade_classificacao='.$dado['id'].') as '.$dado['nome'];
		
			$show[] = array('name'=> $dado['nome'], 'title' => $dado['nome'], 'midia' => '', 'foreign' => 0, 'reference'=>'', 'reference-1'=> '', 'options'=> Array ('['.str_replace(' ', '_', $dado['nome']).']', 'column'  ), 'meta' => '');
			
			
			//$meta['fieldsToShow'][] = array('name'=> $dado['nome'], 'info'=>array('name'=> 'custom_'.str_replace('-', '_', urlAmigavel($dado['nome'])), 'maxlength'=>100, 'type'=> 'enum', 'comment' => array('v', 'nd', '['.str_replace(' ', '_', $dado['nome']).']', 'enum2'), 'enum' => $temp, 'tabela' => self::TABLE, 'default' => '', 'valor' => $dado['valor']));
			
		}
		
		
		//$inner[] = 'LEFT JOIN '.self::TABLE.'_grupo pg ON pg.idEntidade_pessoa=t.id';
		
		//$columns[] = '(SELECT count(*) FROM aluno_curso WHERE idTurma=t.id) as Alunos';
		
		//$show[] = array('name'=> 'Alunos', 'title' => 'Alunos', 'midia' => '', 'foreign' => 0, 'reference'=>'', 'reference-1'=> '', 'options'=> Array ('[Sala_Principal]', 'column'  ), 'meta' => '');
		*/
		
	}
	
	public static function afterSave($id, $arg) {
		global $di;
		/*
		$sel = $di->getDb()->prepare('SELECT g.nome, g.id, idEntidade_grupo_valor as valor FROM '.Entidade_grupoController::TABLE.' g LEFT JOIN '.self::TABLE.'_grupo p ON p.idEntidade_pessoa=? AND p.idEntidade_grupo=g.id WHERE g.idEntidade=? ORDER BY g.ordem ASC');
		$sel->Execute(array($id, $arg[1]));
		
		$sel2 = $di->getDb()->prepare('SELECT valor, id FROM '.Entidade_grupoController::TABLE.'_valor WHERE idEntidade_Grupo=?');
		$dados = $sel->fetchAll();
		$del = $di->getDb()->prepare('DELETE FROM '.self::TABLE.'_grupo WHERE idEntidade_pessoa=?');
		$del->Execute(array($id));
		$ins = $di->getDb()->prepare('INSERT INTO '.self::TABLE.'_grupo (idEntidade_pessoa, idEntidade_grupo, idEntidade_grupo_valor) VALUES (?, ?, ?)');
		foreach($dados as $dado) {
			$sel2->Execute(array($dado['id']));
			
			$resposta = $sel2->fetchAll();
			$temp = array();
			foreach($resposta as $resp) {
				$temp[$resp['id']] = $resp['valor'];
			}
			
			$campo = 'custom_'.str_replace('-', '_', urlAmigavel($dado['nome']));
			
			$valor = Post::doPost(array($campo));
			if ($valor[$campo]) {
				
				$ins->Execute(array($id, $dado['id'], $valor[$campo]));
			}
			
			//$meta['fieldsToShow'][] = array('name'=> $dado['nome'], 'info'=>array('name'=> 'custom_'.str_replace('-', '_', urlAmigavel($dado['nome'])), 'maxlength'=>100, 'type'=> 'enum', 'comment' => array('v', 'nd', '['.str_replace(' ', '_', $dado['nome']).']', 'enum2'), 'enum' => $temp, 'tabela' => self::TABLE, 'default' => '', 'valor' => $dado['valor']));
			
		}
		
		*/
	}
	public static function setForm(&$meta, $arg) {
		global $di;
		/*
		$sel = $di->getDb()->prepare('SELECT g.nome, g.id, idEntidade_grupo_valor as valor FROM '.Entidade_grupoController::TABLE.' g LEFT JOIN '.self::TABLE.'_grupo p ON p.idEntidade_pessoa=? AND p.idEntidade_grupo=g.id WHERE g.idEntidade=? ORDER BY g.ordem ASC');
		$sel->Execute(array($arg[1], $arg[2]));
		
		$sel2 = $di->getDb()->prepare('SELECT valor, id FROM '.Entidade_grupoController::TABLE.'_valor WHERE idEntidade_Grupo=?');
		$dados = $sel->fetchAll();
		
		foreach($dados as $dado) {
			$sel2->Execute(array($dado['id']));
			
			$resposta = $sel2->fetchAll();
			$temp = array();
			foreach($resposta as $resp) {
				$temp[$resp['id']] = $resp['valor'];
			}
			
			$meta['fieldsToShow'][] = array('name'=> $dado['nome'], 'info'=>array('name'=> 'custom_'.str_replace('-', '_', urlAmigavel($dado['nome'])), 'maxlength'=>100, 'type'=> 'enum', 'comment' => array('v', 'nd', '['.str_replace(' ', '_', $dado['nome']).']', 'enum2'), 'enum' => $temp, 'tabela' => self::TABLE, 'default' => '', 'valor' => $dado['valor']));
			
		}
*/
	}
	
	public static function getForeign() {
		return 'Entidade';
	}
		
}

