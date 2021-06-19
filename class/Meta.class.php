<?php
//ok
/**
 *	
 *
 *	@author Otávio Tralli <otavio@tralli.org>
 *	@version 1.0
*/
class Meta {
	use ParseOptions;
	const TABLE = 'meta';

	public static $id = 0;
	
	public static function getLangFile($palavra, $di){
		$arquivo = LoginController::getLng($di);
		$traduzida = $arquivo[$palavra];
		return $traduzida;
	}
	
	public static function getTableContents($di, $table) {
		$db = $di->getDb();
		
		$q = $db->prepare('SELECT id, nome FROM '.(self::getTableName($table) != 'admin' ? '' : '').self::getTableName($table).' ORDER BY '.(self::getTableName($table) == 'mes' ? 'id' : 'nome').' ASC');
		$q->Execute(array());
		$dados = $q->fetchAll(PDO::FETCH_ASSOC);
		$retorno = array();
		foreach($dados as $dado) {
			$retorno[$dado['id']] = $dado['nome'];
		}
		return $retorno;
	}
	
	public static function getTableName($text) {
		return strtolower(substr($text, 2));
	}
	
	public static function getFields($info) {
		$arr = array();
		
		foreach($info as $i) {
			$comment = $i['comment'];
			
			$name = (isset($i['name'])) ? $i['name'] : '';
			if (in_array('id', $comment) || in_array('v', $comment) || in_array('visible', $comment) || in_array('hidden', $comment) || in_array('h', $comment) || in_array('required', $comment) || in_array('r', $comment)) {
				if (!in_array('midia', $comment)) {
					$arr[] = $name;
				}
			}
		}
		return $arr;
	}
	
	public static function generateDimensionArray($string) {
		$matches = preg_grep('/(\d+)x(\d+)/', $string);
		
		$dimension = array();
		if ($matches) {
			foreach($matches as $m) {
				list($width, $height) = explode('x', $m);
				$dimension[] = array($width, $height);
			}
			
		}
		return $dimension;
	}
	public static function getFieldsMidia($info) {
		$arr = array();
		foreach($info as $i) {
			$comment = $i['comment'];
			
			$name = (isset($i['name'])) ? $i['name'] : '';
			
			if (in_array('midia', $comment)) {
				$dimension = array();
				$type = 'general';
				if (in_array('img', $comment)) {
					$type = 'img';
					$dimension = self::generateDimensionArray($comment);
				}
				$arr[] = array('name'=> $name, 'type' => $type, 'dimension' => $dimension);
			}
			
		}
		return $arr;
	}
	
	public static function getFiles($info) {
		$arr = array();
		foreach($info as $i) {
			$comment = $i['comment'];
			$name = (isset($i['name'])) ? $i['name'] : '';
			if (in_array('v', $comment) || in_array('visible', $comment) || in_array('hidden', $comment) || in_array('h', $comment) || in_array('required', $comment) || in_array('r', $comment)) {
				if (in_array('midia', $comment)) {
					$arr[] = $name;
				}
			}
		}
		return $arr;
	}
	
	public static function getForeignArr($di, $table, $foreign2= '', $cond_field = '', $cond_value= '') {
		$db = $di->getDb();
		
		if ($foreign2) {
			
			$chave1 = array_pop($foreign2);
			$valor1 = array_pop($foreign2);
			
			$inner = array();
			$where = '';
			//print_r($foreign2);
			for($x=count($foreign2)-1;$x>=0;$x=$x-2) {
				if ($x==count($foreign2)-1) {
					$inner[] = 'INNER JOIN '.$foreign2[$x-1].' t'.$x.' ON   t'.$x.'.'.end($foreign2).'='.$valor1;
				
				} else {
					$inner[] = 'LEFT JOIN '.$foreign2[$x-1].' t'.$x.' ON t'.$x.'.'.$foreign2[$x+2].'=t'.($x+2).'.'.$foreign2[$x+2];
				}
			}

			$order = ($table == 'mes' ? 't.id' : 't.nome');
			$result = $db->query('SELECT t.* FROM '.$table.' t '.implode(' ', $inner).'  ORDER BY '.$order.' ASC')->fetchAll(PDO::FETCH_ASSOC);
			//echo 'SELECT t.* FROM '.$table.' t '.implode(' ', $inner).'  ORDER BY '.$order.' ASC';
		} else {
			try {
				$where = array('1=1');
				$values = array();
				$order = ($table == 'mes' ? 'id' : 'nome');
				
				if ($cond_field && $cond_value) {
					$where[] = $cond_field.'=?';
					$values[] = $cond_value;
				}
				//echo 'SELECT * FROM '.($table != 'admin' ? PROJECT_PREFIX: '').$table.' WHERE '.implode(' AND ', $where).' ORDER BY '.$order.' ASC';
				$sel = $db->prepare('SELECT * FROM '.($table != 'admin' ? '': '').$table.' WHERE '.implode(' AND ', $where).' ORDER BY '.$order.' ASC');
				
				$sel->Execute($values);
				
				$result = $sel->fetchAll();
			}catch(Exception $e) {
			}
		}
		
		$arr = array();
		foreach($result as $r) {
			$arr[$r['id']] = $r['nome'];
		}
		return $arr;
	}
	
