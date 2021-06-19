<?php

class LoginController {
	
	const TABLE = 'admin';
	
	public static function isLogged($di) {
		//print_r(self::getPermissions($di));
		return ($di->getSession()->getSessionValue('admin') ? true : false);
	}

	public static function getLng($di){
		$idioma = self::getIdioma($di);
		return (parse_ini_file($idioma.'.lang'));
		
	}
	
	public static function isSuper($di) {
		return ($di->getSession()->getSessionValue('admin_super') ? true : false);
	}
	
	public static function getTipo($di) {
		return $di->getSession()->getSessionValue('admin_tipo');
	}
	
	public static function getForeignColumn($di) {
		return $di->getSession()->getSessionValue('admin_foreign');
	}
	
	public static function getIdioma($di) {
		return $di->getSession()->getSessionValue('idioma');
	}

	public static function getTable($di) {
		return $di->getSession()->getSessionValue('admin_table');
	}
	
	public static function getPermissions($di) {
		return $di->getSession()->getSessionValue('admin_permissions');
	}
	
	public static function hasPermission($di, $table, $sub = '') {
		
	}
	
	public static function checkLogged() {
		global $di;
		if (!self::isLogged($di)) {
			header('Location: '.URL.'/login/');
			die;
		}
	}

	public static function checkSelf($di, $id) {
		return ($id == self::getUserId($di));
	}
	
	public static function isAllowed($table, $action = '', $perm = '') {
		global $di;
		
		if (!$table) return 1;
		if (!$perm) {
			$perm = self::getPermissions($di);
		}
		
		if (!isset($perm[$table])) {
			return (self::isSuper($di) ? 1 : 0);
		}
 
		$values = isset($perm[$table]) ? $perm[$table] : array();
		if (!$action && isset($values['nomenu'])) {
			return 0;
		}
		
		if ($action && isset($values['block'.$action])) {
			return 0;
		}

		if ($action && isset($values['selfonly'.$action])) {
			return 2;
		}
		
		if ($action && isset($values['block'.$action])) {	
			return 0;
		}

		if ($action && isset($values[$action])) {
			return 1;
		}

		if (isset($values['selfonly'])) {
			return 2;
		}
		
		if (isset($values['block'])) {
			return 0;
		}

		if (!$action && isset($values['menu'])) {
			return 1;
		}
		
		
					
		return (self::isSuper($di) ? 1 : 0);
		
	}
	
	public static function notAllowed($di) {
		$palavra = Meta::getLangFile('acesso-default', $di);
		$di->getSession()->setMessage($palavra, false);
		
		header("Location: ".URL);
		exit();
	}
	
	public static function getTableAcl($di, $tipo) {
		$selPermissions = $di->getDb()->prepare('SELECT `table` FROM acl m WHERE id=?');
		$selPermissions->Execute(array($tipo));
		
		return $selPermissions->fetch(PDO::FETCH_COLUMN);
	}
	
	public static function getUserId($di) {
		return $di->getSession()->getSessionValue('admin_userId');
	}
	
	public static function getUserName($di) {
		return $di->getSession()->getSessionValue('admin_nome');
	}
	
	public static function login($di, $userId, $login, $tipo, $super, $table, $nome, $foreign) {
		$di->getSession()->setSessionValue('admin', true);
		$di->getSession()->setSessionValue('admin_userId', $userId);
		$di->getSession()->setSessionValue('admin_login', $login);
		$di->getSession()->setSessionValue('admin_nome', $nome);
		$di->getSession()->setSessionValue('admin_super', $super);
		$di->getSession()->setSessionValue('admin_table', $table);
		$di->getSession()->setSessionValue('admin_foreign', $foreign);
		
		$selAcl = $di->getDb()->query('SELECT idAcl FROM admin_tipo WHERE id='.$tipo.';')->fetch(PDO::FETCH_ASSOC);
		$tipoAcl = $selAcl['idAcl'];
		
		$di->getSession()->setSessionValue('admin_tipo', $tipoAcl);


		$selPermissions = $di->getDb()->prepare('SELECT ma.options, m.tabela from meta_acl ma INNER JOIN meta m ON m.id=ma.idMeta WHERE idAcl=?');
		$selPermissions->Execute(array($tipoAcl));
		


		$temp = $selPermissions->fetchAll();
		$result = array();
		foreach($temp as $perm) {
			$result[$perm['tabela']] = array_flip(explode(' ', $perm['options']));
		}
		
		$di->getSession()->setSessionValue('admin_permissions', $result);
		
	}
	
