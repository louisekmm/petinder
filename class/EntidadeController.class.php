<?php

//ok
class EntidadeController {
	
	const TABLE = 'entidade';
	const NAME = 'Escola';

	public static function getConstName($di){
		$palavra = Meta::getLangFile('NAME_entidade', $di);
		return($palavra);
		
	}
	
	public static function getItem($di, $id) {
		$sel = $di->getDb()->prepare('SELECT * FROM '.self::TABLE.' WHERE id=?');
		$sel->Execute(array($id));
		
		return $sel->fetch();
	}
	
	public static function getNewActions() {
		global $di;
		$palavra = Meta::getLangFile('agrupamentos-meta', $di);
		$palavra1 = Meta::getLangFile('classificacoes-meta', $di);
		$palavra2 = Meta::getLangFile('matriculados-meta', $di);
		return array(1=> array('name'=>$palavra, 'btn-class'=>'btn-warning', 'class'=>'', 'icon'=> 'th-list', 'href' => 'entidade_agrupamento/list/1/'), array('name'=>$palavra1, 'btn-class'=>'btn-warning', 'class'=>'', 'icon'=> 'th-list', 'href' => 'entidade_classificacao/list/1/'), array('name'=>$palavra2, 'btn-class'=>'btn-default', 'class'=>'', 'icon'=> 'th-list', 'href' => 'entidade_pessoa/list/1/', 'target'=> ''));
	}
	
