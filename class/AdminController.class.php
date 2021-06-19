<?php

//ok
class AdminController {
	use ComumAdmin;

	function validaData($data){
		$t=explode("/",$data);
		if ($t=="")
			return false;
		$dia=$t[0];
		$mes=$t[1];
		$ano=$t[2];
		
		if (!is_numeric($dia) || !is_numeric($mes) || !is_numeric($ano))
			return false;
			
		if ($dia<1 || $dia>31)
			return false;
		if ($mes<1 || $mes>12)
			return false;
		if ($ano<1800 || $ano>2100)
			return false;
		
		return true;
	}

	public static function _import() {
		global $di;
		
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('import_user');
	}
	public static function _doimport() {
		global $di;
		//$extensao = Upload::getFileExtension($files['arquivo']['name']);
		$file = ($_FILES['arquivo']) ? $_FILES['arquivo']['tmp_name'] : '';
		$arr_csv = self::f_parse_csv($file, 9999999, ';');
		
		
		$selUser = $di->getDb()->prepare('SELECT id FROM admin WHERE email=?');
		$addUser = $di->getDb()->prepare('INSERT INTO admin (email, cpf, nome, nascimento, idAdmin_Tipo, celular, idCelular_Operadora, idAdmin, senha, senha2, admin_entidade)  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

		$selEntidade = $di->getDb()->prepare('SELECT id FROM '.EntidadeController::TABLE.' WHERE nome=?');
		$selOperadora = $di->getDb()->prepare('SELECT id FROM celular_operadora WHERE nome=?');
		$selForm = $di->getDb()->prepare('SELECT id FROM '.FormularioController::TABLE.' WHERE nome=?');

		$insertUserForm = $di->getDb()->prepare('INSERT INTO cadastrador_formulario (idAdmin, idFormulario, numero, pre) VALUES (?, ?, ?, ?)');
	
		$tipos_temp = $di->getDb()->query('SELECT id, nome FROM admin_tipo WHERE idAcl>='.LoginController::getTipo($di))->fetchAll();
	
		$tipos = array();
		foreach($tipos_temp as $tipo) {
			$tipos[$tipo['nome']] 		= $tipo['id'];
		}
		
		
		try {
			$di->getDb()->beginTransaction();
			$x = 0;
			
			$arrImport = array('Nome', 'Email', 'Cpf', 'Nascimento', 'Dono', 'Entidade', 'Tipo', 'Senha', 'Celular', 'Operadora','Formulario', 'Respostas', 'Pre');
			
			$form_temp = '';
			
			foreach($arr_csv as $k=>$arr) {
				$x++;
				if ($k == 0) {
					$arrHead = self::initImport($arrImport, $arr);
					continue;
				}
				
				$dadosImport = self::getDadosImport($arr, $arrHead, false);
				
				
				$selUser->Execute(array($dadosImport['Email']));
				$id = $selUser->fetch(PDO::FETCH_COLUMN);
				
				if ($id) {
					$palavra = Meta::getLangFile('usuario-admin', $di);
					throw new Exception($palavra);
									
				}
				
				$selUser->Execute(array($dadosImport['Cpf']));
				$cpf = $selUser->fetch(PDO::FETCH_COLUMN);

				if ($cpf) {
					$palavra = Meta::getLangFile('usuario1-admin', $di);
					throw new Exception($palavra);
					
				}
				
				if (!isset($tipos[$dadosImport['Tipo']])) {
					$palavra = Meta::getLangFile('tipo-admin', $di);
					throw new Exception($palavra);
					
				}
	
				if ($dadosImport['Senha']) {
					$senha = $dadosImport['Senha'];
					$senhah = hash("sha512", $senha);
				} else {
					$senha = self::geraSenha();
					$senhah = hash("sha512", $senha);
				}
				
				if ($dadosImport['Dono']) {
					$selUser->Execute(array($dadosImport['Dono']));
					$idDono = $selUser->fetch(PDO::FETCH_COLUMN);
					if (!$idDono) {
						$palavra = Meta::getLangFile('dono-admin', $di);
						throw new Exception($palavra);
					}
				} else {
					$idDono = LoginController::getUserId($di);
				}
				
				if ($dadosImport['Operadora']) {
					$selOperadora->Execute(array($dadosImport['Operadora']));
					$idOperadora = $selOperadora->fetch(PDO::FETCH_COLUMN);
					if (!$idOperadora) {
						$palavra = Meta::getLangFile('operadora-admin', $di);
						throw new Exception($palavra);
					}
				} 

				if (!CadastroController::validaCPF($dadosImport['Cpf'])) {
					$palavra = Meta::getLangFile('cpf-admin', $di);
					throw new Exception($palavra);
						
						
				}

				if (!self::validaData($dadosImport['Nascimento'])) {
						$palavra = Meta::getLangFile('data-admin', $di);
						throw new Exception($palavra);
				}
				if ($dadosImport['Entidade']) {
					$selEntidade->Execute(array($dadosImport['Entidade']));
					$idEntidade = $selEntidade->fetch(PDO::FETCH_COLUMN);
					if (!$idEntidade) {
						$palavra = Meta::getLangFile('entidade-admin', $di);
						throw new Exception($palavra);
						
					}
				} 

 				$userdata = implode('-', array_reverse(explode('/', $dadosImport['Nascimento'])));			
				$addUser->Execute(array($dadosImport['Email'], $dadosImport['Cpf'], $dadosImport['Nome'], $userdata, $tipos[$dadosImport['Tipo']], $dadosImport['Celular'], $idOperadora, $idDono, $senhah, $senha, $idEntidade));
				
										
				if ($dadosImport['Formulario']) {
					$idUsuario = $di->getDb()->lastInsertId();
					$selForm->Execute(array($dadosImport['Formulario']));
					$idFormulario = $selForm->fetch(PDO::FETCH_COLUMN);
					
					if (!$idFormulario) {
						$palavra = Meta::getLangFile('formulario-admin', $di);
						throw new Exception($palavra);
						
					}
					
					if (!$dadosImport['Respostas']) {
						$palavra = Meta::getLangFile('numero-admin', $di);
						throw new Exception($palavra);
						
					}
					
					$insertUserForm->Execute(array($idUsuario, $idFormulario, $dadosImport['Respostas'], $dadosImport['Pre']));
				}
				self::enviaEmailSenha($di, $dadosImport['Nome'], $dadosImport['Email'], $senha);
			}
			//echo 'ok';
			$di->getDb()->commit();

			$palavra = Meta::getLangFile('operacao-admin', $di);
			$di->getSession()->setMessage($palavra, true);
				
		} catch (Exception $e) {
			//print_r($e);
			$di->getDb()->rollBack();
			$palavra = Meta::getLangFile('erro-admin', $di);
			$palavra1 = Meta::getLangFile('mensagem-admin', $di);
			$di->getSession()->setMessage($palavra.' '.$x.' - '.$palavra1.' '.$e->getMessage(), false);
			

			
		}
			
			header('Location: '.URL.'/admin/import/');
	}
	public static function getNewActions() {
//		return array(1=> array('name'=>'Lista de presença', 'btn-class'=>'btn-warning', 'class'=>'', 'icon'=> 'th-list', 'href' => 'turma_presenca/list/1/'));
	}
	
	public static function setForeign(&$meta, $id) {
		
	}
	
	
	public static function getFormularios($di) {
		$userId = LoginController::getUserId($di);
		
		$sel = $di->getDb()->prepare('SELECT * from '.FormularioController::TABLE.' f INNER JOIN '.'cadastrador_formulario fc ON fc.idFormulario=f.id WHERE fc.idAdmin=?');
		
		$sel->Execute(array($userId));
		
		return $sel->fetchAll();
	}
	
	public static function filterFilter(&$filter, &$filterSet) {
		//echo '<pre>';
		global $di;
		//print_r($filter);
		if (LoginController::getTipo($di) == 2) {
			unset($filter['idAdmin_Tipo']['data'][1], $filter['idAdmin_Tipo']['data'][2]);
		}
		
		if (LoginController::getTipo($di) == 3) {
			unset($filter['idAdmin_Tipo']['data'][1], $filter['idAdmin_Tipo']['data'][2], $filter['idAdmin_Tipo']['data'][3]);
		}
		
		if (LoginController::getTipo($di) == 4) {
			unset($filter['idAdmin_Tipo']['data'][1], $filter['idAdmin_Tipo']['data'][2], $filter['idAdmin_Tipo']['data'][3], $filter['idAdmin_Tipo']['data'][4]);
		}
	}
	
	public static function listFilter(&$where, &$values, &$inner, $args) {
		global $di;
		if (LoginController::getTipo($di) == 2) {
			$where[] = '(t.idAdmin=? OR aX.idAdmin=? OR aY.idAdmin=?)';
			$values[] = LoginController::getUserId($di);
			$values[] = LoginController::getUserId($di);
			$values[] = LoginController::getUserId($di);
			
			$inner[] = 'INNER JOIN admin aX ON aX.id=t.idAdmin';
			$inner[] = 'INNER JOIN admin aY ON aY.id=aX.idAdmin';
		}
		
		if (LoginController::getTipo($di) == 3) {
			
			$where[] = '(t.idAdmin=? OR aX.idAdmin=?)';
			$values[] = LoginController::getUserId($di);
			$values[] = LoginController::getUserId($di);
			
			
			$inner[] = 'INNER JOIN admin aX ON aX.id=t.idAdmin';
			
		}
		
		if (LoginController::getTipo($di) == 4) {
			$where[] = 't.idAdmin=?';
			$values[] = LoginController::getUserId($di);
		}
	}
	
	
	
	public static function preArray($name, &$arr) {
		global $di;
		
		if ($name == 'idAdmin_Tipo') {
			if (LoginController::getTipo($di) == 2) {
				unset($arr[1], $arr[2]);
				
			}
			
			if (LoginController::getTipo($di) == 3) {
				unset($arr[1], $arr[2], $arr[3]);
			}
			
			if (LoginController::getTipo($di) == 4) {
				unset($arr[1], $arr[2], $arr[3], $arr[4]);
			}
		}
	}
	
	
	public static function preForm(&$meta, $args) {
		global $di;
		
		//echo '<pre>';
		//print_r($meta['fieldsToShow'][2]);
		//die;
		if (isset($args[1]) && $args[1] == LoginController::getUserId($di)) {
			
			//unset($meta['fieldsToShow'][2]);
			//$meta['fieldsToShow'][2]['info']['type'] = 'hidden';
			
			$meta['fieldsToShow'][2]['info']['comment'][] = 'hidden';
		} else	if (LoginController::getTipo($di) == 1 && isset($meta['fieldsToShow'][0]['info']['valor']) && $meta['fieldsToShow'][0]['info']['valor']) {
			
		} else {
			//unset($meta['fieldsToShow'][8]);

		}
		
		/*if (LoginController::getTipo($di) == 2) {
			
			$meta['fieldsToShow'][2]['info']['comment'][] = 'virtual';
			$meta['fieldsToShow'][2]['info']['comment'][] = 'read-only';
			$meta['fieldsToShow'][2]['info']['comment'][] = 'dont-send';

		}*/
	}
	
	public static function filterPost(&$meta, $args) {
		global $di;

		if (LoginController::getTipo($di) == 2) {
			
			/*$meta['idAdmin_Tipo']['comment'][] = 'virtual';
			$meta['idAdmin_Tipo']['comment'][] = 'dont-send';*/
			

		}
	}
	
	public static function beforeSave(&$valores) {
		global $di;

		if (!$valores['id']) {
			if (!$valores['senha']) {
				$senha = self::geraSenha();
				$senhah = hash("sha512", $senha);
				$email = $valores['email'];
				$nome = $valores['nome'];
				$valores['senha'] = $senhah;
				$valores['senha2'] = $senha;
				return self::enviaEmailSenha($di, $nome, $email, $senha);
			} else {
				$valores['senha2'] = $_POST['senha'];
				return self::enviaEmailSenha($di, $nome, $email, $senha);
			}
		}
	}
	
	
	public static function enviaEmailSenha($di, $nome, $email, $senhaInicial) {
		$palavra = Meta::getLangFile('bem-admin', $di);
		$palavra1 = Meta::getLangFile('ola-admin', $di);
		$palavra2 = Meta::getLangFile('aqui-admin', $di);
		$palavra3 = Meta::getLangFile('site-admin', $di);
		$palavra4 = Meta::getLangFile('email-admin', $di);
		$palavra5 = Meta::getLangFile('senha-admin', $di);
		$palavra6 = Meta::getLangFile('obrigado-admin', $di);
			$titulo = $palavra;
        $mensagem = "<p>".$palavra1.", ".$nome."<br>".$palavra2."<br><br><ul><li>".$palavra3." ".URL."</li><li>".$palavra4." ".$email."</li><li>".$palavra5." ".$senhaInicial."</li></ul><br><br>".$palavra6."</p>";
		
		return true;
		//return Email::send($di, $titulo, array(array('email'=>$email, 'nome' => $nome)), $mensagem);
	}
	
	public static function geraSenha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false){
    // Caracteres de cada tipo
    $lmin = 'abcdefghijklmnopqrstuvwxyz';
    $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $num = '1234567890';
    $simb = '!@#$%*-';

    // Variáveis internas
    $retorno = '';
    $caracteres = '';

    // Agrupamos todos os caracteres que poderão ser utilizados
    $caracteres .= $lmin;
    if ($maiusculas) $caracteres .= $lmai;
    if ($numeros) $caracteres .= $num;
    if ($simbolos) $caracteres .= $simb;

    // Calculamos o total de caracteres possíveis
    $len = strlen($caracteres);

    for ($n = 1; $n <= $tamanho; $n++) {
    // Criamos um número aleatório de 1 até $len para pegar um dos caracteres
    $rand = mt_rand(1, $len);
    // Concatenamos um dos caracteres na variável $retorno
    $retorno .= $caracteres[$rand-1];
    }

    return $retorno;
    }
	
}