	public static function logout($di) {
		$di->getSession()->unsetSessionValue('admin');
		$di->getSession()->unsetSessionValue('admin_userId');
		$di->getSession()->unsetSessionValue('admin_login');
		$di->getSession()->unsetSessionValue('admin_tipo');
		$di->getSession()->unsetSessionValue('admin_nome');
		$di->getSession()->unsetSessionValue('admin_super');
		$di->getSession()->unsetSessionValue('admin_permissions');
		$di->getSession()->unsetSessionValue('admin_table');
		$di->getSession()->unsetSessionValue('admin_foreign');
	}
	
	
	
	function _logout() {
		global $di;
		
		self::logout($di);
		$di->getView()->lng = self::getLng($di);
		 $di->getView()->exibir = "nao";
		$di->getView()->load('login');
	}
	
	function getTipos($di) {
		$re = array();
		$temp = $di->getDb()->query('select * from acl ORDER BY ordem DESC')->fetchAll();
		foreach($temp as $t) {
			$re[$t['id']] = $t;
		}
		return $re;
	}
	
	
	function _default() {
		global $di;
		
		$di->getView()->tipos = self::getTipos($di);
		$di->getView()->noheader=true;
		$di->getView()->lng = self::getLng($di);
		$di->getView()->exibir = "sim";
		$di->getView()->load('login');
	}
	
	function _esqueci() {
		global $di;
		
		$di->getView()->tipos = self::getTipos($di);
		$di->getView()->noheader=true;
		$di->getView()->lng = self::getLng($di);
		$di->getView()->load('esqueci');
	}
	
	
	public function _dorecuperar() {
		global $di;
		
		$arr = array('senha', 'token');
		$vals = Post::doPost($arr);
		if ($vals['senha'] && $vals['token']) {
			$update = $di->getDb()->prepare('UPDATE '.self::TABLE.' set senha=:senha,token="" WHERE token=:token');
			$update->Execute($vals);
			
			if ($update->rowCount()) {
				echo '1';
			} else {
				$palavra = Meta::getLangFile('erro-login', $di);
				echo ($palavra);
				
			}
		}
	}
	
