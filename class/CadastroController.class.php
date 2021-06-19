<?php

//ok
class CadastroController {
	
	const TABLE = '';
	
	public function _default() {
		global $di;
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('cadastro', true, true, false);
	}


	public static function validaCPF($cpf) {
		$cpf = preg_replace('/[^0-9]/', '', (string) $cpf);
		// Valida tamanho
		if (strlen($cpf) != 11)
			return false;
		// Calcula e confere primeiro dígito verificador
		for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--)
			$soma += $cpf{$i} * $j;
		$resto = $soma % 11;
		if ($cpf{9} != ($resto < 2 ? 0 : 11 - $resto))
			return false;
		// Calcula e confere segundo dígito verificador
		for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--)
			$soma += $cpf{$i} * $j;
		$resto = $soma % 11;
		return $cpf{10} == ($resto < 2 ? 0 : 11 - $resto);
	}

	
	public function _addescola() {
		global $di;
		
		try {
			$values = Post::doPost(array('id', 'identidade'));
				
			$selEntidade = $di->getDb()->prepare('SELECT * FROM '.EntidadeController::TABLE.' WHERE id=?');
			//$selCpf = $di->getDb()->prepare('SELECT id FROM admin WHERE cpf=?');
			
			$selEntidade->Execute(array($values['id']));
			if ($selEntidade->rowCount()) {
				$entidades = $selEntidade->fetchAll();
				
				foreach($entidades as $e) {
					if (!is_array($values['identidade'])) {
						$values['identidade'] = array();
					}
					if (!in_array($e['id'], $values['identidade'])) {
					?>
					<tr>
						<td><input type='hidden' name='identidade[]' class='identidade' value='<?=$e['id']?>'><?=$e['nome']?></td>
						<td><?=$e['codigo']?></td>
					</tr>
					<?php
					}
				}
			} else {
				echo '0';
			}
			
			
			
			//$di->getDb()->replace('admin', $values);
			
			//echo '1';
		} catch(Exception $e) {
			echo '0';
		}
	}
	public function _finalizar() {
		global $di;
		try {
			$values = Post::doPost(array('cpf', 'nome', 'nascimento', 'idAdmin_Tipo', 'email', 'celular', 'idCelular_Operadora', 'idAdmin','senha',  'identidade', 'idagrupamento'));
			
			$entidades = $values['identidade'];
			$agrupamentos = $values['idagrupamento'];
			unset($values['idagrupamento'], $values['identidade']);
			//print_r($agrupamentos);
			
			$di->getDb()->beginTransaction();
			
			$di->getDb()->insert('admin', $values);
			
			$id = $di->getDb()->lastInsertId();
			
			$insertEntidade = $di->getDb()->prepare('INSERT INTO '.'admin_entidade (idAdmin, idEntidade) VALUES (?, ?)');
			$insertAgrupamento = $di->getDb()->prepare('UPDATE '.EntidadeController::TABLE.'_agrupamento set idAdmin=? WHERE id=?');
			
			
			if (count($entidades)) {
				foreach($entidades as $ent) {
					$insertEntidade->Execute(array($id, $ent));
				}
			}
			
			if (count($agrupamentos) && is_array($agrupamentos)) {
				foreach($agrupamentos as $i=>$ent) {
					if ($ent) {
						$insertAgrupamento->Execute(array($id, $i));
					}
				}
			}
			
			
			//$di->getDb()->replace('admin', $values);
			$di->getDb()->commit();
			echo '1';
			
			
		} catch(Exception $e) {
			$di->getDb()->rollback();
			$palavra = Meta::getLangFile('erro-cadastro', $di);
			echo ($palavra);
			
		}
	}

