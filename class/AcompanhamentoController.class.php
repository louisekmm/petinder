<?php
	
class AcompanhamentoController {


	public function _load_avaliadores() {
		global $di;
		
		$values = Post::doPost(array('id'));
		
		
		$re = $di->getDb()->prepare('SELECT a.nome, at.nome as agrupamento FROM '.'entidade_agrupamento at  LEFT JOIN admin a ON a.id=at.idAdmin WHERE at.idEntidade=?');
		$re->Execute(array($values['id']));
		
		$vals = $re->fetchAll();
		foreach($vals as $v) {
			$palavra = Meta::getLangFile('sem-acompanhamento', $di);
			echo '<br>'.$v['agrupamento'].' - '. ($v['nome'] ? $v['nome'] : $palavra).'';
			
		}
		
	}

	public function _login() {
		global $di;
		$di->getView()->filters = array();
		$di->getView()->filtersSet = array();
		/*$filters = array();
		$filters['tipo'] = array('meta' => array('filter-type' => 'single', 'title' => 'Status'), 'data' => array(0=>'Pendente', 1=>'Finalizado'), 'class' => 'required');
		$filters['mes'] = array('meta' => array('filter-type' => 'single', 'title' => 'Mês'), 'data' => Meta::getForeignArr($di, 'mes'), 'class' => 'required');
		$filters['ano'] = array('meta' => array('filter-type' => 'single', 'title' => 'Ano'),  'data' => array((date("Y")-1)=>date("Y")-1, date("Y")=>date("Y"), (date("Y")+1)=>date("Y")+1), 'class' => 'required');*/
		
		/*$values = Post::doPost(array('tipo', 'mes', 'ano'));
		$di->getView()->filters = $filters;
		$di->getView()->filtersSet = $values;
		if ($values['mes'] && $values['ano'] && $values['tipo']>=0) {
			$di->getView()->resultados = self::getPagamentos($di, $values);
			
		}*/
		
		$di->getView()->resultados = self::getLogins($di);
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('acompanhamento_login');
	}
	
	public function _preenchimento() {
		global $di;
		$di->getView()->filters = array();
		$di->getView()->filtersSet = array();
		/*$filters = array();
		$filters['tipo'] = array('meta' => array('filter-type' => 'single', 'title' => 'Status'), 'data' => array(0=>'Pendente', 1=>'Finalizado'), 'class' => 'required');
		$filters['mes'] = array('meta' => array('filter-type' => 'single', 'title' => 'Mês'), 'data' => Meta::getForeignArr($di, 'mes'), 'class' => 'required');
		$filters['ano'] = array('meta' => array('filter-type' => 'single', 'title' => 'Ano'),  'data' => array((date("Y")-1)=>date("Y")-1, date("Y")=>date("Y"), (date("Y")+1)=>date("Y")+1), 'class' => 'required');*/
		
		/*$values = Post::doPost(array('tipo', 'mes', 'ano'));
		$di->getView()->filters = $filters;
		$di->getView()->filtersSet = $values;
		if ($values['mes'] && $values['ano'] && $values['tipo']>=0) {
			$di->getView()->resultados = self::getPagamentos($di, $values);
			
		}*/
		$di->getView()->semanas = self::getSemanas($di);
		$di->getView()->resultados = self::getPreenchimentos($di);
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('acompanhamento_preenchimento');
	}

	public static function getLogins($di) {
		$db = $di->getDb();
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
		$selEntidades = $db->prepare('SELECT * FROM '.'entidade WHERE 1=1 AND ('.implode(' OR ', $whereor).')');
		$selAvaliadores = $db->prepare('SELECT count(*) FROM '.'admin_entidade ae INNER JOIN admin a ON a.id=ae.idAdmin INNER JOIN '.'admin_tipo at ON at.id=a.idAdmin_Tipo AND at.idAcl=5 WHERE ae.idEntidade=?');
		$selDiretores = $db->prepare('SELECT count(*) FROM '.'admin_entidade ae INNER JOIN admin a ON a.id=ae.idAdmin INNER JOIN '.'admin_tipo at ON at.id=a.idAdmin_Tipo AND at.idAcl=3 WHERE ae.idEntidade=?');
		$selTurmas = $db->prepare('SELECT count(*) FROM '.'entidade_agrupamento  WHERE idEntidade=?');
		
		$selEntidades->Execute($valores);
		
		$results = array();
		
		$entidades = $selEntidades->fetchAll();
		
		foreach($entidades as $entidade) {
			
			$selAvaliadores->Execute(array($entidade['id']));
			$selDiretores->Execute(array($entidade['id']));
			$selTurmas->Execute(array($entidade['id']));
			
			$entidade['agrupamentos'] = $selTurmas->fetch(PDO::FETCH_COLUMN);
			$entidade['avaliadores'] = $selAvaliadores->fetch(PDO::FETCH_COLUMN);

			$palavra = Meta::getLangFile('sim-acompanhamento', $di);
			$palavra1 = Meta::getLangFile('nao-acompanhamento', $di);
			$entidade['diretor'] = ($selDiretores->fetch(PDO::FETCH_COLUMN) ? $palavra : $palavra1);
			
			$results[] = $entidade;
		}
		
		return $results;
	}
	