	public static function prepareMulti($di, $info, $name, $titulo, $disabled=false, $permissions, $args) {
		$primeiro = reset($info);
		$viewonly = false;
		if (!$permissions['add']) {
			if ($permissions['view']) {
				$viewonly = true;
			} else {
				return;
			}
		}
		
		$total = (isset($primeiro['valor']) ? count($primeiro['valor']) : 1);
		
		echo '<fieldset style="border:1px solid gray;padding:10px;border-radius:3px;margin-bottom:10px;clear:both" class="repositorio">';
		if ($titulo) {
			echo '<legend style="width:auto;padding-left:10px;padding-right:10px;border:0;">'.$titulo.'</legend>';
		}
		if (!$disabled && !$viewonly) {
			$palavra = self::getLangFile('novo-meta', $di);
			echo '<a href="#" class="novo_multi"><i class="glyphicon glyphicon-plus"></i>'.$palavra.'</a>';
						
		}
		
		for($x=0;$x<$total;$x++) {
		
			echo '<fieldset class="'.($x==0?'modelo':'').' clear pai multiplo">';
			if (!$disabled && !$viewonly) {
				echo '<span class="remover"><i class="glyphicon glyphicon-remove"></i></span>';
			}
			//echo $x;
			//$y = 0;
			//echo '|';
			foreach($info as $item) {
				
				//$y++;
				//echo '<pre>';
				//print_r($item);
				//$temp = $item;
				$item['valor'] = (isset($item['valor'][$x]) ? $item['valor'][$x] : '');
				$comment = $item['comment'];
				
				if (in_array('foreign2', $comment)) {
					//die;
					$matches = preg_grep('/\!(.+)\!/', $comment);
					//print_r($matches);
					
					
					if ($matches) {
						$foreign2 = array();
						
						$item['foreign2'] = explode('|', rtrim(ltrim(reset($matches), '!'), '!'));
						//print_r($info[$item['foreign2'][1]]);
						//$idTemp = (isset($info[$item['foreign2'][1]]['valor'][0]) ? $info[$item['foreign2'][1]]['valor'][0] : '');
						//print_r($info[$item['foreign2'][1]]);
						//print_r($info[$item['foreign2'][1]]['valor']);
						
						//$item['foreign2'][] = $args[0];
						
						
						if ($args[0]) $item['foreign2'][] = $args[0];
						if (in_array('-14', $comment)) {
							$item['foreign2'][]  = (substr($item['name'], 0, -14));
						} else if (in_array('-1', $comment)) {
							$item['foreign2'][]  = strtolower(substr($item['name'], 0, -1));
						} else {
							$item['foreign2'][] = $item['name'];
						}
						
					//	print_r($item);
						//echo $idTemp;
					}
				}
				
				
				self::gInput($item, array(), array(), $name, 2, $x, '', $permissions);
				
			}
			echo '<span class="clear">&nbsp;</span>';
			echo '</fieldset>';
		}
		//echo '<div class="repositorio"></div>';
		echo '</fieldset>';
	}
	public static function  converteDataHoraMysqlUser($data) {

		$temp = explode(' ',$data);
		
		$hora = '';
		if (count($temp) == 2) {
			$temp2= explode(':', $temp[1]);
			$hora = ' '.$temp2[0].':'.$temp2[1];
		}
		$dat = implode('/', array_reverse(explode('-', $temp[0])));
		return $dat.$hora;
	}
	
	public static function  converteDataHoraMysqlBd($data) {
		
		$temp = explode(' ',$data);
		
		$hora = '';
		if (count($temp) == 2) {
			$temp2= explode(':', $temp[1]);
			$hora = ' '.$temp2[0].':'.$temp2[1];
		}
		$dat = implode('-', array_reverse(explode('/', $temp[0])));
		return $dat.$hora;
	}
	
