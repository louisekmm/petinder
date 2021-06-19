<?php

//OK
class FormularioController {
	
	const TABLE = 'formulario';
	
	public static function getNewActions() {
		global $di;
		$palavra = Meta::getLangFile('perguntas-meta', $di);
		$palavra1 = Meta::getLangFile('preview-menu', $di);
		$palavra2 = Meta::getLangFile('importar-menu', $di);
		$palavra3 = Meta::getLangFile('duplicar-menu', $di);

		return array(1=> array('name'=>$palavra, 'btn-class'=>'btn-warning', 'class'=>'', 'icon'=> 'th-list', 'href' => 'formulario_pergunta/list/1/'), array('name'=>$palavra1, 'btn-class'=>'btn-default', 'class'=>'', 'icon'=> 'eye-open', 'href' => 'formulario/preview/'), array('name'=>$palavra2, 'btn-class'=>'btn-default', 'class'=>'', 'icon'=> 'forward', 'href' => 'formulario/import/'), array('name'=>$palavra3, 'btn-class'=>'btn-default', 'class'=>'', 'icon'=> 'resize-full', 'href' => 'formulario/duplicar/'));
	}
	
	public static function setForeign(&$meta, $id) {
		//echo '<pre>';
		
		
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
	
	public static function beforeSave(&$values) {
		global $di;

		
		$values['slug'] = urlAmigavel($values['nome']);
		
	}
	
	public static function _exportar($a='', $slug_formulario = '') {
		global $di;
		
		$csv = (isset($_GET['csv']) ? $_GET['csv'] : false);
		$where = array('1=1');
		$valores = array();
		$di->getView()->tipo = LoginController::getTipo($di);
		if ($slug_formulario) {
			$sel = $di->getDb()->prepare('SELECT t.* FROM '.self::TABLE.' t WHERE t.slug=?');
			$sel->Execute(array($slug_formulario));
			
			$dados = $sel->fetch();
			
			
			if ($slug_formulario) {
				$where[] = 'f.id=?';
				$valores[] = intval($dados['id']);
			}
			

		}
		
		$forms = array();
		$forms_temp = array(0);
		$selForms = $di->getDb()->query('SELECT id, nome, idAdmin FROM '.self::TABLE)->fetchAll();
		foreach($selForms as $obj) {
			$forms[$obj['id']] = $obj['nome'];
			
			if (LoginController::getTipo($di) == 2 && $obj['idAdmin'] == LoginController::getUserId($di)) {
				$forms_temp[] = $obj['id'];
			}
		}
		
		$inner = '';
		$sel = '';
		switch(LoginController::getTipo($di)) {
			
			case 1: //admin
				$inner .= ' LEFT JOIN admin aC ON aC.id=a.idAdmin';// LEFT JOIN admin_tipo ON admin_tipo.id=aC.idAdmin_Tipo AND admin_tipo.idAcl=4'; //achou o coordenador
				$inner .= ' LEFT JOIN admin aR ON aR.id=aC.idAdmin';// LEFT JOIN admin_tipo ON admin_tipo.id=aR.idAdmin_Tipo AND admin_tipo.idAcl=3'; //achou o gerente regional
				
				$sel = ', aC.nome as coordenador, aR.nome as regional, aC.email as email_coordenador, aR.email as email_regional';
			
			break;
			
			case 2: //Gerente de Projetos
				$inner .= ' LEFT JOIN admin aC ON aC.id=a.idAdmin';// LEFT JOIN admin_tipo ON  admin_tipo.id=aC.idAdmin_Tipo AND  admin_tipo.idAcl=4'; //achou o coordenador
				$inner .= ' LEFT JOIN admin aR ON aR.id=aC.idAdmin';// LEFT JOIN admin_tipo ON  admin_tipo.id=aR.idAdmin_Tipo AND  admin_tipo.idAcl=3'; //achou o gerente regional
				
				$where[] = 'f.id IN ('.implode(',', $forms_temp).')';
				//$valores[] = LoginController::getUserId($di);
				$sel = ', aC.nome as coordenador, aR.nome as regional, aC.email as email_coordenador, aR.email as email_regional';
			break;
			
			case 3: // Gerente Regional
				$inner .= ' LEFT JOIN admin aC ON aC.id=a.idAdmin';// LEFT JOIN admin_tipo ON  admin_tipo.id=aC.idAdmin_Tipo AND  admin_tipo.idAcl=4'; //achou o coordenador
				$where[] = '(aC.idAdmin=? OR a.idAdmin=?)';
				$valores[] = LoginController::getUserId($di);
				$valores[] = LoginController::getUserId($di);
				$sel = ', aC.nome as coordenador, aC.email as email_coordenador';
			break;
			
			case 4: //Coordenador
				$where[] = 'a.idAdmin=?';
				$valores[] = LoginController::getUserId($di);
			break;
			
			case 5: //Cadastrador
				$where[] = 'a.id=?';
				$valores[] = LoginController::getUserId($di);
			break;
			
			default:
				exit();
			break;
		}
		
		
		$max = 0;
		

		$selPerguntas = $di->getDb()->prepare('
					SELECT DATE_FORMAT(r.data, "%d/%m/%Y %H:%i:%s") as data, f.id as id, a.id as idAdmin, r.indice,  a.nome as cadastrador, a.email as email, fc.numero, fc.respondido, r.resposta, f.cache as perguntas'.$sel.'
					FROM '.'resposta_otimizada r
					INNER JOIN '.'formulario f ON f.id=r.idFormulario
					INNER JOIN '.'cadastrador_formulario fc ON fc.idFormulario=f.id
					INNER JOIN admin a ON a.id=fc.idAdmin AND a.id=r.idAdmin
					'.$inner.'
					WHERE '.implode(' AND ', $where).'
					
					ORDER BY f.id, fc.idAdmin, r.indice ASC
					');
					
		$selPerguntas->Execute($valores);
		
		$perguntas = $selPerguntas->fetchAll();
		
		$arr = array();
		foreach($perguntas as $obj) {
			$perguntas = explode('|$|', $obj['perguntas']);
			$respostas = explode('|$|', $obj['resposta']);
			if ($csv) {
				if (!isset($arr[$obj['id']])) {
					$arr[$obj['id']] = array('nome'=>($forms[$obj['id']]), 'perguntas' => $perguntas, 'data'=>array());
				}
				if (!isset($arr[$obj['id']]['data'][$obj['idAdmin']])) {
					$arr[$obj['id']]['data'][$obj['idAdmin']] = array('regional'=> (isset($obj['regional']) ? ($obj['regional']) : ''), 'coordenador'=> (isset($obj['coordenador']) ? ($obj['coordenador']) : ''), 'email_regional'=> (isset($obj['email_regional']) ? ($obj['email_regional']) : ''), 'email_coordenador'=> (isset($obj['email_coordenador']) ? ($obj['email_coordenador']) : ''), 'nome'=>($obj['cadastrador']), 'email'=>($obj['email']), 'numero'=>$obj['numero'], 'respondido'=>$obj['respondido'], 'data'=>array());
				}
			} else {
				if (!isset($arr[$obj['id']])) {
					$arr[$obj['id']] = array('nome'=>htmlentities($forms[$obj['id']]), 'perguntas' => $perguntas, 'data'=>array());
				}
				if (!isset($arr[$obj['id']]['data'][$obj['idAdmin']])) {
					$arr[$obj['id']]['data'][$obj['idAdmin']] = array('regional'=> (isset($obj['regional']) ? htmlentities($obj['regional']) : ''), 'coordenador'=> (isset($obj['coordenador']) ? htmlentities($obj['coordenador']) : ''), 'email_regional'=> (isset($obj['email_regional']) ? htmlentities($obj['email_regional']) : ''), 'email_coordenador'=> (isset($obj['email_coordenador']) ? htmlentities($obj['email_coordenador']) : ''), 'nome'=>htmlentities($obj['cadastrador']), 'email'=>htmlentities($obj['email']), 'numero'=>$obj['numero'], 'respondido'=>$obj['respondido'], 'data'=>array());
				}
			}
			
			if (count($perguntas) > $max) {
				$max = count($perguntas);
			}

			$arr[$obj['id']]['data'][$obj['idAdmin']]['data'][] = array($respostas, $obj['data'], $obj['id'].'-'.$obj['idAdmin'].'-'.$obj['indice']);
			
			
		}
		
		
		$di->getView()->perguntas = $arr;
		$di->getView()->maximo = $max;
		
		if ($csv) {
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename=relatorio.csv');
			header('Pragma: no-cache');
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			$di->getView()->lng = LoginController::getLng($di);
			$di->getView()->load('relatorio_csv', false, false, false);
		} else {
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-type:   application/x-msexcel; charset=utf-8");
			header("Content-Disposition: attachment; filename=relatorio.xls");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			$di->getView()->lng = LoginController::getLng($di);
			$di->getView()->load('relatorio', false, false, false);
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
	
	public static function _doimport($a, $b) {
		global $di;
		//$extensao = Upload::getFileExtension($files['arquivo']['name']);
		$file = ($_FILES['arquivo']) ? $_FILES['arquivo']['tmp_name'] : '';
		ini_set("auto_detect_line_endings", "1");
		$arr_csv = self::f_parse_csv($file, 9999999, ';');
		$selUser = $di->getDb()->prepare('SELECT id FROM admin WHERE email=? AND idAdmin_Tipo=?');
		//$selForm = $di->getDb()->prepare('SELECT id FROM formulario WHERE nome=?');
		$selPergunta = $di->getDb()->prepare('SELECT id FROM '.'formulario_pergunta WHERE idFormulario=:formId ORDER BY ordem ASC limit 1 OFFSET :page ');
		$selMax = $di->getDb()->prepare('SELECT max(indice) FROM '.'resposta WHERE idPergunta=? AND idAdmin=?');
		$selMax = $di->getDb()->prepare('SELECT max(indice) FROM '.'resposta r INNER JOIN formulario_pergunta f ON f.id=r.idPergunta WHERE idFormulario=? AND r.idAdmin=? ORDER BY r.id DESC limit 1');
		$addResposta = $di->getDb()->prepare('INSERT INTO '.'resposta (idPergunta, resposta, idAdmin, indice, pre) VALUES (?, ?, ?, ?, ?)');
		
		try {
			$di->getDb()->beginTransaction();
			$x = 0;
			
			$arrImport = array('Coordenador', 'Cadastrador', 'Formulario', 'Resposta1', 'Resposta2', 'Resposta3', 'Resposta4', 'Resposta5', 'Resposta6', 'Resposta7', 'Resposta8', 'Resposta9', 'Resposta10');
			
			$form_temp = '';
			$usr_temp = '';
			$respostas = array();
			$max = 0;
			foreach($arr_csv as $k=>$arr) {
				
				$x++;
				if ($k == 0) {
					$arrHead = self::initImport($arrImport, $arr);
					continue;
				}
				
				$dadosImport = self::getDadosImport($arr, $arrHead, false);
				
				if ($usr_temp != $dadosImport['Cadastrador']) {
					$usr_temp = $dadosImport['Cadastrador'];
					$selUser->Execute(array($dadosImport['Cadastrador'], 5));
					$idUser = $selUser->fetch(PDO::FETCH_COLUMN);
					if (!$idUser) {
						$palavra = Meta::getLangFile('cadastrador-formulario', $di);
						throw new Exception($palavra);
						
					}
					
					$selMax->Execute(array($b, $idUser));
					$max = $selMax->fetch(PDO::FETCH_COLUMN);
					if (!$max) {
						$max = 1;
					} else {
						$max++;
					}
				} else {
					$max = $max + 1;
				}

				
				$idForm = $b;
				
				
				for($y=1;$y<=50;$y++) {
					if (!$dadosImport['Resposta'.$y]) {
						break;
					}
					
					if (count($respostas) < $y) {
						$selPergunta->bindValue(':formId', (int) $idForm, PDO::PARAM_INT); 
						$selPergunta->bindValue(':page', (int) $y-1, PDO::PARAM_INT); 
						$selPergunta->Execute();
						$idPergunta = $selPergunta->fetch(PDO::FETCH_COLUMN);
						if (!$idPergunta) {
							$palavra = Meta::getLangFile('pergunta-formulario', $di);
							$palavra1 = Meta::getLangFile('nao-formulario', $di);
							throw new Exception($palavra.' '.$y.' '.$palavra1);
							
						}
						$respostas[$y] = $idPergunta;
					} else {
						$idPergunta = $respostas[$y];
					}
					
					$addResposta->Execute(array($idPergunta, $dadosImport['Resposta'.$y], $idUser, $max, ($y==1?1:0)));
				
				}
				
				
			}
			
			$di->getDb()->commit();
			$palavra = Meta::getLangFile('operacao-admin', $di);
			$di->getSession()->setMessage($palavra, true);
			
		} catch (Exception $e) {
			$di->getDb()->rollBack();
			$palavra = Meta::getLangFile('erro-admin', $di);
			$palavra1 = Meta::getLangFile('mensagem-admin', $di);
			$di->getSession()->setMessage($palavra.' '.$x.' - '.$palavra1.' '.$e->getMessage(), false);
				
		}
			
			header('Location: '.URL.'/formulario/import/'.$idForm.'/');
	}
	
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
			
			$arrResult[$i] = trim((isset($arr[$arrHead[$i]]) ? $arr[$arrHead[$i]] : ''));
		}
		return $arrResult;
	}
	public static function _duplicar($a='', $id = '') {
		global $di;
		
		$selForm = $di->getDb()->prepare('SELECT * from '.self::TABLE.' WHERE id=?');
		$insertForm = $di->getDb()->prepare('INSERT INTO '.self::TABLE.' (nome, slug, programa, idMidia, idMidia2, idAdmin, cache) VALUES (?, ?, ?, ?, ?, ?, ?)');
		$selPerguntas = $di->getDb()->prepare('SELECT * FROM '.self::TABLE.'_pergunta WHERE idFormulario=?');
		$insertPergunta = $di->getDb()->prepare('INSERT INTO '.self::TABLE.'_pergunta (idFormulario, idTipo_Pergunta, nome, obrigatorio, numerico, telefone, email, opcao1, opcao2, opcao3, opcao4, opcao5, opcao6, opcao7, opcao8, opcao9, opcao10, outro, ordem) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		try {
			$di->getDb()->beginTransaction();
			
			$selForm->Execute(array($id));
			
			$formulario = $selForm->fetch();
			
			unset($formulario['id'], $formulario['data'], $formulario['formulario_pergunta']);
			
			$formulario['nome'] .= ' (Cópia)';
			$formulario['slug'] .= '-copia';
			
			$insertForm->Execute(array_values($formulario));
			
			$idForm = $di->getDb()->lastInsertId();
			
			$selPerguntas->Execute(array($id));
			
			$perguntas = $selPerguntas->fetchAll();
			
			foreach($perguntas as $p) {
				unset($p['id']);
				$p['idFormulario'] = $idForm;
				
				$insertPergunta->Execute(array_values($p));
			}
			
			$di->getDb()->commit();
			$palavra = Meta::getLangFile('operacao-admin', $di);
			$di->getSession()->setMessage($palavra, true);
			
		} catch (Exception $e) {
			$di->getDb()->rollBack();
			$palavra = Meta::getLangFile('erro-formulario', $di);
			$di->getSession()->setMessage($palavra.' '.$e->getMessage(), false);	

	
		}
			
		header('Location: '.URL.'/formulario/list/');
			

	}
	public static function _import($a='', $id = '') {
		global $di;
		
		$di->getView()->id = $id;
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('import');
	}
	
	public static function _preview($a='', $slug_formulario = '') {
		global $di;
		if ($slug_formulario) {
			$sel = $di->getDb()->prepare('SELECT t.* FROM '.self::TABLE.' t WHERE t.id=?');
			$sel->Execute(array($slug_formulario));
			
			$dados = $sel->fetch();
			if ($dados) {
				$selPerguntas = $di->getDb()->prepare('SELECT * FROM '.self::TABLE.'_pergunta WHERE idFormulario=? ORDER BY ordem ASC');
				$selPerguntas->Execute(array($dados['id']));
				
				$perguntas = $selPerguntas->fetchAll();
			
				$di->getView()->perguntas = $perguntas;
				$di->getView()->preview = true;
				$di->getView()->lng = LoginController::getLng($di);
				$di->getView()->load('cadastrador_perguntas');//, true, true, false);
			}
		}
				
			
	}
	
	public static function _responder($arg1 = '', $slug_formulario = '', $perguntas = '', $indice = 0) {
		global $di;
		
		if ($slug_formulario) {
			$sel = $di->getDb()->prepare('SELECT t.*, fc.pre, fc.respondido, fc.numero, m.file as arquivo1, m2.file as arquivo2 FROM '.self::TABLE.' t LEFT JOIN '.'midia m ON m.id=t.idMidia LEFT JOIN '.'midia m2 ON m2.id=t.idMidia2 INNER JOIN '.'cadastrador_'.self::TABLE.' fc ON fc.idFormulario=t.id WHERE t.slug=? AND fc.idAdmin=?');
			$sel->Execute(array($slug_formulario, LoginController::getUserId($di)));
			
			$dados = $sel->fetch();
			if ($dados) {
				$di->getView()->dados = $dados;
				
				if ($perguntas == 'perguntas') {
					
					if ($dados['respondido'] >= $dados['numero'] && !$indice) {
						$di->getView()->lng = LoginController::getLng($di);
						$di->getView()->load('cadastrador_finalizou');//, true, true, false);
					} else {
						if ($dados['pre']) {
							$selPerguntas = $di->getDb()->prepare('SELECT fp.*, r.resposta, r.indice, f.slug, r.pre as pre2 FROM '.self::TABLE.'_pergunta fp LEFT JOIN '.'resposta r ON r.idPergunta=fp.id AND r.idAdmin=? INNER JOIN '.self::TABLE.' f ON f.id=fp.idFormulario WHERE fp.idFormulario=? AND fp.ordem=1 ORDER BY r.indice ASC');
							$selPerguntas->Execute(array(LoginController::getUserId($di), $dados['id']));
							
							$di->getView()->preperguntas = $selPerguntas->fetchAll();
						}
						if ($indice) {
							$di->getView()->indice = $indice;
							$di->getView()->total = $dados['respondido'];
							$selPerguntas = $di->getDb()->prepare('SELECT fp.*, r.resposta FROM '.self::TABLE.'_pergunta fp LEFT JOIN '.'resposta r ON r.idPergunta=fp.id AND r.indice=? AND r.idAdmin=?  WHERE fp.idFormulario=? ORDER BY fp.ordem ASC');
							$selPerguntas->Execute(array($indice, LoginController::getUserId($di), $dados['id']));
						} else {
							$selPerguntas = $di->getDb()->prepare('SELECT *, "" as resposta FROM '.self::TABLE.'_pergunta WHERE idFormulario=? ORDER BY ordem ASC');
							$selPerguntas->Execute(array($dados['id']));
						}
						
						$perguntas = $selPerguntas->fetchAll();
						
						$di->getView()->perguntas = $perguntas;
						
						$selPerguntas = $di->getDb()->prepare('SELECT fp.*, r.resposta, r.indice, f.slug, r.pre as pre2 FROM '.self::TABLE.'_pergunta fp LEFT JOIN '.'resposta r ON r.idPergunta=fp.id AND r.idAdmin=? INNER JOIN '.self::TABLE.' f ON f.id=fp.idFormulario WHERE fp.idFormulario=? AND fp.ordem=1 ORDER BY r.indice ASC');
						$selPerguntas->Execute(array(LoginController::getUserId($di), $dados['id']));
						
						$di->getView()->preperguntas = $selPerguntas->fetchAll();
						$di->getView()->lng = LoginController::getLng($di);
						$di->getView()->load('cadastrador_perguntas');//, true, true, false);
					}
				} else if ($perguntas == 'completar') {
					if ($dados['pre']) {
						$selPerguntas = $di->getDb()->prepare('SELECT fp.*, r.resposta, r.indice, f.slug, r.pre as pre2 FROM '.self::TABLE.'_pergunta fp LEFT JOIN '.'resposta r ON r.idPergunta=fp.id AND r.idAdmin=? INNER JOIN '.self::TABLE.' f ON f.id=fp.idFormulario WHERE fp.idFormulario=? AND fp.ordem=1 ORDER BY r.indice ASC');
						$selPerguntas->Execute(array(LoginController::getUserId($di), $dados['id']));
						
						$di->getView()->preperguntas = $selPerguntas->fetchAll();
						$di->getView()->lng = LoginController::getLng($di);
						$di->getView()->load('cadastrador_completar');// true, true, false);
						
					}
				} else if ($perguntas == 'resposta') {
					if (($dados['respondido'] >= $dados['numero'] || $_POST['indice'] != $dados['respondido']) && !$indice) {
						header("Location: ".URL.'/formulario/responder/'.$dados['slug'].'/perguntas/');
						exit();
					} else {
						if ($indice > $dados['respondido'] && !$dados['pre']) {
							die;
						}
						$selPerguntas = $di->getDb()->prepare('SELECT * FROM '.self::TABLE.'_pergunta WHERE idFormulario=? ORDER BY ordem ASC');
						$selPerguntas->Execute(array($dados['id']));
						$perguntas = $selPerguntas->fetchAll();
						
						$outro = array();
						$arr = array();
						foreach($perguntas as $obj) {
							$arr[] = 'id'.$obj['id'];
							if ($obj['outro']) {
								$outro[] = $obj['id'];
							}
						}
						
						$valores = Post::doPost($arr);

						if ($indice) {
							$updateResposta = $di->getDb()->prepare('UPDATE '.'resposta SET resposta=? WHERE idPergunta=? AND  idAdmin=? AND indice=?');
							//$updateResposta2 = $di->getDb()->prepare('UPDATE resposta_otimizada SET resposta=? WHERE idFormulario=? AND  idAdmin=? AND indice=?');
							$selResposta = $di->getDb()->prepare('SELECT id, pre FROM '.'resposta WHERE idPergunta=? AND  idAdmin=? AND indice=?');
						}
						
						$insertResposta = $di->getDb()->prepare('INSERT INTO '.'resposta (idPergunta, resposta, idAdmin, indice) VALUES (?, ?, ?, ?)');
						$insertResposta2 = $di->getDb()->prepare('INSERT INTO '.'resposta_otimizada (idFormulario, resposta, idAdmin, indice, r1) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY update resposta=VALUES(resposta), r1=VALUES(r1)');
						$updateCadastrador = $di->getDb()->prepare('UPDATE '.'cadastrador_'.self::TABLE.' SET respondido=respondido+1 WHERE idFormulario=? and idAdmin=?');
						
						try {
							
							$di->getDb()->beginTransaction();
							$pre = false;
							$texto = '';
							$x = 0;
							$r1 = '';
							foreach($valores as $i=>$resp) {
								$x++;
								
								$id = str_replace('id', '', $i);
								if (in_array($id, $outro) && $resp == -1) {
									$resp = 'Outro: '.(isset($_POST['outro'.$id]) ? $_POST['outro'.$id] : '');
								}
								$texto .= ($texto ? '|$|' : '').$resp;
								
								if ($x == 1) {
									$r1 = $resp;
								}
								
								if ($indice) {
									$selResposta->Execute(array($id, LoginController::getUserId($di), $indice));
									$temp = $selResposta->fetch();
									
									if (isset($temp['pre']) && $temp['pre']) {
										$pre = true;
										$up = $di->getDb()->prepare('UPDATE '.'resposta SET pre=0 WHERE id=?');
										$up->Execute(array($temp['id']));
	
									}
									
									if (isset($temp['id']) && $temp['id']) {
										$updateResposta->Execute(array($resp, $id, LoginController::getUserId($di), $indice));
									} else {
										$insertResposta->Execute(array($id, $resp, LoginController::getUserId($di), $indice));
									}
								} else {
									$insertResposta->Execute(array($id, $resp, LoginController::getUserId($di), $dados['respondido']+1));
								}
							}
							

							if (!$indice || ($pre)) {
								$updateCadastrador->Execute(array($dados['id'], LoginController::getUserId($di)));
							}
							
							$insertResposta2->Execute(array($dados['id'], $texto, LoginController::getUserId($di), ($indice ? $indice : $dados['respondido']+1), $r1));
							
							$di->getDb()->commit();
							$palavra = Meta::getLangFile('resposta-formulario', $di);
							$di->getSession()->setMessage($palavra, 1);
							
							
							if ($dados['pre']) {
								header("Location: ".URL.'/formulario/responder/'.$dados['slug'].'/completar/'.$indice.'/');
							} else {
								if ($indice) {
									header("Location: ".URL.'/formulario/responder/'.$dados['slug'].'/perguntas/'.$indice.'/');
								} else {
									header("Location: ".URL.'/formulario/responder/'.$dados['slug'].'/perguntas/');
								}
							}
						} catch(Exception $e) {
							$palavra = Meta::getLangFile('erro1-formulario', $di);
							$di->getSession()->setMessage($palavra, 0);
														
							$di->getDb()->rollBack();
						}
						
						
					}
				} else {
					$di->getView()->lng = LoginController::getLng($di);
					$di->getView()->load('cadastrador_inicial');//, true, true, false);
				}
			} else {
				header('Location: '.URL.'/index/');
			}
		} else {
			header('Location: '.URL.'/index/');
		}
		
	}
	
	
	public static function getFormularios($di) {
		$userId = LoginController::getUserId($di);
		
		$where = array('1=1');
		$vals = array();
		
		
		if (LoginController::getTipo($di) == 2) { //Gerente de Projetos
			$where[] = 't.idAdmin=?';
			$vals[] = LoginController::getUserId($di);
		}
		
		if (LoginController::getTipo($di) == 3) { //Gerente de Projetos
			$where[] = 't.idAdmin=?';
			$vals[] = LoginController::getFather($di);
		}
		
		if (LoginController::getTipo($di) == 4) { //Gerente de Projetos
			$where[] = 't.idAdmin=?';
			$vals[] = LoginController::getGrandfather($di);
		}
		//$sel = $di->getDb()->prepare('SELECT * from formulario f INNER JOIN cadastrador_formulario fc ON fc.idFormulario=f.id WHERE fc.idAdmin=?');
		$sel = $di->getDb()->prepare('SELECT * from '.self::TABLE.' t WHERE '.implode(' AND ', $where).' ORDER BY nome');
		
		//$sel->Execute(array($userId));
		$sel->Execute($vals);
		
		return $sel->fetchAll();
	}

	public static function listFilter(&$where, &$values, $inner, $args) {
		global $di;
		if (LoginController::getTipo($di) == 2) { //Gerente de Projetos
			$where[] = 't.idAdmin=?';
			$values[] = LoginController::getUserId($di);
		}	
		
	}
	
	
}

