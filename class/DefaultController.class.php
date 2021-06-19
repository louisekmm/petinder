 <?php
//ok
class DefaultController {
	use ParseOptions;
	
	public function __call($method, $arguments) {
		LoginController::checkLogged();
		header('Location: '.URL.'/index/');

	}
	function _tirafoto() {
		global $di;
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('webcam', false, false);
	}
/*
	public static function getformulario($di){
		global $di;

		
		$di->getView()->userTipo = LoginController::getTipo($di);
		$formulario = (isset($_GET['formulario'])) ? $_GET['formulario'] : '';

		return $formulario;
	
	}*/

	function _preencher(){
		global $di;
		
		$di->getView()->userTipo = LoginController::getTipo($di);
		$formulario = (isset($_GET['formulario'])) ? $_GET['formulario'] : '';

		//if (LoginController::getTipo($di) == 5) {
			$di->getView()->formularios = AdminController::getFormularios($di);
			
			//print_r($di->getView()->formularios);
			$di->getView()->lng = LoginController::getLng($di);
			$di->getView()->load('index_cadastrador');//, true, true, false); para uma unica pagina
	
	}

	function _index() {
		global $di;
		$db = $di->getDb();

		$idiom = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
		$idioma = explode("-", $idiom[0]);

		$zerar = $db->prepare("UPDATE idioma set ativo=0");
		$zerar->Execute();

		if(file_exists(strtolower($idiom[0]).'.lang'))
		{
			$l = strtolower($idiom[0]);
			$atualizar = $db->prepare("UPDATE idioma set ativo=1 where lang='".$l."'");
			$atualizar->Execute();

			$di->getSession()->setSessionValue('idioma', (strtolower($idiom[0])));
		    $di->getView()->lng = parse_ini_file(strtolower($idiom[0]).'.lang');
		    //$lang = (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'pt');
		}
		else
		{
			if(file_exists(strtolower($idioma[0]).'.lang')){
				$l = strtolower($idioma[0]);
				$atualizar = $db->prepare("UPDATE idioma set ativo=1 where lang='".$l."'");
				$atualizar->Execute();

				$di->getSession()->setSessionValue('idioma', (strtolower($idioma[0])));
		    	$di->getView()->lng = parse_ini_file(strtolower($idioma[0]).'.lang');
			}
			else{
				$l = 'en';
				$atualizar = $db->prepare("UPDATE idioma set ativo=1 where lang='".$l."'");
				$atualizar->Execute();

				$di->getSession()->setSessionValue('idioma', "en");
			    $di->getView()->lng = parse_ini_file('en.lang');
			    //$lang = (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'en');
		    }
		}

		$di->getView()->exibir = "sim";
		$di->getView()->noheader=true;
		//$di->getView()->lng = LoginController::getLng($di);
		//$di->getView()->load('index');	
		$di->getView()->load('index');
	}