	public static function gInput($info, $args = array(), $args2 = array(), $prepend = '', $div = false, $x=0, $tabs = array(), $permissions) {
		global $di, $current_tab;
		if (!is_array($info)) {
			return;
		}
		
		$viewonly = false;
		if (!$permissions['add'] && !$permissions['edit']) {
			if ($permissions['view']) {
				$viewonly = true;
			} else {
				return;
			}
		}
		$arr=array();
		//print_r($info['name'].",");

		$name = (isset($info['name'])) ? $info['name'] : '';
		/*if(isset($info['name'])){
			$name = $info['name'];
			print_r($info['name']."-".$info['type'].",");
			//echo "oi";
		}else{
			$name = '';
			print_r($info['name']."-".$info['type'].",");
			//echo "ola";
		}*/

		$oldname = $name;
		//print_r($info);
		
		if ($prepend) {
			$name = $prepend.'_'.$name.'['.$x.']';
			//print_r($name);
		
		}
		$id = str_replace('.', '', 'inp'.uniqid('', true).$name);
		
		$valor = (isset($info['valor'])) ? $info['valor'] : '';
		
		$default = (isset($info['default'])) ? $info['default'] : '';
		
		if ($valor == ''  && $default && $default != 'CURRENT_TIMESTAMP') {
			$valor = $default;
		}
		
		$maxlength = (isset($info['maxlength'])) ? $info['maxlength'] : '';
		$foreign2 = (isset($info['foreign2'])) ? $info['foreign2'] : '';
		
		
		$nodesign = (isset($info['nodesign']) ? $info['nodesign'] : false);
		$tipo = $info['type'];
		
		$comment = $info['comment'];
		$enum = $info['enum'];

		
		$tab = (isset($info['tab']) ? $info['tab'] : -1);
		$class = array();
		
		if ($tipo == 'date') {
			$maxlength = 10;
			$valor = implode('/',array_reverse(explode('-',$valor)));
		}
		if ($tipo == 'year') {
			$tipo = 'varchar';
			$maxlength=4;
			$class[]='year';
		}
		if ($tipo == 'datetime') {
			$maxlength = 16;
			$valor = self::converteDataHoraMysqlUser($valor);
		}
		
		if ($tipo == 'text') {
			$tipo = 'textarea';
		}
		if ($tipo == 'datetime' || $tipo == 'timestamp') {
			$maxlength = 16;
		}
		
		$break_tab = (in_array('break-tab', $comment));
		$counter = false;
		$readonly = false;
		$disabled = false;
		$foreign = false;
		$foreign_table = '';
		$label = '';
		$dontsend = false;
		$virtual = false;
		$onchange = '';
		
		if ($break_tab) {
			echo '<span class="clearfix"></span>';
			echo ' </div></div>';
			$tab = -1;
		}
		
		if ($comment) {
			// check for title

			$matches = preg_grep('/\[([^\[]+)\]/', $comment);
			if ($matches) {
				$label = str_replace('_', ' ', rtrim(ltrim(reset($matches), '['), ']'));
				
				//print_r("Label:".$label.",");
				//nome campos pagina
				
				if($label == "CPF"){
					$label = self::getLangFile('cpf_cadastro', $di);;
				}
				if($label == "Data de Nascimento"){
					$label = self::getLangFile('data_cadastro', $di);
				}
				if($label == "Tipo"){
					$label = self::getLangFile('tipo-meta', $di);
				}
				if($label == "Escolas"){
					$label = self::getLangFile('escolas-meta', $di);
				}
				if($label == "Nome"){
					$label = self::getLangFile('nome_cadastro', $di);
				}
				if($label == "Grupo"){
					$label = self::getLangFile('grupo-meta', $di);
				}
				if($label == "Classificação"){
					$label = self::getLangFile('classificacao-meta', $di);
				}
				if($label == "Configuração"){
					$label = self::getLangFile('configuracao-meta', $di);
				}
				if($label == "Celular"){
					$label = self::getLangFile('celular_cadastro', $di);
				}	
				if($label == "Operadora"){
					$label = self::getLangFile('operadora_cadastro', $di);
				}
				if($label == "Imagem"){
					$label = self::getLangFile('imagem-meta', $di);
				}
				if($label == "Senha"){
					$label = self::getLangFile('senha_cadastro', $di);
				}
				if($label == "Opção"){
					$label = self::getLangFile('opcao-meta', $di);
				}
				if($label == "Pergunta"){
					$label = self::getLangFile('pergunta_relatorio', $di);
				}
				if($label == "Obrigatório"){
					$label = self::getLangFile('obrigatorio-meta', $di);
				}
				if($label == "Numérico"){
					$label = self::getLangFile('numerico-meta', $di);
				}
				if($label == "Telefone"){
					$label = self::getLangFile('telefone-meta', $di);
				}
				if($label == "E-mail"){
					$label = self::getLangFile('email_cadastro', $di);
				}
				if($label == "Escola"){
					$label = self::getLangFile('NAME_entidade', $di);
				}
				if($label == "Permitir"){
					$label = self::getLangFile('oermitir-meta', $di);
				}
				if($label == "Outro"){
					$label = self::getLangFile('outro-meta', $di);
				}
				if($label == "Dimensão"){
					$label = self::getLangFile('dimensao-meta', $di);
				}
				if($label == "Período de informações"){
					$label = self::getLangFile('periodo-meta', $di);
				}
				if($label == "Grupo de Envio"){
					$label = self::getLangFile('grupo-meta', $di);
				}
				if($label == "Código"){
					$label = self::getLangFile('codigo_cadastro', $di);
				}
				if($label == "Distrito"){
					$label = self::getLangFile('distrito-meta', $di);
				}
				if($label == "Município"){
					$label = self::getLangFile('municipio-meta', $di);
				}
				if($label == "Diretoria"){
					$label = self::getLangFile('diretoria-meta', $di);
				}
				if($label == "Eventos"){
					$label = self::getLangFile('eventos_eventos', $di);
				}
				if($label == "Avaliacao"){
					$label = self::getLangFile('avaliacao_avalia', $di);
				}
				if($label == "Professor_Responsável"){
					$label = self::getLangFile('professor-meta', $di);
				}
				if($label == "Agrupamento"){
					$label = self::getLangFile('agrupamento-meta', $di);
				}
				if($label == "Ra"){
					$label = self::getLangFile('ra-meta', $di);
				}
				if($label == "Turno"){
					$label = self::getLangFile('turno-meta', $di);
				}
				if($label == "Programa"){
					$label = self::getLangFile('programa-meta', $di);
				}
				if($label == "Nome Curto"){
					$label = self::getLangFile('nomecurto-meta', $di);
				}
				if($label == "Responsavel"){
					$label = self::getLangFile('responsavel-meta', $di);
				}
				if($label == "Texto"){
					$label = self::getLangFile('texto-meta', $di);
				}
				if($label == "Texto Envio"){
					$label = self::getLangFile('texto1-meta', $di);
				}
				if($label == "Notas"){
					$label = self::getLangFile('notas-meta', $di);
				}
				if($label == "Descrição"){
					$label = self::getLangFile('descricao-meta', $di);
				}
				if($label == "Inicio"){
					$label = self::getLangFile('inicio-meta', $di);
				}
				if($label == "Termino"){
					$label = self::getLangFile('termino-meta', $di);
				}
				if($label == "Data Envio Diretor Todos"){
					$label = self::getLangFile('data-meta', $di);
				}
				if($label == "Data Envio Professor Todos"){
					$label =self::getLangFile('data1-meta', $di);
				}
				if($label == "EnviadoSMSDT"){
					$label = self::getLangFile('enviado-meta', $di);
				}
				if($label == "EnviadoSMSPT"){
					$label = self::getLangFile('enviado1-meta', $di);
				}
				if($label == "Chave"){
					$label = self::getLangFile('chave-meta', $di);
				}
				if($label == "Categoria"){
					$label = self::getLangFile('categoria-meta', $di);
				}
				if($label == "Valor"){
					$label = self::getLangFile('valor-meta', $di);
				}
				if($label == "Arquivo"){
					$label = self::getLangFile('arquivo_import', $di);
				}
				

				//print_r($label);
				//nome pagina
				if (strpos($label, '*') === 0) {
					$temp = str_replace('*', '', $label).'Controller';
					$label = $temp::getConstName($di);
					//$label = $temp::NAME;
					//print_r($label);
				}
			}
			
			//check for on change
			
			$matches = preg_grep('/\&([^\[]+)\&/', $comment);

			if ($matches) {
				$onchange = explode('|', rtrim(ltrim(reset($matches), '&'), '&'));
				$class[] = 'onchange';
			}
			
			if ($tab >=0) {
					if (!isset($current_tab)) {
						$current_tab = 0;
						//if 
						
						echo '<div>';
						echo '<ul class="nav nav-tabs" role="tablist">';
						foreach($tabs as $i=>$t) {
							$a = urlAmigavel($t);
							echo '<li role="presentation" '.(!$i ? 'class="active"' : '').'><a href="#'.$a.'" aria-controls="'.$a.'" role="tab" data-toggle="tab">'.$t.'</a></li>';
						}
						echo '</ul>';
						echo '<div class="tab-content">';
						echo '<div role="tabpanel" class="tab-pane active" id="'.urlAmigavel($tabs[0]).'">';
						//echo '---------------';
					} else {
					if ($current_tab !== $tab) {
						
							echo '<span class="clearfix"></span>';
							echo '</div>';
						
						
						$current_tab = $tab;
						
						echo '<div role="tabpanel" class="tab-pane clear" id="'.urlAmigavel($tabs[$tab]).'">';
					}
					}
					
			}
			
			if (in_array('1n', $comment)) {
				$fieldsMeta = Meta::getTabelaGenerica2($di, $name, self::$id, 'id'.ucwords($info['tabela']));
				
				
				$controller = ucfirst($info['tabela']).'Controller';
				if (is_file('class/'.$controller.'.class.php')) {
					$reflector = new ReflectionClass($controller);
					if ($reflector->hasMethod('pre1n')) {
						//if (isset($args[2])) {
							$controller::pre1n($fieldsMeta, $args);
						//}
					}
				
				}
				self::prepareMulti($di, $fieldsMeta, $name, $label, (in_array('disabled', $comment)), $permissions, $args);
				
				//print_r($fieldsMeta);
				return;
				
			}
			if (((!in_array('visible', $comment) && !in_array('v', $comment)) && (!in_array('required', $comment) && !in_array('r', $comment)))  && !in_array('hidden', $comment) && !in_array('h', $comment)) {
				return;
			}
			
			if (self::isForeign($comment)) {
				$foreign = true;
				
				
				
				if (in_array('-1', $comment)) {
					$foreign_table = strtolower(substr($oldname, 2, -1));
				} else {
					$foreign_table = strtolower(substr($oldname, 2));
				}
				
				
				if (in_array('admin', $comment)) {
					
					$tipo = substr($valor, 0, 1);
					$valor = substr($valor, 1);
					//echo $tipo;
					$foreign_table = LoginController::getTableAcl($di, $tipo);
				}
			}
			
			
			$fakeHidden = false;
			
			if (in_array('id', $comment)) {
				
				if ($valor) {
					self::$id = $valor;
					$valorTemp = $valor;
				} else if (in_array('getnext', $comment)) {
					//$query = $di->getDb()->query('SELECT AUTO_INCREMENT FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'DatabaseName' AND TABLE_NAME   = "'.$info['tabela'].'"');
					$q = $di->getDb()->query("SHOW TABLE STATUS LIKE '".$info['tabela']."'");
					$next = $q->fetch(PDO::FETCH_ASSOC);
					$valorTemp = $next['Auto_increment'];
				}
				
				//self::gInput(array());
				if (!in_array('hidden', $comment)) {
					$fakeHidden = $valor;
					$valor = $valorTemp;
					$disabled = true;
				} else {
					$tipo='hidden';
				}
			}
			
			if (in_array('m', $comment) || in_array('midia', $comment)) $tipo='file';
			if (in_array('virtual', $comment)) $virtual=true;
			if (in_array('select', $comment)) {
				//$arr= array();
				//$tipo = 'enum';
			}
			if (in_array('read-only', $comment)) $readonly=true;
					//print_r($comment);
			
			
			
			if (in_array('dont-show', $comment) && !$valor) {
				return;
			}
			if (in_array('dont-send', $comment)) {
				$dontsend=true;
				$name = '';
			}
			if (in_array('disabled', $comment)) $disabled=true;
			if (in_array('required', $comment) || in_array('r', $comment)) $class['required'] = 'required';
			if (in_array('color', $comment)) $class[] = 'color';
			if (in_array('rg', $comment)) {
				$class[] = 'rg';
				$maxlength = 12;
			}
			if (in_array('cep', $comment)) {
				$class[] = 'cep';
				$maxlength++; /* para caber o tracinho */
			}
			
			
			if (in_array('reference-1', $comment) && !$valor) {
				$valor = $args[0];
				
			}
			
			if (in_array('rich', $comment)) $class[] = 'rich';
			
			if (in_array('data', $comment) || in_array('date', $comment)) $class[] = 'data';
			if (in_array('hidden', $comment)) $tipo = 'hidden';
			if (in_array('cpf', $comment)) $class[] = 'cpf';
			if (in_array('cnpj', $comment)) $class[] = 'cnpj';
			if (in_array('perc', $comment)) $class[] = 'perc';
			if (in_array('count', $comment)) {
				$counter = true;
				$class[] = 'count_chars';
			}
			if (in_array('telefone', $comment) || in_array('fone', $comment)) $class[] = 'telefone';
			if (in_array('email', $comment)) $class[] = 'email';
			if (in_array('datatempo', $comment)) $class[] = 'datatempo';
			if (in_array('hora', $comment) || $tipo == 'time') $class[] = 'hora';
			if (in_array('number', $comment)) $class[] = 'number';
			if (in_array('enum2', $comment)) $enum2 = true;
		
			
			if ($tipo == 'int' || $tipo == 'tinyint') {
				//$class[] = 'number';
			}
			if (in_array('check', $comment)) {
				$tipo='checkbox';
			}
			if (in_array('radio', $comment)) {
				$tipo='radio';
			}
			
			if (in_array('bool', $comment)) {
				$arr = array('1'=>'Sim/Yes', 0=>'Não/No');
			}
			
			if (self::isMoney($comment)) {
				$valor = number_format((float)$valor, 2, ',', '');
			}
			if (in_array('password', $comment)) {
				$tipo='password';
				if ($valor) unset($class['required']);
				$valor = '';
			}
		}
		
		
		
		if ($foreign) {
			//$foreign2 = '';
			$arr = self::getForeignArr($di, $foreign_table, $foreign2);
			
			$controller = ucfirst($info['tabela']).'Controller';
			
			if (is_file('class/'.$controller.'.class.php')) {
				$reflector = new ReflectionClass($controller);
				if ($reflector->hasMethod('preArray')) {
					//if (isset($args[2])) {
						$controller::preArray($oldname, $arr);
					//}
				}
			
			}
			
			if (!$arr) {
				$arr = array('');
			}
			
		}
		if (in_array('x1', $comment)) {
			$div = 0.5;
		}
		if (in_array('x2', $comment)) {
			$div = 1;
		}
		if (in_array('x3', $comment)) {
			$div = 1.5;
		}
		if (in_array('x4', $comment)) {
			$div = 2;
		}
		
		if ($div && $tipo != 'hidden' && !$nodesign) {
			
			echo '<div class="col-md-'.($div*2).'">';
		}
		
		if (count($arr)) {
			
			$class[] = 'form-control';
			if (!$nodesign) {
				if ($tipo == 'hidden') {
					
				} else if ($div) {
					echo '<div class="form-group '.($tipo != 'textarea' ? '' : ' clear').'">';
				} else {
					echo '<div class="form-group '.($tipo != 'textarea' ? 'col-md-6' : ' col-md-12 clear').'">';
				}
			}
			
			
			if ($virtual) { //virtual foreign

				echo '<input type="hidden" name="'.$name.'" value="'.$valor.'">';
				if (isset($arr[$valor]) && $arr[$valor]) {
					if (!$nodesign) echo '<label for="'.$id.'">'.ucwords(($label ? $label : $name)).'</label>';
					if ($viewonly) { 
						echo '<span class="block">'.(isset($arr[$valor]) ? $arr[$valor] : '').'</span>';
					} else {
						echo '<input '.($readonly?'readonly':'').' type="text" '.($disabled ? 'disabled' : '').' name="" value="'.(isset($arr[$valor]) ? $arr[$valor] : '').'" id="'.$id.'" class="'.implode(' ', $class).'" />';
					}
				}
			} else {
				
				$foreign_form = (in_array('foreign-form', $comment));
				
				
				if (!$nodesign) {
					if ($tipo != 'hidden') echo  '<label for="'.$id.'">'.ucwords(($label ? $label : $name));
					
					if (!$viewonly) {
						echo ($foreign_form ? ' <a href="'.$foreign_table.'/add/?modal=1" data-toggle="modal" class="label label-warning label-xs foreign-form" data-target="#foreign_form" data-table="'.$foreign_table.'">+</a>' : '');
					}
					if ($tipo != 'hidden') echo '</label>';
				}
				if ($viewonly) {
					echo '<span class="block">'.(isset($arr[$valor]) ? $arr[$valor] : '').'</span>';
				} else {
					if ($tipo == 'hidden') {
						echo '<input type="hidden" id="'.$id.'" name="'.$name.'" data-value="'.$valor.'" data-name="'.$oldname.'" class="'.implode(' ', $class).'" value="'.$valor.'">';
					} else {
						echo '<select '.($disabled?'disabled':'').' '.($readonly?'readonly':'').' id="'.$id.'" name="'.$name.'" data-value="'.$valor.'" data-name="'.$oldname.'" class="'.implode(' ', $class).'" '.($onchange? 'data-change-table='.$onchange[0].' data-change-field='.$onchange[1].'' : '').' '.(in_array('skip-value', $comment) ? 'data-skip=1' : '').'>';
						if (in_array('nd', $comment) && (!isset($arr[0]['nome']) || $arr[0]['nome'])) { //no default value
							echo '<option></option>';
						}
						foreach($arr as $k=>$val) {
							if ($val != '') {
								$selected = ($valor == $k && strlen($k) == strlen($valor)) ? 'selected' : '';
							
								//$valorExibe = $val;
							
								echo '<option '.$selected.' value="'.$k.'">'.($val).'</option>';
							}
						}			
						echo '</select>';
					}
				}
			}
			if (!$nodesign && $tipo != 'hidden') echo '</div>';
			if ($div && $tipo != 'hidden') {
				echo '</div>';
			}
			return;
		}
		
		if ($tipo == 'varchar' || $tipo == 'char' || $tipo == 'date' || $tipo == 'datetime' || $tipo == 'tinyint' || $tipo == 'int' || $tipo == 'float' || $tipo == 'float unsigned' || $tipo == 'timestamp' || $tipo == 'time') {
			$tipo = 'text';
		}
		if (in_array('textarea', $comment)) $tipo = 'textarea';
		
		
		if ($tipo != 'hidden' && !$nodesign) {
			if ($div) {
				echo '<div class="form-group '.($tipo != 'textarea' ? '' : ' clear all').'">';
			} else {
				echo '<div class="form-group '.($tipo != 'textarea' ? 'col-md-6' : ' col-md-12 clear').'">';
			}
			echo  '<label for="'.$id.'">';
			
			echo ucwords(($label ? $label : $name));
			
			if (in_array('flash', $comment) && !$viewonly) {
				echo ' (<a href="default/tirafoto/" target="_blank">Câmera</a>)';
			}
			
			echo '</label>';
			$class[] = 'form-control';
		}
		
		
		if ($fakeHidden && !$viewonly) {
			echo '<input type="hidden" name="'.$name.'" value="'.$fakeHidden.'" />';
		}
		
		
		/// FUNÇÃO MUITO ESPECÍFICA: Remover
		if (in_array('link1', $comment) && !$viewonly) {
			echo '<div class="block">';
			echo '<a href="#" class="verificador label label-primary label-xs ">!</a>';
			echo '</div>';
		} 
		// FIM REMOÇÃO
		
		
		else if ($tipo == 'hidden') {
			echo '<input type="hidden" name="'.$name.'" value="'.$valor.'" />';
		} else if ($tipo == 'text' || $tipo == 'password') {
			if ($tipo == 'password') $valor = '';
			if ($viewonly) {
				echo '<span class="block">'.$valor.'</span>';
			} else {
				echo '<input autocomplete="new-password" '.($readonly?'readonly':'').' type="'.$tipo.'" '.($disabled ? 'disabled' : '').' name="'.$name.'" value="'.htmlspecialchars(stripslashes($valor)).'" maxlength="'.$maxlength.'" id="'.$id.'" class="'.implode(' ', $class).'" />';
				if ($counter) {
					echo '<span class="counter_'.$id.' counter">Caracteres: </span>';
				}
			}
		} else if ($tipo == 'file') {
			
			if ($valor) {
				if (in_array('required', $class)) {
					unset($class[array_search('required', $class)]);
				}
			}
			if (!$viewonly) {
				echo '<input '.($readonly?'readonly':'').' type="'.$tipo.'" '.($disabled ? 'disabled' : '').' name="'.$name.'" id="'.$id.'" class="'.implode(' ', $class).'" />';
			}
			
			if ($valor) {
				echo '<label class="noid">';
				if (in_array('img', $comment)) {
					$dimension = self::generateDimensionArray($comment);
					$string_thumb = '';
					$exibe = false;
					foreach($dimension as $d) {
						if ($d[0] && $d[1]) {
							$exibe = true;
							$string_thumb .= $d[0].'/'.$d[1].'/';
						}
					}
					if ($exibe) { 
						$palavra = self::getLangFile('miniatura_footer', $di);
						echo '<a title="'.$palavra.'" href="fotoadmin/miniatura/'.$valor.'/'.$string_thumb.'" target="_blank" class="btn btn-warning active btn-xs"><i class="glyphicon glyphicon-pencil"></i>'.$palavra.'</a>';
					}
				}
				
				if (in_array('removable', $comment)) {
					$palavra = self::getLangFile('remover-meta', $di);
					echo ' <input  class="noid" type="checkbox" name="remove['.$name.']" value=1>'.$palavra;
										
				}
				echo '</label>';
			}
			
		} else if ($tipo == 'checkbox') {
			
			$checked = ($valor == 1) ? 'checked' : '';
			if ($viewonly) {
				$palavra = self::getLangFile('sim-acompanhamento', $di);
				$palavra1 = self::getLangFile('nao-acompanhamento', $di);
				echo '<span class="block">'.($checked ? $palavra : $palavra1).'</span>';
			} else {
				echo '<input type="'.$tipo.'" name="'.$name.'" '.$checked.' value="1" id="'.$id.'" class="'.implode(' ', $class).'" />';
			}
		} else if ($tipo == 'textarea') {
			if ($viewonly) {
				echo '<span class="block">'.($valor).'</span>';
			} else {
				echo '<textarea name="'.$name.'" '.($disabled ? 'disabled' : '').' '.($readonly?'readonly':'').' id="'.$id.'" data-container="#cke_'.$id.'" data-selector="#cke_'.$id.'" class="'.implode(' ', $class).'" style="position:relative">';
				echo stripslashes($valor);
				echo '</textarea>';
				if ($counter) {
					$palavra = self::getLangFile('caracteres-meta', $di);
					echo '<span class="counter_'.$id.' counter">'.$palavra .' </span>';
				}
			}
		} else if ($tipo == 'radio') {
			foreach($enum as $val) {
				$valorTemp = trim($val, "'");
				$selected = ($valor == $valorTemp) ? 'checked' : '';
				$valorExibe = $valorTemp;
				$class[] = 'inverso';
				if ($viewonly) {
					echo '<span class="block">'.($valorTemp).'</span>';
				} else {
					echo '<input type="radio" name="'.$name.'" '.$selected.' value="'.$valorTemp.'" class="'.implode(' ', $class).'"> '.$valorTemp;
				}
			}
		} else if ($tipo == 'enum' || $tipo == 'enum2' || $tipo == 'set' || count($arr)) {
		
			echo '<select '.($readonly?'readonly':'').' id="'.$id.'" name="'.$name.'" class="'.implode(' ', $class).'">';
			if (in_array('nd', $comment) && (!isset($arr[0]['nome']) || $arr[0]['nome'])) { //no default value
				echo '<option></option>';
			}
			foreach($enum as $i=>$val) {
				$valorTemp = trim($val, "'");
				$valorExibe = $valorTemp;
				if (isset($enum2)) {
					$valorTemp = $i;
				}
				$selected = ($valor == $valorTemp && strlen($valorTemp) == strlen($valor)) ? 'selected' : '';
				
				if (in_array('genero', $comment)) {
					$palavra = self::getLangFile('masculino-meta', $di);
					$palavra1 = self::getLangFile('feminino-meta', $di);
					$palavra2 = self::getLangFile('especificado-meta', $di);
					if ($valorTemp == 'm') $valorExibe = $palavra;
					else if ($valorTemp == 'f') $valorExibe = $palavra1;
					else $valorExibe = $palavra2;
				}
				
				if ($viewonly) {
					echo '<span class="block">'.($valorExibe).'</span>';
				} else {
					echo '<option '.$selected.' value="'.$valorTemp.'">'.($valorExibe).'</option>';
				}
			}			
			echo '</select>';
		}
		
		
		if ($tipo != 'hidden' && !$nodesign) {
			
			echo '</div>';
			
		}
		
		if ($div && $tipo != 'hidden' && !$nodesign) {
			echo '</div>';
		}
	}
	public static function prepareTabs(&$meta, $di) {
		$tabs = array();
		$x = 0;
		foreach($meta['fieldsToShow'] as $i=>$coluna) {
			//$comment = ;
			//print_r($coluna['info']['comment']);
			$matches = preg_grep('/\%([^\[]+)\%/', $coluna['info']['comment']);
			
			if ($matches) {
				if(in_array("%Dados%", $matches)){
					$key = array_search("%Dados%", $matches);
					$palavra = self::getLangFile('dados-meta', $di);
					$matches[$key] = $palavra;
					
				}

				if(in_array("%Geral%", $matches)){
					$key = array_search("%Geral%", $matches);
					$palavra = self::getLangFile('geral-meta', $di);
					$matches[$key] = $palavra;
					
				}

				$tabs[] = trim(str_replace('_', ' ', reset($matches)), '%');
				$meta['fieldsToShow'][$i]['info']['tab'] = $x;
				$x++;
			}
			
			
		}
		
		return $tabs;
		
	}
	public static function prepare($di, $info, $table, $args) {
		$arr = array();
		$arr['controller'] = (isset($args[0]) ? $args[0] : '');
		$arr['action'] = (isset($args[1]) ? $args[1] : '');
		$arr['params'] = (isset($args[2]) ? $args[2] : '');
		if($table['nome'] == "Usuários"){
			$table['nome'] = self::getLangFile('usuarios_header', $di);
		}
		if($table['nome'] == "Configurações"){
			$table['nome'] = self::getLangFile('configuracoes_header', $di);
		}
		if($table['nome'] == "Textos"){
			$table['nome'] = self::getLangFile('textos_header', $di);
		}
		if($table['nome'] == "Escolas"){
			$table['nome'] = self::getLangFile('escolas_header', $di);
		}
		if($table['nome'] == "Agrupamentos"){
			$table['nome'] = self::getLangFile('agrupamentos-meta', $di);
		}
		if($table['nome'] == "Classificações"){
			$table['nome'] = self::getLangFile('classificacoes-meta', $di);
		}
		if($table['nome'] == "Matriculados"){
			$table['nome'] = self::getLangFile('matriculados-meta', $di);
		}
		if($table['nome'] == "Grupos de Envio"){
			$table['nome'] = self::getLangFile('grupos-meta', $di);
		}
		if($table['nome'] == "Dimensões"){
			$table['nome'] = self::getLangFile('dimensoes-meta', $di);
		}
		if($table['nome'] == "Semanas"){
			$table['nome'] = self::getLangFile('semanas-meta', $di);
		}
		if($table['nome'] == "Envio Todos"){
			$table['nome'] = self::getLangFile('envio-meta', $di);
		}
		if($table['nome'] == "Tipos de Usuários"){
			$table['nome'] = self::getLangFile('tipos-meta', $di);
		}
		if($table['nome'] == "Formularios"){
			$table['nome'] = self::getLangFile('formularios_header', $di);
		}
		if($table['nome'] == "Perguntas"){
			$table['nome'] = self::getLangFile('perguntas-meta', $di);
		}
		if($table['nome'] == "Arquivo"){
			$table['nome'] = self::getLangFile('arquivo_import', $di);
		}
		
			
		$arr['title'] = $table['nome'];
		
		$table['options'] = explode(' ', $table['options']);
		
		$arr['order'] = (in_array('order', $table['options']) || in_array('alls', $table['options']) ? 'ordem' : 'id');
		$arr['edit'] = (in_array('edit', $table['options']) || in_array('all', $table['options']) || in_array('alls', $table['options']) ? true : false);
		$arr['add'] = (in_array('add', $table['options']) || in_array('all', $table['options']) || in_array('alls', $table['options']) ? true : false);
		$arr['import'] = (in_array('import', $table['options']) ? true : false);
		$arr['del'] = (in_array('del', $table['options']) || in_array('all', $table['options']) || in_array('alls', $table['options']) ? true : false);
		
		$arr['permissions'] = array();
		
		$permissoes = LoginController::getPermissions($di);
		
		
		$arr['permissions']['add'] = LoginController::isAllowed($arr['controller'], 'add', $permissoes);
		$arr['permissions']['edit'] = LoginController::isAllowed($arr['controller'], 'edit', $permissoes);
		$arr['permissions']['list'] = LoginController::isAllowed($arr['controller'], 'list', $permissoes);
		$arr['permissions']['del'] = LoginController::isAllowed($arr['controller'], 'del', $permissoes);
		$arr['permissions']['report'] = LoginController::isAllowed($arr['controller'], 'report', $permissoes);
		$arr['permissions']['order'] = LoginController::isAllowed($arr['controller'], 'order', $permissoes);
		$arr['permissions']['view'] = LoginController::isAllowed($arr['controller'], 'view', $permissoes);
		$arr['permissions']['import'] = LoginController::isAllowed($arr['controller'], 'import', $permissoes);
		$arr['permissions']['filter'] = LoginController::isAllowed($arr['controller'], 'filter', $permissoes);
		//$arr['permissions']['filter-me'] = LoginController::isAllowed($arr['controller'], 'filter-me', $permissoes);
		
		
		$arr['columns'] = array();
		$arr['fields'] = array();
		$arr['midias'] = array();
		
		$arr['fieldsToShow'] = array();

		//echo '<pre>';
		//print_r($info);
		
		foreach($info as $item) {
			$comment = $item['comment'];
			$name = (isset($item['name'])) ? $item['name'] : '';
			$type = (isset($item['type'])) ? $item['type'] : '';
			//print_r($comment);
			if (in_array('foreign2', $comment)) {
				//die;
				$matches = preg_grep('/\!(.+)\!/', $comment);
				//print_r($matches);
				
						
				if ($matches) {
					$item['foreign2'] = explode('|', rtrim(ltrim(reset($matches), '!'), '!'));
					//print_r($info[$item['foreign2'][1]]);
					$idTemp = (isset($info[$item['foreign2'][1]]['valor']) ? $info[$item['foreign2'][1]]['valor'] : '');
					
					$item['foreign2'][] = $idTemp;
					if (in_array('-1', $comment)) {
						$item['foreign2'][]  = strtolower(substr($item['name'], 0, -1));
					} else {
						$item['foreign2'][] = $item['name'];
					}
					//echo $idTemp;
				}
			}
			
			
			if (in_array('id', $comment) || in_array('v', $comment) || in_array('visible', $comment) || in_array('hidden', $comment) || in_array('h', $comment) || in_array('required', $comment) || in_array('r', $comment)) {
				if (!in_array('midia', $comment)) {
					$arr['fields'][] = $name;
					$arr['fieldsToShow'][] = array('name'=> $name, 'info' => $item);
				}
			}
			
			
			if (in_array('v', $comment) || in_array('visible', $comment) || in_array('hidden', $comment) || in_array('h', $comment) || in_array('required', $comment) || in_array('r', $comment)) {
				if (in_array('midia', $comment)) {
					$arr['midias'][] = $name;
					$arr['fieldsToShow'][] = array('name'=> $name, 'info' => $item);
				}
			}
			
			if (in_array('order', $comment)) {
				
				$arr['order'] = $name;
			}
			$arr['orderby'] = 'ASC';
			if (in_array('rorder', $comment)) {
				$arr['orderby'] = 'DESC';
				$arr['order'] = $name;
			}
			
			
			
			
			
			if (self::isColumn($comment)) {

				$matches = preg_grep('/\[(.+)\]/', $comment);
				$title = ucwords($name);
				if ($matches) {
					$title = rtrim(ltrim(reset($matches), '['), ']');
					if (strpos($title, '*') === 0) {
						$temp = str_replace('*', '', $title).'Controller';
						//aqui
						$title = $temp::NAME;
					}
				}
				
				$matches = preg_grep('/(\d+)x(\d+)/', $comment);
				$dimension = array();
				if ($matches) {

					foreach($matches as $m) {
						list($width, $height) = explode('x', $m);
						$dimension[] = array($width, $height);
					}
					
				}
				
				$filtertype = (in_array('filter', $comment) && preg_match('/filter-(single|multiple)/', implode(' ', $comment), $matches) ? $matches[1] : 'single');
				
				if (!self::isForeign($comment) && $type != 'enum') {
					$filtertype = 'text';
				}
				
				if (self::isFilterDate($comment)) {
					$filtertype = 'date';
				}

				$enumList = '';
				
				if ($type == 'enum') {
					$arr1 = array();
					foreach($item['enum'] as $obj) {
						$obj = trim($obj, "'");
						$arr1[$obj] = $obj;
					}
					$enumList = $arr1;
				}
				if (self::isCheckBox($comment)) {
					$type = 'enum';
					$palavra = self::getLangFile('nao-acompanhamento', $di);
					$palavra1 = self::getLangFile('sim-acompanhamento', $di);
					$enumList = array(0=>$palavra, $palavra1);
					$filtertype = 'single';
				}
				
				if($title == "CPF"){
					$title = self::getLangFile('cpf_cadastro', $di);
				}
				if($title == "Nome"){
					$title = self::getLangFile('nome_cadastro', $di);
				}
				if($title == "Data_de_Nascimento"){
					$title = self::getLangFile('data_cadastro', $di);
				}
				if($title == "Tipo"){
					$title = self::getLangFile('tipo-meta', $di);
				}
				if($title == "Celular"){
					$title = self::getLangFile('celular_cadastro', $di);
				}	
				if($title == "Dono"){
					$title = self::getLangFile('dono-meta', $di);
				}
				if($title == "Programa"){
					$title = self::getLangFile('programa-meta', $di);
				}
				if($title == "Formulário"){
					$title = self::getLangFile('formulario_cadastrador_perguntas', $di);
				}
				if($title == "Pergunta"){
					$title = self::getLangFile('pergunta_relatorio', $di);
				}
				if($title == "Obrigatório"){
					$title = self::getLangFile('obrigatorio-meta', $di);
				}
				if($title == "Numérico"){
					$title = self::getLangFile('numerico-meta', $di);
				}
				if($title == "Telefone"){
					$title = self::getLangFile('telefone-meta', $di);
				}
				if($title == "E-mail"){
					$title = self::getLangFile('email_cadastro', $di);
				}
				if($title == "Escola"){
					$title = self::getLangFile('NAME_entidade', $di);
				}
				if($title == "Dimensão"){
					$title = self::getLangFile('dimensao-meta', $di);
				}
				if($title == "Grupo_de_Envio"){
					$title = self::getLangFile('grupo-meta', $di);
				}
				if($title == "Código"){
					$title = self::getLangFile('codigo_cadastro', $di);
				}
				if($title == "Distrito"){
					$title = self::getLangFile('distrito-meta', $di);
				}
				if($title == "Município"){
					$title = self::getLangFile('municipio-meta', $di);
				}
				if($title == "Diretoria"){
					$title = self::getLangFile('diretoria-meta', $di);
				}
				if($title == "Eventos"){
					$title = self::getLangFile('eventos_eventos', $di);
				}
				if($title == "Avaliacao"){
					$title = self::getLangFile('avaliacao_avalia', $di);
				}
				if($title == "Professor_Responsável"){
					$title = self::getLangFile('professor-meta', $di);
				}
				if($title == "Agrupamento"){
					$title = self::getLangFile('agrupamento-meta', $di);
				}
				if($title == "Ra"){
					$title = self::getLangFile('ra-meta', $di);
				}
				if($title == "Turno"){
					$title = self::getLangFile('turno-meta', $di);
				}
				if($title == "Responsavel"){
					$title = self::getLangFile('responsavel-meta', $di);
				}
				if($title == "Texto"){
					$title = self::getLangFile('texto-meta', $di);
				}
				if($title == "Descrição"){
					$title = self::getLangFile('descricao-meta', $di);
				}
				if($title == "Inicio"){
					$title = self::getLangFile('inicio-meta', $di);
				}
				if($title == "Termino"){
					$title = self::getLangFile('termino-meta', $di);
				}
				if($title == "Data_Envio_Diretor_Todos"){
					$title = self::getLangFile('data-meta', $di);
				}
				if($title == "Data_Envio_Professor_Todos"){
					$title = self::getLangFile('data1-meta', $di);
				}
				if($title == "Chave"){
					$title = self::getLangFile('chave-meta', $di);
				}
				if($title == "Categoria"){
					$title = self::getLangFile('categoria-meta', $di);
				}
				if($title == "Valor"){
					$title = self::getLangFile('valor-meta', $di);
				}
				if($title == "Arquivo"){
					$title = self::getLangFile('arquivo_import', $di);
				}
								

				$arr['columns'][$name] = array(
											'name'=>$name, 
											'title' => $title, 
											'midia' => (in_array('midia', $comment)), 
											'foreign' => (self::isForeign($comment)), 
											'dimensions' => $dimension, 
											'reference' => (in_array('reference', $comment) ? $name : ''), 
											'reference-1' => (in_array('reference-1', $comment) ? $name : ''), 
											'filter' => (in_array('filter', $comment) ? $name : ''), 
											'filter-type' => $filtertype,
											'enum' => ($type == 'enum' ? $enumList : ''),
											'date' => (in_array('date', $comment)), 
											'datetime' => (in_array('datetime', $comment)),
											'options' => $comment,
											'meta' => (in_array('inline',$comment) ? $item : '')
										);
			}

		}
		
		
		$arr['info'] = $table;
				return $arr;
		
	}
	
