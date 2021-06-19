<?php

/**
 * Classe de implementação do Design Pattern "Dependancy Injection"
 *
 * O objeto irá guardar instâncias de classes de uso global, como Banco de Dados e Sessão
 *
 * @author Otávio Tralli <otavio@tralli.org>
 * @version v 1.0
*/
class DependencyContainerAdmin extends DependencyContainer {

	public function __construct($params) {
		$this->_instances['session'] = new Session(SESSION_NAME);
		//$this->_instances['datacache'] = new DataCache();
		$this->_instances['db'] = new DBAdmin($params['db_dsn'], $params['db_user'], $params['db_pwd']);
		//$this->_instances['log'] = new Log($this);
		
		$this->_instances['view'] = new SavantCustom();
		$this->_instances['view']->addPath('template', './view/templates');
		$this->_instances['view']->session = $this->_instances['session'];
		
		
		$arr = array();
		$query = $this->_instances['db']->query('SET NAMES utf8');
		$query = $this->_instances['db']->prepare('SELECT * FROM '.'config');
		$query->Execute(array());
		while($dados = $query->fetch(PDO::FETCH_ASSOC)) {
			$arr[$dados['chave']] = $dados['valor'];
		}
		$this->_instances['config'] = $arr;
		
	}

	public function getConfig() {
		return $this->_instances['config'];
	}
	
	public function getLog() {
		return $this->_instances['log'];
	}
}