	function _recuperar() {
		global $di;
		
		$di->getView()->noheader=true;
		$args = func_get_args();
		if (isset($args[1])) {
			$db = $di->getDb();
			
			$existe = $db->prepare('SELECT id FROM '.self::TABLE.' WHERE token=?');
			$existe->Execute(array($args[1]));
			
			$id = $existe->fetch(PDO::FETCH_COLUMN);
			
			if ($id) {
				$di->getView()->token = $args[1];
				$di->getView()->lng = self::getLng($di);
				$di->getView()->load('recuperar');
			} else {
				$this->_logout();
			}
		} else {
			$this->_logout();
		}
	}
	
	
	function _do() {
		global $di;
		$db = $di->getDb();
		$dados = Post::doPost(array('login', 'senha', 'tipo'));
		
		$tipos = self::getTipos($di);
		
		$tabela = self::TABLE;
		$campo = 'login';
		$tipoColumn = '';
		$tipoValor = '';
		$foreign = '';
		//$_COOKIE['tipo_login'] = $dados['tipo'];
		//print_r($_COOKIE);
		//setcookie('tipo_login', $dados['tipo'], time()+99999, '/');
		
		if (count($tipos)) {
			if (isset($dados['login'])) {
				$where = array('1=1');
				$valores = array();
				
				//$tabela = $tipos[$dados['tipo']]['table'];
				$tabela = 'admin';
				//$campo = $tipos[$dados['tipo']]['loginColumn'];
				$campo = 'email';
				$foreign = 'idAdmin';
				//$foreign = $tipos[$dados['tipo']]['foreignColumn'];
				//$tipoColumn = $tipos[$dados['tipo']]['tipoColumn'];
				//$tipoValor = $tipos[$dados['tipo']]['tipoValor'];
				
				$where[] = $campo.'=?';
				$valores[] = $dados['login'];
				
				$where[] = 'senha=?';
				$valores[] = $dados['senha'];
				
				if ($tipoColumn) {
					$where[] = $tipoColumn.'=?';
					$valores[] = $tipoValor;
				}
			} else {
				$palavra = Meta::getLangFile('dados-login', $di);
				$di->getSession()->setMessage($palavra, false);
				
				$di->getView()->session = $di->getSession();
				$di->getView()->lng = self::getLng($di);
				$di->getView()->load('warnings', false, false);
				exit(0);
			}
		}
		
		$r = $db->prepare('SELECT id, nome, idAdmin_Tipo FROM '.$tabela.' WHERE '.implode(' AND ', $where));
		
		
		$r->Execute($valores);
		
		if ($r->rowCount()) {
			$palavra = Meta::getLangFile('login-login', $di);
			$di->getSession()->setMessage($palavra, true);
						
			$valores = $r->fetch();
			//print_r($valores);
			self::login($di, $valores['id'], $dados['login'], $valores['idAdmin_Tipo'], 1, $tabela, $valores['nome'], $foreign);

			echo '1';
		} else {
			$palavra = Meta::getLangFile('dados-login', $di);
			$di->getSession()->setMessage($palavra, false);
			
			$di->getView()->session = $di->getSession();
			$di->getView()->lng = self::getLng($di);
			$di->getView()->load('warnings', false, false);
		}
	}
	
	function _doesqueci() {
		global $di;
		
		$db = $di->getDb();
		$vals = Post::doPost(array('login'));
		
		$email = $di->getDb()->prepare('SELECT id, email, nome FROM '.self::TABLE.' WHERE email=?');
			$email->Execute(array($vals['login']));
			
			$dados = $email->fetch(PDO::FETCH_ASSOC);
			
			if (!$dados) {
				$palavra = Meta::getLangFile('email-login', $di);
				echo ($palavra);
				
			} else {

				$token = md5(uniqid($vals['login'], true));
				$set = $di->getDb()->prepare('UPDATE '.self::TABLE.' SET token=? WHERE id=?');
				$set->Execute(array($token, $dados['id']));
				
				$url = URL.'/login/recuperar/'.$token.'/';
				$palavra = Meta::getLangFile('ola-login', $di);
				$palavra1 = Meta::getLangFile('para-login', $di);
				$palavra2 = Meta::getLangFile('obrigado-login', $di);
				$palavra3 = Meta::getLangFile('recuperacao-login', $di);
				$palavra4 = Meta::getLangFile('verifique-login', $di);
				$palavra5 = Meta::getLangFile('erro1-login', $di);

				$body = '<p>'.$palavra.'</p>';
				$body .= '<p>'.$palavra1.' <a href="'.$url.'">'.$url.'</a></p>';
				$body .= '<p>'.$palavra2.'</p>';

				if (Email::send($di, $palavra3, array(array('email'=>$dados['email'], 'nome' => $dados['nome'])), $body)) {
					echo ($palavra4);
				} else {
					echo ($palavra5);
				}
			}
				
		
	}
	
	public static function getFather($di) {
		$id = self::getUserId($di);
		
		$sel = $di->getDb()->prepare("SELECT idAdmin FROM admin WHERE id=?");
		$sel->Execute(array($id));
		
		return $sel->fetch(PDO::FETCH_COLUMN);
	}
	
	public static function getGrandfather($di) {
		$id = self::getUserId($di);
		
		$sel = $di->getDb()->prepare("SELECT a2.idAdmin FROM admin a INNER JOIN admin a2 ON a2.id=a.idAdmin WHERE a.id=?");
		$sel->Execute(array($id));
		
		return $sel->fetch(PDO::FETCH_COLUMN);
	}
}