	public static function getFromMeta($di, $table) {
		$r = $di->getDb()->prepare("SELECT * from ".self::TABLE." where tabela=?");
	
		$r->Execute(array($table));
		if (!$r->rowCount()) {
			return false;
		} else {
			$dado = $r->fetch(PDO::FETCH_ASSOC);
			if (!$dado['options']) {
				$dado['options'] = 'all';
			}
			return $dado;
		}
	}

	public static function getTabelaGenerica($di, $tabela, $id = 0, $key='id') {
		$info = self::getMetaTabela($di, $tabela);
		if($tabela == 'grupo_envio_conf')
		{
			$tabela = 'grupo_envio';
		}
		if ($id) {
			
			$r = $di->getDb()->query("SELECT t.* from ".($tabela != 'admin' ? '' : '').$tabela." t where t.".$key."='".$id."'");
			
			if ($r->rowCount()) {
				self::atualizaMeta($info, $r->fetch(PDO::FETCH_ASSOC));
			}
		}
		return $info;
	}
	
	public static function getTabelaGenerica2($di, $tabela, $id = 0, $key='id') {
		$info = self::getMetaTabela($di, $tabela);
		if ($id) {
			$r = $di->getDb()->query("SELECT t.* from ".($tabela != 'admin' ? '' : '').$tabela." t where t.".$key."=".$id);
			if ($r->rowCount()) {
				self::atualizaMeta2($info, $r->fetchAll(PDO::FETCH_ASSOC));
			}
			
		}
		return $info;
	}
	public static function atualizaMeta2(&$pac, $dados) {
		foreach($dados as $k2=>$dado2) {
			foreach($dado2 as $k=>$dado) {
				if (isset($pac[$k]) && $pac[$k]['type'] == 'timestamp') {
					if ($dado == '0000-00-00 00:00' || $dado == '0000-00-00 00:00:00') {
						$sDate = '00/00/0000 00:00';
					} else {
						$oDate = new DateTime($dado);
						$sDate = $oDate->format("d/m/Y H:i");
					}
					$pac[$k]['valor'][$k2] = $sDate;
				} else {
					$pac[$k]['valor'][$k2] = $dado;
				}
			}
		}
	}
	
