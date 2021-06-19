<?php

/**
 * Classe de implementação do Design Pattern "Dependency Injection"
 *
 * O objeto irá guardar instâncias de classes de uso global, como Banco de Dados e Sessão
 *
 * @author Otávio Tralli <otavio@tralli.org>
 * @version v 1.0
*/
class DependencyContainer {
	/**#@+
	* @access protected
	*/
	protected $_instances = array();
	protected $_params = array();
	
	public function __construct($params) {
		$this->_params = $params;
		$this->_instances['session'] = new Session(SESSION_NAME);
		//$this->_instances['message'] = new Message();
		//$this->_instances['datacache'] = new DataCache();
		/*if (defined('SANDBOX_LOCAL') || defined('DEBUG_SQL')) $this->_instances['db'] = new DB($this->_params['db_dsn'], $this->_params['db_user'], $this->_params['db_pwd'], $this->_instances['session']);
		else $this->_instances['db'] = new PDO($this->_params['db_dsn'], $this->_params['db_user'], $this->_params['db_pwd']);
		*/
		$this->_instances['db'] = new DB($this->_params['db_dsn'], $this->_params['db_user'], $this->_params['db_pwd']);
		$this->_instances['db']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$arr = array();
		$query = $this->_instances['db']->query('SET NAMES utf8');
		$query = $this->_instances['db']->prepare('SELECT * FROM config');
		$query->Execute(array());
		while($dados = $query->fetch(PDO::FETCH_ASSOC)) {
			$arr[$dados['chave']] = $dados['valor'];
		}
		$this->_instances['config'] = $arr;
		
		$this->_instances['view'] = new SavantCustom();
		$this->_instances['view']->addPath('template', './view/templates');
		$this->_instances['view']->session = $this->_instances['session'];
		
		$this->_instances['view']->config = $arr;
		
		$usuario = new UsuarioController();
		
		$userId = $this->_instances['session']->getSessionValue('site_id');
		$this->_instances['view']->logado = ($userId ? $userId : 0);
		$this->_instances['view']->fotografo = ($this->_instances['session']->getSessionValue('site_tipo') == 2 ?true:false);
		$this->_instances['view']->userid = $userId;
		
		$this->_instances['view']->username = ($userId ? $this->_instances['session']->getSessionValue('site_nome') : '');
		$this->_instances['view']->carrinho = ($this->_instances['session']->getSessionValue2('site_carrinho', 'itens') ? count($this->_instances['session']->getSessionValue2('site_carrinho', 'itens')) : 0);
		
		
		
	}

	/**
	 *	Retorna a instância da classe de banco de dados
	 *
	 *	@access public
	 *	@return PDO
	*/
	public function getDb() {
		return $this->_instances['db'];
	}
	
	public function getView() {
		return $this->_instances['view'];
		
	}
	
	public function getConfig() {
		return $this->_instances['config'];
		
	}
	
	/**
	 *	Retorna a instância da classe de cache de dados
	 *
	 *	@access public
	 *	@return DataCache
	*/
	public function getDataCache() {
		return $this->_instances['datacache'];
	}
	
	/**
	 *	Retorna a instância da classe de Message
	 *
	 *	@access public
	 *	@return Message
	*/
	public function getMessage() {
		return $this->_instances['message'];
	}
	
	/**
	 *	Retorna a instância da classe de Session
	 *
	 *	@access public
	 *	@return Session
	*/
	public function getSession() {
		return $this->_instances['session'];
	}

}