	function _report() {
		global $di;
		LoginController::checkLogged();
		$db = $di->getDb();
		$args = func_get_args();
		$info = Meta::getFromMeta($di, $args[0]);
		
		if (LoginController::isAllowed($args[0], 'report') < 1) {
			self::notAllowed($di);
		}
		
		
		
		$di->getView()->userTipo = LoginController::getTipo($di);
		$formulario = (isset($_GET['formulario'])) ? $_GET['formulario'] : '';
/*//apenas se o executor nao puder ver o cadastramento
		if (LoginController::getTipo($di) == 5) {
			$di->getView()->formularios = AdminController::getFormularios($di);
			
			//print_r($di->getView()->formularios);
			$di->getView()->load('index_cadastrador');//, true, true, false); para uma unica pagina
		} else {
			
			*/
					$where = array('1=1');
					$vals = array();
				
				
					$inner = '';
					
					if ($formulario) {
						$where[] = 't.slug=?';
						$vals[] = $formulario;
					}
					if (LoginController::getTipo($di) == 2) { //Gerente de Projetos
						$where[] = 't.idAdmin=?';
						$vals[] = LoginController::getUserId($di);

					}

					if (LoginController::getTipo($di) == 3) { //Gerente Regional
						//$where[] = 't.idAdmin=?';
						//$vals[] = LoginController::getUserId($di);
						//$inner .= ' INNER JOIN admin a2 ON a2.id=a.idAdmin '; //achou o coordenador
						//$inner .= ' INNER JOIN admin a3 ON a3.id=a2.idAdmin '; //achou o gerente regional
						/*
						$where[] = '(a2.idAdmin=? OR a.idAdmin=?)';
						$vals[] = LoginController::getUserId($di);
						$vals[] = LoginController::getUserId($di);*/
						
						
						$inner .= 'LEFT JOIN admin aC ON aC.id=a.idAdmin LEFT JOIN admin_tipo ad ON ad.id=aC.idAdmin_Tipo AND ad.idAcl=4'; //achou o coordenador
						$where[] = '(aC.id=? OR a.idAdmin=?)';
						$vals[] = LoginController::getUserId($di);
						$vals[] = LoginController::getUserId($di);
						
					}
					
					if (LoginController::getTipo($di) == 4) { //Coordenador
						//$where[] = 't.idAdmin=?';
						//$vals[] = LoginController::getUserId($di);
//						$inner .= ' INNER JOIN admin a2 ON a2.id=a.idAdmin '; //achou o coordenador

						$where[] = '(a.idAdmin=?)';
						$vals[] = LoginController::getUserId($di);
						//$vals[] = LoginController::getUserId($di);
						
					}
					if (LoginController::getTipo($di) == 5) { //Coordenador
						$where[] = '(a.idAdmin=?)';
						$vals[] = LoginController::getUserId($di);
						
					}
					$selPerguntas = $di->getDb()->prepare('
								SELECT t.id, a.id as idAdmin, t.nome as formulario, a.nome as cadastrador, fc.numero, fc.respondido
								FROM '.'formulario t
								INNER JOIN '.'cadastrador_formulario fc ON fc.idFormulario=t.id
								LEFT JOIN admin a ON a.id=fc.idAdmin
								'.$inner.'
								WHERE '.implode(' AND ', $where).'
								
								ORDER BY t.id, fc.idAdmin
								');
								
					$selPerguntas->Execute($vals);
					
					$perguntas = $selPerguntas->fetchAll();
					
					$arr = array();
					foreach($perguntas as $obj) {
						if (!isset($arr[$obj['id']])) {
							$arr[$obj['id']] = array('nome'=>$obj['formulario'], 'data'=>array());
						}
						
						if (!isset($arr[$obj['id']]['data'][$obj['idAdmin']])) {
							$arr[$obj['id']]['data'][$obj['idAdmin']] = array('nome'=>$obj['cadastrador'], 'numero'=>$obj['numero'], 'respondido'=>$obj['respondido']);
						}
						
					}
										
						$di->getView()->formularios = FormularioController::getFormularios($di);
						$di->getView()->formulario = $formulario;
						$di->getView()->perguntas = $arr;
						$di->getView()->slug = ($formulario ? $formulario.'/':'');
						$di->getView()->lng = LoginController::getLng($di);
						$di->getView()->load('cadastramento');
					
		//}
		
	}
	function _upload() {
		global $di;
		
		
		$di->getView()->eventos = Meta::getForeignArr($di, 'imagem');
		$di->getView()->lng = LoginController::getLng($di);
		$di->getView()->load('upload');
	}
	
	function _doupload() {
		global $di;
		$file = ($_FILES['arquivo']) ? $_FILES['arquivo']['tmp_name'] : '';
		$file2 = ($_FILES['arquivo2']) ? $_FILES['arquivo2']['tmp_name'] : '';
		$evento = isset($_POST['evento'])? intval($_POST['evento']) : 0;
		$filezip = false;
		if ($file && $file2 && $evento) {
			
			$files = glob('../libs/tmp/preview/*'); // get all file names
			foreach($files as $file1){ // iterate files
			  if(is_file($file1))
				unlink($file1); // delete file
			}
			$files = glob('../libs/tmp/normal/*'); // get all file names
			foreach($files as $file1){ // iterate files
			  if(is_file($file1))
				unlink($file1); // delete file
			}
			
			$extensao = Upload::getFileExtension($_FILES['arquivo']['name']);
			$extensao2 = Upload::getFileExtension($_FILES['arquivo2']['name']);
			if ($extensao == 'zip') {
				
				$zip = new ZipArchive;
				
				if ($zip->open($file) === TRUE) {
					
					$zip->extractTo('../libs/tmp/normal/');
					$zip->close();
				}

			}
			
			if ($extensao2 == 'zip') {
				
				
				$zip = new ZipArchive;
				if ($zip->open($file2) === TRUE) {
					
					$zip->extractTo('../libs/tmp/preview/');
					$zip->close();
				}
				
			}
			
			
			
			try {
				
				$di->getDb()->beginTransaction();
				$insertImagem = $di->getDb()->prepare('INSERT INTO imagem (idEvento, idMidia, idMidia2) VALUES (?, ?, ?)');
				
				foreach (glob("../libs/tmp/normal/*.*", GLOB_BRACE) as $filename) {
					$filename=basename($filename);	
					
					$_FILES['arquivo']['name'] = $filename;
					$_FILES['arquivo']['tmp_name'] = '../libs/tmp/normal/'.$filename;
					$_FILES['arquivo']['rename'] = true;
					
				//	print_r(array(array('name'=> 'arquivo', 'tmp_name' => '../libs/tmp/normal/'.$filename, 'type'=> 'img', 'dimension'=> array(array(242, 180)))));
					$result = PostAdmin::doMidia($di, array(array('name'=> 'arquivo', 'tmp_name' => '../libs/tmp/normal/'.$filename, 'type'=> 'img', 'dimension'=> array(array(242, 180)))), false, true);
					$idMidia = reset($result);
					
					$idMidia2 = null;
					$_FILES['arquivo2']['name'] = $filename;
					$_FILES['arquivo2']['tmp_name'] = '../libs/tmp/preview/'.$filename;
					$_FILES['arquivo2']['rename'] = true;
					if (is_file('../libs/tmp/preview/'.$filename)) {
						$result = PostAdmin::doMidia($di, array(array('name'=> 'arquivo2', 'tmp_name' => '../libs/tmp/preview/'.$filename, 'type'=> 'img', 'dimension'=> array(array(800, 600)))), false, true);
						$idMidia2 = reset($result);
					}
					
					$insertImagem->Execute(array($evento, $idMidia, $idMidia2));
					
				}
				$di->getDb()->commit();
				
	
				$palavra = Meta::getLangFile('operacao-admin', $di);
				$di->getSession()->setMessage($palavra, true);
				
				
				//return true;
			} catch (Exception $e) {
				$di->getDb()->rollBack();
				
	
				$palavra = Meta::getLangFile('erro1-cadastro', $di);
				$palavra1 = Meta::getLangFile('mensagem-admin', $di);
				$di->getSession()->setMessage($palavra.' - '.$palavra1.' '.$e->getMessage(), false);
				
				
	
				//return false;
			}
			
		}
		header('Location: '.URL.'admin/imagem/upload/');
	}
	
	
	function _default() {
		
		//LoginController::checkLogged();
		$args = func_get_args();
		
		if (!$args[0]) {
			//echo("oi");
			header('Location: '.URL.'/index/');
		} else 	if ($args[0] == 'index') {
			//echo("oi1");
			$this->_index();
		} else {
			//echo("oi2");
			//$this->_list($args[0]);
			call_user_func_array(array($this,'_list'), $args);
		}
	}
	
	
	
	function _list() {
		global $di;
		LoginController::checkLogged();
		$db = $di->getDb();
		$args = func_get_args();
		$info = Meta::getFromMeta($di, $args[0]);
		
		if (LoginController::isAllowed($args[0], 'list') < 1) {
			self::notAllowed($di);
		}
		
		if ($info) {
			$all = Meta::prepare($di, Meta::getMetaTabela($di, $args[0]), $info, $args);
			//echo '<pre>';
			//print_r($all);
			$di->getView()->meta = $all;
			
			$columns = array('t.id');
			$columnsShow = array();
			$inner = array();
			$where = array('1=1');
			$values = array();
			$filter = array();
			$filterSet = array();
			
			$args_text = '';
			$args_array = array();
			if (isset($args[2])) {
				$args_text .= $args[2].'/';
				$args_array[] = $args[2];
				if (isset($args[3])) {
					$args_text .= $args[3].'/';
					$args_array[] = $args[3];
				}
			}
			$di->getView()->args = $args_text;
			$di->getView()->args_arr = $args_array;

			foreach($all['columns'] as $column) {
				$columnsShow[] = $column;
				$options = $column['options'];
				if ($column['midia']) {
					$temp = 'a'.md5($column['name']);
					$inner[] = 'LEFT JOIN midia '.$temp.' ON '.$temp.'.id=t.'.$column['name'];
					$columns[] = $temp.'.file as '.$column['name'];
					$columns[] = $temp.'.type as '.$column['name'].'_type';
					$columns[] = $temp.'.id as '.$column['name'].'_id';
					$x = 1;
					foreach($column['dimensions'] as $dimension) {
						$columns[] = '"'.implode('/', $dimension).'" as '.$column['name'].'_dimension'.$x;
						$x++;
					}
				} else {
				
					if ($column['reference-1']){
						$where[] = $column['reference-1'].'=?';
						
						$values[] = $args[2];
					}
					
					if ($column['foreign']) {
						
						$temp = 'a'.md5($column['name']);
						$nomeTemp = $column['name'];
						if (in_array('-1', $column['options'])) {
							$nomeTemp = strtolower(substr($nomeTemp, 0, -1));
						}
						$inner[] = 'LEFT JOIN '.(Meta::getTableName($nomeTemp) != 'admin' ? '' : '').Meta::getTableName($nomeTemp).' '.$temp.' ON '.$temp.'.id=t.'.$column['name'];
						$columns[] = $temp.'.nome as '.$column['name'];
					}
					else {
					
						if ($column['datetime'] || in_array('datahora', $column['options'])) {
							$columns[] = 'DATE_FORMAT(t.'.$column['name'].', "%d/%m/%Y %H:%i") as '.$column['name'];
						} elseif ($column['date']) {
							
							$columns[] = 'DATE_FORMAT(t.'.$column['name'].', "%d/%m/%Y") as '.$column['name'];
						} else {
							if (self::isMoney($options)) {

								$columns[] = 'REPLACE(CONCAT("R$ ", t.'.$column['name'].'), ".", ",") as '.$column['name'];
							} else {
								$columns[] = 't.'.$column['name'];
							}
						}
					}
					
					if ($column['filter'] && $all['permissions']['filter']) {
						
						$filter[$column['filter']] = array('meta'=> $column, 'data' => ($column['foreign'] ? Meta::getTableContents($di, $column['filter']) : ''));
						
						
						$nome = $column['name'];
						
						if (isset($_POST['filter'])) {
							
							if ($filter[$column['filter']]['meta']['filter-type'] == 'date') {
								$_COOKIE[$nome.'0'] = (isset($_POST[$column['name']][0]) ? $_POST[$column['name']][0] : '');
								$_COOKIE[$nome.'1'] = (isset($_POST[$column['name']][1]) ? $_POST[$column['name']][1] : '');
								
							} else {
								//echo $column['name'];
								$_COOKIE[$nome] = (isset($_POST[$column['name']]) ? $_POST[$column['name']] : '');
							}
						}
						if ($filter[$column['filter']]['meta']['filter-type'] == 'date') {
							$filterSet[$column['name']][0] = (isset($_COOKIE[$nome.'0']) ? $_COOKIE[$nome.'0'] : '');
							$filterSet[$column['name']][1] = (isset($_COOKIE[$nome.'1']) ? $_COOKIE[$nome.'1'] : '');
						} else {
							$filterSet[$column['name']] = (isset($_COOKIE[$nome]) ? $_COOKIE[$nome] : '');
						}
					}
				}
			}
			
			
			foreach($filterSet as $id=>$f) {
				if ($f != '' && $id != '') {
					if ($filter[$id]['meta']['filter-type'] == 'date') {
						$data1 = $f[0];
						$data2 = $f[1];
						
						if ($data1) {
							$where[] = 't.'.$id.' >= ?';
							$values[] = Meta::converteDataHoraMysqlBd($data1);
						}
						
						if ($data2) {
							$where[] = 't.'.$id.' <= ?';
							$values[] = Meta::converteDataHoraMysqlBd($data2);
						}
						
					}
					else if (!$filter[$id]['data']) {
						$where[] = 't.'.$id.' LIKE ?';
						$values[] = '%'.str_replace(' ', '%', $f).'%';
					} else {
						$where[] = 't.'.$id.'=?';
						$values[] = $f;
					}
				}
			}
			
			//print_r($where);
			
			$controller = ucfirst($args[0]).'Controller';
			
			if (is_file('class/'.$controller.'.class.php')) {
				$reflector = new ReflectionClass($controller);
				if ($reflector->hasMethod('getNewActions')) {
					$di->getView()->extraActions = $controller::getNewActions();
				}
				
				if ($reflector->hasMethod('listFilter')) {
					$controller::listFilter($where, $values, $inner, $args);
				}
				
				if ($reflector->hasMethod('filterFilter')) {
					$controller::filterFilter($filter, $filterSet);
				}
			}
			
			//paginacao
			
			$di->getView()->columns = $columnsShow;
			$di->getView()->filters = $filter;
			$di->getView()->filtersSet = $filterSet;
			$results = $db->prepare("SELECT count(*) from ".($all['controller'] != 'admin' ? '': '').$all['controller']." t ".implode(' ', $inner).' WHERE '.implode(' AND ', $where));
			
			$results->Execute($values);
			$total = $results->fetch(PDO::FETCH_COLUMN);
			
			$paginas = ceil($total/RESULTADOSPAGINA);
			$pagina = (isset($args[1]) && $args[1] ? intval($args[1]) : 1);
			
			$di->getView()->totalResults = $total;
			$di->getView()->totalPages = $paginas;
			$di->getView()->page = $pagina;
			
			if ($all['order'] == 'ordem') {
				$columns[] = 't.ordem';
			}
			
			$results = $db->prepare("SELECT ".implode(',', $columns)." from ".($all['controller'] != 'admin' ? '' : '').$all['controller']." t ".implode(' ', $inner)." WHERE ".implode(' AND ', $where)." ORDER BY ".$all['order'].' '.$all['orderby'].'  LIMIT '.($pagina-1)*RESULTADOSPAGINA.','.RESULTADOSPAGINA);

			$results->Execute($values);
			if ($results->rowCount()) {
				$di->getView()->results = $results->fetchAll(PDO::FETCH_ASSOC);
			} else {
				$di->getView()->results = array();
			}
			
			$di->getView()->lng = LoginController::getLng($di);
			$di->getView()->load('list');
		}
	}
	
	
	function _order() {
		global $di;
		LoginController::checkLogged();
		try {
			$args = func_get_args();
			$infoTable = Meta::getFromMeta($di, $args[0]);
			$db = $di->getDb();
			
			if ($infoTable) {
				$values = Post::doPost(array('ordem'));
				
				$r = $db->prepare('UPDATE '.$args[0].' SET ordem=? WHERE id=?');
				
				foreach($values['ordem'] as $id=>$ordem) {
					$r->Execute(array($ordem, $id));
				}
				
				$controller = ucfirst($args[0]).'Controller';
				
				if (is_file('class/'.$controller.'.class.php')) {
					$reflector = new ReflectionClass($controller);
					if ($reflector->hasMethod('posOrder')) {
						
						if (isset($args[1])) {
							$controller::posOrder($args[1]);
						}
					}
				}
				
	
				$palavra = Meta::getLangFile('ordenacao-default', $di);
				$di->getSession()->setMessage($palavra, true);
								
			}
		} catch(Exception $e) {
			
			$palavra = Meta::getLangFile('erro-default', $di);
			$di->getSession()->setMessage($palavra, false);
			
		}
	}
	
	function _foreign_select() {
		global $di;
		
		$args = func_get_args();
		
		if (!isset($args[1])) {
			$arr = Meta::getForeignArr($di, $args[0]);
		} else {
			if (!isset($args[2])) { 
				$args[2] = '';
			}
			$arr = Meta::getForeignArr($di, $args[0], '', $args[1], $args[2]);
		}
		echo '<option value=""></option>';
		foreach($arr as $i=>$a) {
			$sel = ($args[1] == $i ? 'selected' : '');
			echo '<option value="'.$i.'" '.$sel.'>'.$a.'</option>';
		}
	}
	
	function _enviar() {
		global $di;
		LoginController::checkLogged();
		$args = func_get_args();
		$db = $di->getDb();

		$infoTable = Meta::getFromMeta($di, $args[0]);

		$modal = (isset($_GET['modal']) ? $_GET['modal'] : false);
		$back = (isset($_GET['back']) ? $_GET['back'] : false);
		
	
		if ($infoTable) {
			
			$di->getView()->modal = $modal;
			$ta = 'grupo_envio_conf';
		
			$r = $db->prepare("select tipo, texto from grupo_envio WHERE id=?");		
			$r->Execute(array($args[1]));
			$r1 = $r->fetch();


			if($r1['tipo'] == 'Evento'){
				
				$palavra = Meta::getLangFile('erro1-default', $di);
				$di->getSession()->setMessage($palavra, false);
			
				header('Location: '.URL.'/grupo_envio/list');
				die;
				exit();

			} 
			else if($r1['texto'] == 'Evento'){
				
				$palavra = Meta::getLangFile('erro1-default', $di);
				$di->getSession()->setMessage($palavra, false);
			
				
				header('Location: '.URL.'/grupo_envio/list');
				die;
				exit();

			} 			
			else{
				$fieldsMeta = Meta::getTabelaGenerica($di, $ta, (isset($args[1]) && $args[1] ? $args[1] : 0));
				
				$di->getView()->meta = Meta::prepare($di, $fieldsMeta, $infoTable, $args);

				//print_r($fieldsMeta);

				$args_text = '';
				$args_array = array();
				
				if (isset($args[2])) {
					$args_text .= $args[2].'/';
					$args_array[] = $args[2];
					if (isset($args[3])) {
						
						$args_text .= $args[3].'/';
						$args_array[] = $args[3];
					}
				}
				
				$di->getView()->args = $args_text;
				$di->getView()->args_arr = $args_array;
				$di->getView()->back = $back;
				
				
				$controller = ucfirst($args[0]).'Controller';
				if (is_file('class/'.$controller.'.class.php')) {
					$reflector = new ReflectionClass($controller);
					
					if ($reflector->hasMethod('setForeign')) {
						if (isset($args[2])) {
							$controller::setForeign($di->getView()->meta, $args[2]);
						}
					}
					
					if ($reflector->hasMethod('preForm')) {
						//if (isset($args[2])) {
							$controller::preForm($di->getView()->meta, $args);
						//}
					}
				}
				$di->getView()->tabs = Meta::prepareTabs($di->getView()->meta, $di);
				$di->getView()->lng = LoginController::getLng($di);
				if ($modal) {
					$di->getView()->load('enviar', false, false, false);
				} else {				
					$di->getView()->load('enviar');
				}
			}
		}
	}
	
	
	function _add() {
		global $di;
		LoginController::checkLogged();
		$di->getView()->lng = LoginController::getLng($di);
		$args = func_get_args();

		$infoTable = Meta::getFromMeta($di, $args[0]);
		
		
		$perm1 = LoginController::isAllowed($args[0], 'add');
		if (!$perm1) {
			$perm1 = LoginController::isAllowed($args[0], 'view');
		}
		
		if (!$perm1) {
			die;
			self::notAllowed($di);
		}
		
		if ($perm1 === 2) {
			if (!isset($args[1]) || !LoginController::checkSelf($di, $args[1])) {
				self::notAllowed($di);
			}

			$selfonly = true;
		}
		
		

		$modal = (isset($_GET['modal']) ? $_GET['modal'] : false);
		$back = (isset($_GET['back']) ? $_GET['back'] : false);
		
		if ($infoTable) {
			
			$di->getView()->modal = $modal;
			$fieldsMeta = Meta::getTabelaGenerica($di, $args[0], (isset($args[1]) && $args[1] ? $args[1] : 0));
			
			$di->getView()->meta = Meta::prepare($di, $fieldsMeta, $infoTable, $args);

			
			$args_text = '';
			$args_array = array();
			
			if (isset($args[2])) {
				$args_text .= $args[2].'/';
				$args_array[] = $args[2];
				if (isset($args[3])) {
					
					$args_text .= $args[3].'/';
					$args_array[] = $args[3];
				}
			}
			
			$di->getView()->args = $args_text;
			$di->getView()->args_arr = $args_array;
			$di->getView()->back = $back;
			
			
			$controller = ucfirst($args[0]).'Controller';
			if (is_file('class/'.$controller.'.class.php')) {
				$reflector = new ReflectionClass($controller);
				
				if ($reflector->hasMethod('setForeign')) {
					if (isset($args[2])) {
						$controller::setForeign($di->getView()->meta, $args[2]);
					}
				}
				
				if ($reflector->hasMethod('preForm')) {
					//if (isset($args[2])) {
						$controller::preForm($di->getView()->meta, $args);
					//}
				}
			}
			$di->getView()->tabs = Meta::prepareTabs($di->getView()->meta, $di);
			
			if ($modal) {
				$di->getView()->load('add', false, false, false);
			} else {				
				$di->getView()->load('add');
			}
		}
	}
	
	public static function notAllowed($di) {
		
		$palavra = Meta::getLangFile('acesso-default', $di);
		~$di->getSession()->setMessage($palavra, false);
		
		header("Location: ".URL);
		exit();
	}

	
	function _del() {
		global $di;
		LoginController::checkLogged();
		$db = $di->getDb();
		$args = func_get_args();
		$perm1 = LoginController::isAllowed($args[0]);
		if (!$perm1 || $perm1 == 2) {
			self::notAllowed($di);
		}
		
		try {
			if ($args[0] && $args[1]) {
				
				if($args[0] == 'grupo_envio' && ($args[1] == 1  || $args[1] == 2 )){
					
					$palavra = Meta::getLangFile('nao-default', $di);
					throw new Exception($palavra);
					
				}else{
					$r = $db->prepare("DELETE from ".($args[0] != 'admin' ? '': '').$args[0].' WHERE id=?');
					
					$r->Execute(array($args[1]));

					if ($r->rowCount()) {
						$palavra = Meta::getLangFile('exclusao-default', $di);
						$di->getSession()->setMessage($palavra, true);
									
					} else {
						$palavra = Meta::getLangFile('erro2-default', $di);
						throw new Exception($palavra);
												
					}
				}
			}
		} catch(Exception $e) {
			$palavra = Meta::getLangFile('erro1-cadastro', $di);
			$di->getSession()->setMessage($palavra.': '.$e->getMessage(), false);
			
		}
	}
	
	function _inline_edit() {
		global $di;
		LoginController::checkLogged();
		try {
			$args = func_get_args();
			$infoTable = Meta::getFromMeta($di, $args[0]);
			if ($infoTable) {
				$fieldsMeta = Meta::getMetaTabela($di, $args[0]);
				$temp1 = $fieldsMeta['id'];
				$temp2 = $fieldsMeta[$_POST['columnName']];
				
				
				$values = Post::doPost(Meta::getFields(array('id'=> $temp1, $_POST['columnName']=>$temp2)));
				
				if (in_array('date', $fieldsMeta[$_POST['columnName']]['comment'])) {
					if ($values[$_POST['columnName']]) {
						$values[$_POST['columnName']] = Meta::converteDataHoraMysqlBd($values[$_POST['columnName']]);
					}
				}
				$di->getDb()->replace($args[0], $values);
			}
			//print_r($values);
		} catch(Exception $e) {
			echo '0';
		}
		
	}

	function _change(){
        global $di;
        $db = $di->getDb();
		$args = func_get_args();
        $di->getView()->exibir = "nao";

        $zerar = $db->prepare("UPDATE idioma set ativo=0");
		$zerar->Execute();
        
        if(in_array("pt-br", $args)){
        	$atualizar = $db->prepare("UPDATE idioma set ativo=1 where lang='pt-br'");
			$atualizar->Execute();

            $di->getSession()->setSessionValue('idioma', "pt-br");
            $di->getView()->lng = parse_ini_file('pt-br.lang');
            //$lang = (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'pt');

        }
        else if(in_array("fr", $args)){
        	$atualizar = $db->prepare("UPDATE idioma set ativo=1 where lang='fr'");
			$atualizar->Execute();

            $di->getSession()->setSessionValue('idioma', "fr");
            $di->getView()->lng = parse_ini_file('fr.lang');
            //$lang = (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'pt');

        }
        else if(in_array("de", $args)){
        	$atualizar = $db->prepare("UPDATE idioma set ativo=1 where lang='de'");
			$atualizar->Execute();

            $di->getSession()->setSessionValue('idioma', "de");
            $di->getView()->lng = parse_ini_file('de.lang');
            //$lang = (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'pt');

        }
        else if(in_array("es", $args)){
        	$atualizar = $db->prepare("UPDATE idioma set ativo=1 where lang='es'");
			$atualizar->Execute();

            $di->getSession()->setSessionValue('idioma', "es");
            $di->getView()->lng = parse_ini_file('es.lang');
            //$lang = (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'pt');

        }
        else
        {
            $di->getSession()->setSessionValue('idioma', "en");
            $di->getView()->lng = parse_ini_file('en.lang');
            //$lang = (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'en');

        }

        $logado = LoginController::isLogged($di);
        if($logado){
        	$di->getView()->load('index');
        }else{
        	$di->getView()->load('login');
        }
        
    }
    
	function _add_action() {
		global $di;
		LoginController::checkLogged();
		try {
			$ajax = (isset($_GET['ajax']) ? $_GET['ajax'] : false);
			$args = func_get_args();
			
			$infoTable = Meta::getFromMeta($di, $args[0]);		
			
			if ($infoTable) {
				
				$options = explode(' ', $infoTable['options']);
				
				$fieldsMeta = Meta::getMetaTabela($di, $args[0]);
				//$valuesAfter = array();
				
				
				$values = Post::doPost(Meta::getFields($fieldsMeta));
				
				
				$selfonly = false;
				$perm1 = LoginController::isAllowed($args[0], 'add');
				if (!$perm1) {
					$perm1 = LoginController::isAllowed($args[0], 'edit');
					if (!$perm1 || !$values['id']) {
						self::notAllowed($di);
					}
				}
				if ($perm1 == 2) {
					if (!isset($values['id']) || LoginController::getUserId($di) != $values['id']) {
						self::notAllowed($di);
					}
					$selfonly = true;
				}
				
			
				$controller = ucfirst($args[0]).'Controller';
				if (is_file('class/'.$controller.'.class.php')) {
				//if (is_file('class/'.$controller.'.class.php')) {
					$reflector = new ReflectionClass($controller);
					if ($reflector->hasMethod('filterPost')) {
						$controller::filterPost($fieldsMeta, $args, $values);
					}
					
				}
				$reference = '';
				foreach($fieldsMeta as $i=>$field) {
					if (in_array('admin', $field['comment']) && !$values['id']) {
						//$values[$i] = LoginController::getTipo($di).LoginController::getUserId($di);
						$values[$i] = LoginController::getUserId($di);
						//$valuesAfter[$i] = LoginController::getUserId($di);
						unset($fieldsMeta[$i]);
					}
					
					if (in_array('dont-send', $field['comment'])) {
						unset($fieldsMeta[$i], $values[$i]);
					}
					
					if (in_array('date', $field['comment'])) {
						if ($values[$i]) {
							$values[$i] = Meta::converteDataHoraMysqlBd($values[$i]);
						}
					}
					if (in_array('null', $field['comment'])) {
						if ($values[$i] == '') {
							$values[$i] = null;
						}
					}
					
					if (in_array('reference-1', $field['comment'])) {
						$reference = $field;
					}
				}
				
				
				$files = PostAdmin::doMidia($di, Meta::getFieldsMidia($fieldsMeta));
				
				foreach($files as $id=>$file) {
					$values[$id] = $file;
				}

				
				$outros = Post::doPost(array('remove', 'back'));
				
				if ($outros['remove']) {
					foreach($outros['remove'] as $id=>$m) {
						$values[$id] = null;
					}
				}

				if (isset($values['senha'])) {
					if ($values['id']) {
						if (!$values['senha']) {
							unset($values['senha']);
						}
					}
				}

				
				if (!$values['id'] && in_array('order', $options)) {
					$where = '';
					
					if ($args[1] && $reference) {
						//echo $reference;
						$where = ' WHERE '.$reference['name'].'='.$args[1];
					}
					
					$r = $di->getDb()->query("SELECT MAX(ordem) from ".($args[0] != 'admin' ? '': '').$args[0].$where);
					$maxOrdem = $r->fetch(PDO::FETCH_COLUMN);
					$values['ordem'] = $maxOrdem+1;
				}
				

				
				$controller = ucfirst($args[0]).'Controller';
				if (is_file('class/'.$controller.'.class.php')) {
				//if (is_file('class/'.$controller.'.class.php')) {
					$reflector = new ReflectionClass($controller);
					if ($reflector->hasMethod('beforeSave')) {
						$controller::beforeSave($values, $args);
					}
					
				}
				
				
				$result = $di->getDb()->replace(($args[0] != 'admin' ? '' : '').$args[0], $values);
				
				
				
				if (!$values['id']) {
					$values['id'] = $di->getDb()->getInsertId();
				}
				
				
				$controller = ucfirst($args[0]).'Controller';
				
				if (is_file('class/'.$controller.'.class.php')) {
				//if (is_file('class/'.$controller.'.class.php')) {
					$reflector = new ReflectionClass($controller);
					if ($reflector->hasMethod('afterSave')) {
						$controller::afterSave($values['id'], $args);
					}
					
				}
				
				
				$idForeign = 'id'.ucwords($args[0]);
				
				foreach($fieldsMeta as $field) {
					if (in_array('1n', $field['comment']) && !in_array('disabled', $field['comment'])) {
						$arrSkip = array('0');
						
						
						
						
						$fieldsMeta2 = Meta::getMetaTabela($di, $field['name']);
						
						$fields = array();
						$midias = array();
						$skipifnull = array();
						foreach($fieldsMeta2 as $field2) {
							if (in_array('visible', $field2['comment']) || in_array('required', $field2['comment']) || in_array('hidden', $field2['comment']) || in_array('v', $field2['comment']) || in_array('r', $field2['comment'])) {
								
								if (in_array('null', $field2['comment'])) {
									$skipifnull[] = $field2['name'];
								}
								if (in_array('midia', $field2['comment'])) {
									$midias[$field2['name']] = array($field['name'].'_'.$field2['name'], $field2['comment']);
								}
									$fields[$field2['name']] = $field['name'].'_'.$field2['name'];
								
							}
						}

						
						
						if (count($fields)) {
							$primeiro = Post::doPost(array(reset($fields)));	
							
							$primeiro = array_keys(reset($primeiro));
							foreach($primeiro as $x) {
								$arrDados = array();
								$arrDados[$idForeign] = $values['id'];
								$sskip = false;
								foreach($fields as $c=>$f) {
								
									if (isset($midias[$c])) {
										
									
										$info = array();
										$info[0]['comment'] = $midias[$c][1];
										$info[0]['name'] = $midias[$c][0];

										$files = PostAdmin::doMidia($di, Meta::getFieldsMidia($info), $x);

										foreach($files as $id=>$file) {
											if($file) {
												$arrDados[$c] = $file;
											}
										}
										
										if (isset($outros['remove'][$f.'['.$x])) {
											$arrDados[$c] = null;
										}
										
										
									} else {
										$d = Post::doPost(array($fields[$c]));
										
										$arrDados[$c] = isset($d[$fields[$c]][$x]) ? $d[$fields[$c]][$x] : '';
									}
								}
								if (count($skipifnull) && !$arrDados[$skipifnull[0]]) {
									continue;
								}
								
								$di->getDb()->replace($field['name'], $arrDados);

								$arrSkip[] = ($arrDados['id'] ? $arrDados['id'] : $di->getDb()->getInsertId());
							}
						}

						$placeHolders = implode(', ', array_fill(0, count($arrSkip), '?'));
						
						$clear = $di->getDb()->prepare('DELETE FROM '.$field['name'].' WHERE id NOT IN ('.$placeHolders.') AND '.$idForeign.'=?');
						
						foreach ($arrSkip as $index => $value) {
							$clear->bindValue($index+1, $value, PDO::PARAM_INT);
						}
						$clear->bindValue(count($arrSkip)+1, $values['id'], PDO::PARAM_INT);
						
						$clear->Execute();
						
					}
				}
				
				$args_text = '';
				//$args_array = array();
				if (isset($args[1])) {
					$args_text .= $args[1].'/';
					//$args_array[] = $args[1];
					if (isset($args[2])) {
						$args_text .= $args[2].'/';
						//$args_array[] = $args[2];
					}
				}
				//print_r($args_text);
				
				if ($ajax) {
					echo json_encode(array('status'=>1, 'message' => 'ok', 'id'=> $values['id']));
				} else {
					
					$palavra = Meta::getLangFile('operacao-admin', $di);
					$di->getSession()->setMessage($palavra, true);
								
					
					if ($selfonly) {
						header("Location: ".URL.'/'.$args[0].'/add/'.$values['id']);
					} else if ($outros['back']) {
						header("Location: ".URL.'/'.$args[0].'/add/0/'.$args_text.'?back=1');
					} else {
						header("Location: ".URL.'/'.$args[0].'/list/1/'.$args_text);
					}
				}
				
			}
		} catch(Exception $e) {
			if ($ajax) {
				echo json_encode(array('status'=>0, 'message' => $e->getMessage()));
			} else {
				
				$palavra = Meta::getLangFile('erro1-cadastro', $di);
				$di->getSession()->setMessage($palavra.': '.$e->getMessage(), false);
					
				if ($selfonly) {
					header("Location: ".URL.'/'.$args[0].'/add/'.$values['id']);
				} else {
					header("Location: ".URL.'/'.$args[0].'/add/');
				}
			}
		}
	}
}