	public function _lista_eventos() {
		global $di;
		$args = func_get_args();
		
		$escola = (isset($_POST['escola']) ? intval($_POST['escola']) : '');
		$escola = $args[1];
		$start = (isset($_GET['start']) ? $_GET['start'] : '');
		$date1 = DateTime::createFromFormat('Y-m-d', $start);
		$end = (isset($_GET['end']) ? $_GET['end'] : '');
		$date2 = DateTime::createFromFormat('Y-m-d', $end);
		
		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($date1, $interval, $date2);
		
		
			
		
		$eventos = array();
		
		$where = array('1=1');
		$valores = array();
		
		$where[] = 't.data > ?';
		$valores[] = $date1->format('Y-m-d');
		
		$where[] = '(t.data < ?)';
		$valores[] = $date2->format('Y-m-d');
		
		if ($escola) {
			$where[] = 't.idEntidade=?';
			$valores[] = $escola;
		}
		$sel = $di->getDb()->prepare('SELECT *,t.id as idEvento, DATE_FORMAT(data, "%d/%m/%Y") as dia, DATE_FORMAT(hora, "%H") as horas, DATE_FORMAT(hora, "%i") as minutos FROM '.self::TABLE.'_evento t WHERE '.implode(' AND ', $where));		
		$sel->Execute($valores);
		$selTurmas = $di->getDb()->prepare('SELECT idEntidade_agrupamento FROM '.self::TABLE.'_evento_agrupamento WHERE idEntidade_evento=?');
		
		//$selProfessores->Execute($valores);
		
		
		$dados = $sel->fetchAll(PDO::FETCH_ASSOC);
		

			foreach($dados as $dado) {
				//if ($dt->format('w') == $dado['idDia']) {
					$selTurmas->Execute(array($dado['idEvento']));
					$dados = $selTurmas->fetchAll(PDO::FETCH_COLUMN);
					$turmas = array();
					foreach($dados as $d) {
						$turmas[] = $d;
					}
					$eventos[] = array('title'=> $dado['nome'], 'dia'=> $dado['dia'], 'id'=> $dado['id'], 'horas'=>$dado['horas'], 'minutos'=>$dado['minutos'], 'descricao'=> $dado['descricao'], 'sms'=> $dado['sms'],'start' => $dado['data'].'T'.$dado['hora'], 'end'=> $dado['data'].'T'.$dado['hora'], 'color'=>$dado['cor'], 'turmas'=>json_encode($turmas));
				//}
			}
		
		
		//$eventos[] = array('title'=> 'Teste', 'start' => '2016-02-19T16:00:00', 'end'=> '2016-02-19T17:00:00');
		//$eventos[] = array('title'=> 'Teste', 'start' => '2016-02-29T16:00:00', 'end'=> '2016-02-29T17:00:00');
		echo json_encode($eventos);
		
		
	}
	public function _del_evento() {
		global $di;
		
		$values = Post::doPost(array('id'));
		if ($values['id']) {
			$del = $di->getDb()->prepare('DELETE FROM '.self::TABLE.'_evento WHERE id=?');
			$del->Execute(array($values['id']));
			
			if ($del) {
				$palavra = Meta::getLangFile('evento-entidade', $di);
				$di->getSession()->setMessage($palavra, true);
								
				echo '1';
			}
		}
	}
	public function _adicionar_evento() {
		global $di;
		$values = Post::doPost(array('escola', 'nome', 'descricao', 'cor', 'data', 'hora', 'sms','id', 'turma'));
		
		if ($values['nome']) {
			$addEvento = $di->getDb()->prepare('INSERT INTO '.self::TABLE.'_evento (idEntidade, nome, descricao, cor, data, hora, sms) VALUES (?, ?, ?, ?, ?, ?,?)');
			$updEvento = $di->getDb()->prepare('UPDATE '.self::TABLE.'_evento set nome=?, descricao=?, cor=?, hora=?, sms=? WHERE id=?');

			$addTurma = $di->getDb()->prepare('INSERT IGNORE INTO '.self::TABLE.'_evento_agrupamento (idEntidade_Evento, idEntidade_agrupamento) VALUES (?, ?)');
			$delTurma = $di->getDb()->prepare('DELETE FROM '.self::TABLE.'_evento_agrupamento WHERE idEntidade_agrupamento NOT IN ('.str_repeat("?,", count($values['turma'])-1).'?)');
			
			
			if ($values['id']) {
				if ($updEvento->Execute(array($values['nome'],$values['descricao'], $values['cor'],$values['hora'],$values['sms'], $values['id']))) {
					$turmas = json_decode($values['turma']);
					$skip = array(0);
					if (is_array($turmas)) {
						foreach($turmas as $obj) {
							//$delTurma->Execute(array());
							$skip[] = intval($obj);
							$addTurma->Execute(array($values['id'], intval($obj)));
						}
					}
					$di->getDb()->query('DELETE FROM '.self::TABLE.'_evento_agrupamento WHERE idEntidade_agrupamento NOT IN ('.implode(' , ', $skip).')');
					$palavra = Meta::getLangFile('evento1-entidade', $di);
					$di->getSession()->setMessage($palavra, true);
										
					echo '1';
				} else {
					//$di->getSession()->setMessage('Evento alterado!', true);
				}
				
				
			} else {
				//unset($values['id'], $values['turma']);
				
				if($values['sms'] == 17){

					if ($addEvento->Execute(array($values['escola'], $values['nome'],$values['descricao'], $values['cor'],$values['data'],$values['hora'],1))) {
						//$
						$id = $di->getDb()->lastInsertId();
						$turmas = json_decode($values['turma']);
						
						foreach($turmas as $obj) {
							//$delTurma->Execute(array());
							$addTurma->Execute(array($id, $obj));
						}

						if ($addEvento->Execute(array($values['escola'], $values['nome'],$values['descricao'], $values['cor'],$values['data'],$values['hora'],7))) {
							//$
							$id = $di->getDb()->lastInsertId();
							$turmas = json_decode($values['turma']);
							
							foreach($turmas as $obj) {
								//$delTurma->Execute(array());
								$addTurma->Execute(array($id, $obj));
							}
							$palavra = Meta::getLangFile('evento2-entidade', $di);
							$di->getSession()->setMessage($palavra, true);
															

							echo '1';
						}
					}
				}
				else{

					if ($addEvento->Execute(array($values['escola'], $values['nome'],$values['descricao'], $values['cor'],$values['data'],$values['hora'],$values['sms']))) {
						//$
						$id = $di->getDb()->lastInsertId();
						$turmas = json_decode($values['turma']);
						
						foreach($turmas as $obj) {
							//$delTurma->Execute(array());
							$addTurma->Execute(array($id, $obj));
						}

						$palavra = Meta::getLangFile('evento2-entidade', $di);
						$di->getSession()->setMessage($palavra, true);
						
						echo '1';
					}
				}



			}
			
		}
		
	}
	
	
	public function _eventos() {
		global $di;
		$args = func_get_args();
		
		
		$di->getView()->escola = $args[1];
		$di->getView()->turmas = self::getTurmas($di, $args[1]);
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('eventos');
	}
	