	public static function atualizaMeta(&$pac, $dados) {
		
		foreach($dados as $k=>$dado) {
			if (isset($pac[$k]) && $pac[$k]['type'] == 'timestamp') {
				if ($dado == '0000-00-00 00:00' || $dado == '0000-00-00 00:00:00') {
					$sDate = '00/00/0000 00:00';
				} else {
					$oDate = new DateTime($dado);
					$sDate = $oDate->format("d/m/Y H:i");
				}
				$pac[$k]['valor'] = $sDate;
			} else {
				$pac[$k]['valor'] = $dado;
			}
		}
	}
	
	public static function getMetaTabela($di, $tabela) {
		if($tabela == 'grupo_envio_conf')
		{
			$tabela='grupo_envio';
			$read = true;
		}
		else{
			$read = false;
		}
		$colunas = self::metaColumns($di, $tabela);
		
		$meta = array();
		foreach($colunas as $nome=>$coluna) {
			$meta[$nome] = array();
			$meta[$nome]['name'] = $nome;
			$meta[$nome]['maxlength'] = $coluna['maxlength'];
			$meta[$nome]['type'] = $coluna['type'];
			if($read){
				$meta[$nome]['comment'] = explode(' ', $coluna['comment']);
				array_push($meta[$nome]['comment'], "read-only");
			}
			else{
				$meta[$nome]['comment'] = explode(' ', $coluna['comment']);
			}
			$meta[$nome]['enum'] = $coluna['enums'];
			$meta[$nome]['tabela'] = $tabela;
			$meta[$nome]['default'] = $coluna['default'];
		}
		
		//$meta['arquivos'] = self::arquivosTabela($di, $tabela);

		return $meta;
	}
	