	public static function getPreenchimentos($di) {
		$db = $di->getDb();
		$whereor = array();
		$valores = array();
		if (LoginController::getTipo($di) != 1) {
			$selTipo = $di->getDb()->prepare('SELECT idEntidade FROM '.'admin_entidade WHERE idAdmin=?');
			$selTipo->Execute(array(LoginController::getUserId($di)));
			
			$dados = $selTipo->fetchAll();
			
			foreach($dados as $d) {
				$whereor[] = 'e.id=?';
				$valores[] = $d['idEntidade'];
			}
			
			
		}
		
		if (!count($whereor)) {
			$whereor = array('1=1');
		}
		$selEntidades = $db->prepare('SELECT e.id, e.nome as entidade, a.nome as avaliador, ea.nome as agrupamento FROM '.'entidade e INNER JOIN '.'entidade_agrupamento ea ON ea.idEntidade=e.id INNER JOIN admin a ON a.id=ea.idAdmin WHERE 1=1 AND ('.implode(' OR ', $whereor).') ORDER BY e.nome, ea.nome');
		
		$selEntidades->Execute($valores);

		//$selSemanas->Execute(array());
		
		$results = array();
		
		$entidades = $selEntidades->fetchAll();
		$semanas = self::getSemanas($di);

		foreach($entidades as $entidade) {
			$entidade['semanas'] = array();
			foreach($semanas as $semana) {
				$selAvaliados = $db->prepare('SELECT epaa.idSemana FROM entidade e INNER JOIN entidade_agrupamento ea ON ea.idEntidade=e.id INNER JOIN admin a ON a.id=ea.idAdmin INNER JOIN entidade_pessoa ep ON ep.idEntidade = e.id AND ep.idEntidade_agrupamento = ea.id INNER JOIN entidade_pessoa_avaliacao epaa ON epaa.idEntidade_pessoa = ep.id WHERE e.id='.$entidade['id'].' AND epaa.idSemana='.$semana['id'].' GROUP BY epaa.idSemana ORDER BY e.nome, ea.nome');
				$selAvaliados->Execute();
				$avaliados = $selAvaliados->fetchAll();
				$palavra = Meta::getLangFile('sim-acompanhamento', $di);
				$palavra1 = Meta::getLangFile('nao-acompanhamento', $di);
				if(!$avaliados){
					$entidade['semanas'][] = $palavra1;
					
				}
				else{
					$entidade['semanas'][] = $palavra;

				}
			}
			
			
			
			$results[] = $entidade;
		}
		
		return $results;
	}
	
	public static function getSemanas($di) {
		$db = $di->getDb();
		
		$selSemanas = $db->prepare('SELECT * FROM '.'semana ORDER BY termino DESC');
		
	
		$selSemanas->Execute(array());
		

		return $selSemanas->fetchAll();
		
		
	}
	
	public function _recebimentos() {
		global $di;
		
		global $di;
		$filters = array();
		$filters['tipo'] = array('meta' => array('filter-type' => 'single', 'title' => 'Status'), 'data' => array(0=>'Pendente', 1=>'Finalizado'), 'class' => 'required');
		$filters['mes'] = array('meta' => array('filter-type' => 'single', 'title' => 'Mês'), 'data' => Meta::getForeignArr($di, 'mes'), 'class' => 'required');
		$filters['ano'] = array('meta' => array('filter-type' => 'single', 'title' => 'Ano'),  'data' => array((date("Y")-1)=>date("Y")-1, date("Y")=>date("Y"), (date("Y")+1)=>date("Y")+1), 'class' => 'required');
		
		$values = Post::doPost(array('tipo', 'mes', 'ano'));
		$di->getView()->filters = $filters;
		$di->getView()->filtersSet = $values;
		if ($values['mes'] && $values['ano'] && $values['tipo']>=0) {
			$di->getView()->resultados = self::getRecebimentos($di, $values);
			
		}
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('relatorio_recebimentos');
	}
	