	public static function getTurmas($di, $id) {
		$sel = $di->getDb()->prepare('SELECT id, nome FROM '.self::TABLE.'_agrupamento WHERE idEntidade=? ORDER BY nome ASC');
		$sel->Execute(array($id));
		
		return $sel->fetchAll();
	}
	
	public function _minhas() {
		global $di;
		$args = func_get_args();
		//print_r($args);
		//$def = new DefaultController();
		//call_user_func_array(array($def, '_list'), $args);
		//$def->_list($args);
		
		
		$whereor = array();
		$valores = array();
		
		if (LoginController::getTipo($di) != 1) {
			$selTipo = $di->getDb()->prepare('SELECT idEntidade FROM '.'admin_entidade WHERE idAdmin=?');
			$selTipo->Execute(array(LoginController::getUserId($di)));
			
			$dados = $selTipo->fetchAll();
			
			foreach($dados as $d) {
				$whereor[] = 'id=?';
				$valores[] = $d['idEntidade'];
			}
			
			
		}
		
		
		
		if (!count($whereor)) {
			$whereor = array('1=1');
		}
		$di->getView()->args = '';
		$di->getView()->meta['controller'] = 'entidade';
		$results = $di->getDb()->prepare('SELECT count(*) from '.self::TABLE.' WHERE 1=1 AND ('.implode(' OR ', $whereor).')');
		
			
		$results->Execute($valores);
		$total = $results->fetch(PDO::FETCH_COLUMN);
		
		$paginas = ceil($total/RESULTADOSPAGINA);
		$pagina = (isset($args[1]) && $args[1] ? intval($args[1]) : 1);
		
		$di->getView()->totalResults = $total;
		$di->getView()->totalPages = $paginas;
		$di->getView()->page = $pagina;
		
		$sel = $di->getDb()->prepare('SELECT id, nome, codigo, eventos, avaliacao FROM '.self::TABLE.' WHERE 1=1 AND ('.implode(' OR ', $whereor).') ORDER BY nome ASC LIMIT '.($pagina-1)*RESULTADOSPAGINA.','.RESULTADOSPAGINA);
		$sel->Execute($valores);
		$dados = $sel->fetchAll();
		
		$di->getView()->tipo_usuario = LoginController::getTipo($di);
		
		
		$di->getView()->results = $dados;
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('minhas');
		
		
	}
	
	public function _agrupamentos() {
		global $di;
		$args = func_get_args();
		//print_r($args);
		//$def = new DefaultController();
		//call_user_func_array(array($def, '_list'), $args);
		//$def->_list($args);
		
		
		$whereor = array('t.idEntidade=?');
		$where = array('1=1');
		$valores = array($args[1]);
		if (LoginController::getTipo($di) != 1) {
			$selTipo = $di->getDb()->prepare('SELECT idEntidade, idAdmin_Tipo FROM '.'admin_entidade WHERE idEntidade=? AND idAdmin=?');
			$selTipo->Execute(array($args[1], LoginController::getUserId($di)));
			
			$dados = $selTipo->fetchAll();
			
			foreach($dados as $d) {
				$whereor[] = 't.idEntidade=?';
				$valores[] = $d['idEntidade'];
			}
			
			if (LoginController::getTipo($di) == 3) {
				
				$where[] = 't.idAdmin=?';
				$valores[] = LoginController::getUserId($di);
			}
			
		} else {
			
		}
		//print_r($whereor);
		$di->getView()->args = '';
		$di->getView()->meta['controller'] = 'escola';
		
		
		$di->getView()->semanas = $di->getDb()->query('SELECT s.*, d.nome as dimensao FROM '.'semana s INNER JOIN '.'dimensao d ON d.id=s.idDimensao ORDER BY termino DESC')->fetchAll();
		
		$sel = $di->getDb()->prepare('SELECT t.id, t.nome as agrupamento, p.nome FROM '.self::TABLE.'_agrupamento t LEFT JOIN admin p ON p.id=t.idAdmin WHERE 1=1 AND ('.implode(' OR ', $whereor).') AND ('.implode(' AND ', $where).')  ORDER BY t.nome');
		
		
		$sel->Execute($valores);
		$dados = $sel->fetchAll();
		
		$di->getView()->results = $dados;
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('agrupamentos');
		
		
	}
	