public function _basico() {
		global $di;
		try {
			$nome = $_POST['nome'];
			$email = $_POST['email'];
			$celular = $_POST['celular'];
			$idAdmin_Tipo = $_POST['idAdmin_Tipo'];
			$senha = $_POST['senha'];

			$insertEntidade = $di->getDb()->prepare('INSERT INTO admin (email, nome, idAdmin_Tipo, celular, senha) VALUES (?, ?, ?, ?, ?)');
			$insertEntidade->Execute(array($email, $nome, $idAdmin_Tipo, $celular, $senha));

			$di->getSession()->setMessage("yay", true);
			//$di->getDb()->commit();
			echo '1';
			
			
		} catch(Exception $e) {
			$palavra = Meta::getLangFile('erro-cadastro', $di);
			echo ($palavra);
			
		}
		
	}

	public function _parte1() {
		global $di;
		try {
			$values = Post::doPost(array('cpf', 'nome', 'nascimento', 'idAdmin_Tipo', 'email', 'celular', 'idCelular_Operadora', 'idAdmin', 'senha'));
			
			$selEmail = $di->getDb()->prepare('SELECT id FROM admin WHERE email=?');
			$selCpf = $di->getDb()->prepare('SELECT id FROM admin WHERE cpf=?');
			$selCelular = $di->getDb()->prepare('SELECT id FROM admin WHERE celular=?');
			
			
			if (!self::validaCPF($values['cpf'])) {
				$palavra = Meta::getLangFile('cpf-admin', $di);
				echo ($palavra);
				
				exit();
			}
			
			
			$selEmail->Execute(array($values['email']));
			if ($selEmail->rowCount()) {
				$palavra = Meta::getLangFile('email-cadastro', $di);
				echo ($palavra);
								
				exit();
			}
			$selCpf->Execute(array($values['cpf']));
			if ($selCpf->rowCount()) {
				$palavra = Meta::getLangFile('cpf-cadastro', $di);
				echo ($palavra);
								
				exit();
			}
			
			$selCelular->Execute(array($values['celular']));
			if ($selCelular->rowCount()) {
				$palavra = Meta::getLangFile('celular-cadastro', $di);
				echo ($palavra);
												
				exit();
			}
			
			
			//$di->getDb()->replace('admin', $values);
			
			echo '1';
		} catch(Exception $e) {
			
			$palavra = Meta::getLangFile('erro1-cadastro', $di);
			echo ($palavra);
		}
	}
	
	public function _turmas() {
		global $di;
		//print_r($_POST);
		try {
			$values = Post::doPost(array('idAdmin_Tipo', 'identidade'));
		//	$resultado = '';
			$selTipo = $di->getDb()->prepare('SELECT * FROM '.'admin_tipo WHERE id=?');
			
			
			$selTipo->Execute(array($values['idAdmin_Tipo']));
			if ($selTipo->rowCount()) {
				$da = $selTipo->fetch();
				
				if (($da['idAcl']) == 5) {
					if (!$values['identidade']) {
						$values['identidade'] = array(0);
					}
					$qMarks = str_repeat('?,', count($values['identidade']) - 1) . '?';
					$selTurmas = $di->getDb()->prepare('SELECT t.*, e.nome as entidade FROM '.'entidade_agrupamento t INNER JOIN '.EntidadeController::TABLE.' e ON e.id=t.idEntidade WHERE t.idEntidade IN ('.$qMarks.') AND t.idAdmin IS NULL');
					$selTurmas->Execute($values['identidade']);
					$tt = $selTurmas->fetchAll();
					
					if (count($tt)) {
						?>
						<div class='col-md-12'>
						<table class='table table-striped'>
							<thead>
								<th><?=EntidadeController::getConstName($di)?></th>
								<th>Turma</th>
							</thead>
							<tbody>
								<?php
								foreach($tt as $t) {
									?>
									<tr>
										<td><input type='checkbox' name='idagrupamento[<?=$t['id']?>]' value='1'> <?=$t['entidade']?></td>
										<td><?=$t['nome']?></td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
						</div>
						<?php
					}
				}
			}
			
			echo '<span></span>';
			//echo $resultado;
		} catch(Exception $e) {
			echo '0';
		}
	}
	
}