	public static function getRecebimentos($di, $valores) {
		
		$sel = $di->getDb()->prepare('
		
		
				SELECT
					b.valor, DATE_FORMAT(b.vencimento, "%d/%m/%Y") as vencimento, CONCAT(a.nome, " (", a.id,")") as nome, "Mensalidade" as tipo, "Pagamento de mensalidade" descricao,
					"" as data, DATE_FORMAT(pagamento, "%d/%m/%Y") as data2
				FROM
					boleto b
				INNER JOIN
					aluno a ON a.id=b.idAluno
				WHERE
					idMes=? AND ano=? AND pago=?
			
			UNION ALL
			
				SELECT
					cva.valor, "" as vencimento, IF(idProfessor, p.nome, c.nome) as nome, cva.tipo, cva.descricao,
					DATE_FORMAT(data, "%d/%m/%Y") as data, DATE_FORMAT(pagamento, "%d/%m/%Y") as data2
				FROM
					cva
				LEFT JOIN
					cliente c ON c.id=cva.idCliente
				LEFT JOIN
					professor p ON p.id=cva.idProfessor
				WHERE
					MONTH(cva.data)=? AND year(cva.data)=? AND cva.concluido=? AND cva.tipo IN ("Venda", "Aluguel", "Matrícula")
				
			');
		$sel->Execute(array($valores['mes'], $valores['ano'], $valores['tipo'], $valores['mes'], $valores['ano'], $valores['tipo']));
		return $sel->fetchAll();
	}
	
	public static function getPagamentos($di, $valores) {
		$sel = $di->getDb()->prepare('SELECT cva.*, IF(idProfessor, p.nome, c.nome) as externo, IF(idProfessor, 1, 2) as quem, DATE_FORMAT(data, "%d/%m/%Y") as data, DATE_FORMAT(pagamento, "%d/%m/%Y") as data2 FROM cva  LEFT JOIN cliente c ON c.id=cva.idCliente LEFT JOIN professor p ON p.id=cva.idProfessor WHERE MONTH(cva.data)=? AND year(cva.data)=? AND cva.concluido=? AND cva.tipo IN ("Compra", "Pagamento")');
		$sel->Execute(array($valores['mes'], $valores['ano'], $valores['tipo']));
		
		return $sel->fetchAll();
	}
	
	public function _alunoprofessor() {
		global $di;
		$filters = array();
		$filters['professor'] = array('meta' => array('filter-type' => 'single', 'title' => 'Professor'), 'data' => Meta::getForeignArr($di, 'professor'));
		
		$values = Post::doPost(array('professor'));
		$di->getView()->filters = $filters;
		$di->getView()->filtersSet = $values;
		if ($values['professor']) {
			
			$di->getView()->resultados = self::getAlunos($di, $values['professor']);
		}
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('relatorio_alunoprofessor');
		
	}
	
	public static function getAlunos($di, $professor) {
		$q = $di->getDb()->prepare('SELECT DISTINCT ac.mensalidade, a.vencimento, a.id, a.nome, c.nome as curso, GROUP_CONCAT(d.nome) as dia, GROUP_CONCAT(CONCAT(DATE_FORMAT(th.inicio, "%H:%i"), " - ", DATE_FORMAT(th.fim, "%H:%i"))) as horario  FROM aluno a INNER JOIN aluno_curso ac ON ac.idAluno=a.id INNER JOIN turma t ON t.id=ac.idTurma INNER JOIN professor p ON p.id=t.idProfessor INNER JOIN curso c ON c.id=ac.idCurso INNER JOIN turma_horario th ON t.id=th.idTurma INNER JOIN dia d on d.id=th.idDia WHERE idAluno_Status=1 and p.id=? AND NOW() >= t.inicio AND (t.fim = "0000-00-00" OR now() <= t.fim) GROUP BY t.id, a.id');
		$q->Execute(array($professor));
		return $q->fetchAll(PDO::FETCH_ASSOC);
		
	}
	
	public function _aniversariantes() {
		global $di;

		$di->getView()->resultados = self::getAniversarios($di);
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('relatorio_aniversariantes');
		
	}

	public static function getAniversarios($di) {
		$q = $di->getDb()->prepare('SELECT nome, DAY(nascimento) as dia, "Aluno" as tipo, nascimento FROM aluno WHERE MONTH(nascimento) = MONTH(now())
									UNION ALL
									SELECT nome, DAY(nascimento) as dia, "Professor" as tipo, nascimento FROM professor WHERE MONTH(nascimento) = MONTH(now()) ORDER BY MONTH(nascimento) ASC, DAY(nascimento) ASC, nome ASC');
		$q->Execute();
		$r = $q->fetchAll(PDO::FETCH_ASSOC);
		$arr = array();
		foreach($r as $obj) {
			$arr[$obj['dia']][] = $obj;
		}
		//print_r($arr);
		return $arr;
	}
	
		public function _default($tipo) {
			global $di;
			
			
			$filters = array();

			
			
			 
			$filters['unidade'] = array('meta' => array('filter-type' => 'single', 'title' => 'Unidade'), 'data' => Meta::getForeignArr($di, 'unidade'));
			$filters['sala'] = array('meta' => array('filter-type' => 'single', 'title' => 'Sala'), 'data' => Meta::getForeignArr($di, 'sala'));
			$filters['professor'] = array('meta' => array('filter-type' => 'single', 'title' => 'Professor'), 'data' => Meta::getForeignArr($di, 'professor'));
			
			
			$values = Post::doPost(array('unidade', 'sala', 'professor'));
			
			$di->getView()->filters = $filters;
			$di->getView()->filtersSet = $values;
			$di->getView()->lng = LoginController::getLng($di);
			$di->getView()->load('calendario');
		}
		
		public function _feed() {
			global $di;
			
			
			$unidade = (isset($_GET['unidade']) ? intval($_GET['unidade']) : '');
			$sala = (isset($_GET['sala']) ? intval($_GET['sala']) : '');
			$professor = (isset($_GET['professor']) ? intval($_GET['professor']) : '');
			$start = (isset($_GET['start']) ? $_GET['start'] : '');
			$date1 = DateTime::createFromFormat('Y-m-d', $start);
			$end = (isset($_GET['end']) ? $_GET['end'] : '');
			$date2 = DateTime::createFromFormat('Y-m-d', $end);
			
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($date1, $interval, $date2);
			
			
				//echo $dt->format( "l Y-m-d H:i:s\n" );
			
			$eventos = array();
			
			$where = array();
			$valores = array();
			
			$where[] = 't.inicio<=?';
			$valores[] = $date1->format('Y-m-d');
			
			$where[] = '(t.fim = "0000-00-00" OR t.fim >= ?)';
			$valores[] = $date2->format('Y-m-d');
			
			if ($unidade) {
				$where[] = 't.idUnidade=?';
				$valores[] = $unidade;
			}
			
			if ($sala) {
				$where[] = 'th.idSala=?';
				$valores[] = $sala;
			}
			
			if ($professor) {
				$where[] = 'p.id=?';
				$valores[] = $professor;
			}
			
			$selProfessores = $di->getDb()->prepare('SELECT p.nome as professor, p.cor, t.nome, th.idDia, th.inicio, th.fim FROM turma t LEFT JOIN professor p ON p.id=t.idProfessor LEFT JOIN turma_horario th ON th.idTurma=t.id WHERE '.implode(' AND ', $where));
			$selProfessores->Execute($valores);
			
			
			$dados = $selProfessores->fetchAll(PDO::FETCH_ASSOC);
			
			foreach ( $period as $dt ) {
				foreach($dados as $dado) {
					if ($dt->format('w') == $dado['idDia']) {
						$eventos[] = array('title'=> $dado['professor'].' ('.$dado['nome'].')', 'start' => $dt->format('Y-m-d').'T'.$dado['inicio'], 'end'=> $dt->format('Y-m-d').'T'.$dado['fim'], 'color'=>$dado['cor']);
					}
				}
			}
			
			//$eventos[] = array('title'=> 'Teste', 'start' => '2016-02-19T16:00:00', 'end'=> '2016-02-19T17:00:00');
			//$eventos[] = array('title'=> 'Teste', 'start' => '2016-02-29T16:00:00', 'end'=> '2016-02-29T17:00:00');
			echo json_encode($eventos);
		}
		
		
	}