	public static function metaColumns($di, $tabela) {
		$q = $di->getDb()->prepare("SHOW FULL COLUMNS FROM ".($tabela != 'admin' ? ''.$tabela : $tabela));
		
		$q->execute();
		$table_fields = $q->fetchAll();
		$colunas = array();
		foreach($table_fields as $v) {
			
			$nome = $v['Field'];
			$comentario = $v['Comment'];
			$type = $v['Type'];
			$tipo = '';
			$enums = array();

			if (preg_match("/^(.+)\((\d+),(\d+)/", $type, $query_array)) {
				$tipo = $query_array[1];
				$max_length = is_numeric($query_array[2]) ? $query_array[2] : -1;
				
			} elseif (preg_match("/^(.+)\((\d+)/", $type, $query_array)) {
				$tipo = $query_array[1];
				$max_length = is_numeric($query_array[2]) ? $query_array[2] : -1;
			} elseif (preg_match("/^(enum)\((.*)\)$/i", $type, $query_array)) {
				$tipo = $query_array[1];
				$arr = explode(",",$query_array[2]);
				$enums = $arr;
				$zlen = max(array_map("strlen",$arr)) - 2; // PHP >= 4.0.6
				$max_length = ($zlen > 0) ? $zlen : 1;
			} else {
				$tipo = $type;
				$max_length = -1;
			}
			//print_r($v);
			$colunas[$nome]['name'] = $nome;
			$colunas[$nome]['comment'] = $comentario;
			$colunas[$nome]['type'] = $tipo;
			$colunas[$nome]['enums'] = $enums;
			$colunas[$nome]['maxlength'] = $max_length;
			$colunas[$nome]['default'] = $v['Default'];
			//$colunas[$nome]['default'] = $v[5];
		}
		return $colunas;
	}
	
}