	public function _avalia() {
		global $di;
		$args = func_get_args();
		//print_r($args);
		//$def = new DefaultController();
		//call_user_func_array(array($def, '_list'), $args);
		//$def->_list($args);
		
		$values = Post::doPost(array('semana', 'id'));
		
		if ($values['semana'] && $values['id']) {
			$whereor = array('1=1');
			$valores = array($values['semana'], $values['id']);
						
			$di->getView()->args = '';
			$di->getView()->meta['controller'] = 'entidade';
			
			
			$turma = $di->getDb()->prepare('SELECT t.*, e.nome as entidade, a.nome as professor FROM '.self::TABLE.'_agrupamento t INNER JOIN '.self::TABLE.' e ON e.id=t.idEntidade LEFT JOIN admin a ON a.id=t.idAdmin WHERE t.id=?');
			$turma->Execute(array($values['id']));
			$di->getView()->turma = $turma->fetch();
	
			$semana = $di->getDb()->prepare('SELECT s.*, d.nome as dimensao, d.id as idDimensao, DATE_FORMAT(inicio, "%d/%m/%Y") as inicio, DATE_FORMAT(termino, "%d/%m/%Y") as termino, IF(termino < current_date(), 1, 0) as expirado  FROM '.'semana s INNER JOIN '.'dimensao d ON d.id=s.idDimensao WHERE s.id=?');
			$semana->Execute(array($values['semana']));
			$di->getView()->semana = $semana->fetch();
			
			
			
			$notas = $di->getDb()->prepare('SELECT valor, descricao FROM '.'dimensao_descricao WHERE idDimensao=? ORDER BY valor ASC');
			$notas->Execute(array($di->getView()->semana['idDimensao']));
			$di->getView()->notas = $notas->fetchAll();

			
			$sel = $di->getDb()->prepare('SELECT em.*, em.id as idPessoa, ema.nota, ema.nao_matriculado FROM '.self::TABLE.'_pessoa em LEFT JOIN '.self::TABLE.'_pessoa_avaliacao ema ON ema.idEntidade_pessoa=em.id AND ema.idSemana=? WHERE em.idEntidade_agrupamento=?');
			$sel->Execute($valores);
			$dados = $sel->fetchAll();
			
			$di->getView()->results = $dados;
			$di->getView()->lng = LoginController::getLng($di);
			$di->getView()->load('avalia');
		
		}
	}
	public function _salva_avaliacao() {
		global $di;
		$args = func_get_args();
		//print_r($args);
		//$def = new DefaultController();
		//call_user_func_array(array($def, '_list'), $args);
		//$def->_list($args);
		
		$values = Post::doPost(array('semana', 'turma', 'nota', 'nao_matriculado'));
		//print_r($values);
		if ($values['semana'] && $values['turma']) {
			$upd = $di->getDb()->prepare('INSERT INTO '.self::TABLE.'_pessoa_avaliacao (idEntidade_pessoa, idSemana, nota, nao_matriculado) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE nota=VALUES(nota), nao_matriculado=VALUES(nao_matriculado)');
			foreach($values['nota'] as $i=>$nota) {
				$matr = (isset($values['nao_matriculado'][$i]) ? $values['nao_matriculado'][$i] : 0);
				$upd->Execute(array($i, $values['semana'], $nota, $matr));
				
			}
			$palavra = Meta::getLangFile('avaliacao-entidade', $di);
			$di->getSession()->setMessage($palavra, true);
			
			
			header("Location: ".URL.'/entidade/agrupamentos/'.$values['turma'].'/');
		}
		
	}
	
	public static function setForeign(&$meta, $id) {
		//echo '<pre>';
		
		
	//	die;
		
		//die;
		
		//return $meta;
		$meta['columns']['idEntidade']['valor'] = $id;
		
		$meta['fieldsToShow'][1]['info']['valor'] = $id;
		
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
	
	
	
}


