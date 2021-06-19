<?php

/**
 * Classe com métodos para manipulação de sessões
 *
 * @author Otávio Tralli <otavio@tralli.org>
 * @version v 1.0
*/
class Session {

	public function __construct($name = 'Sessao') {
		session_name(SESSION_NAME);
		session_start();
	}
	
	/**
	 *	Retorna o valor de uma dada variável de sessão
	 *
	 *	@access public
	 *	@param string $nome nome da variável
	 *	@param bool $unset destrói a variável depois (opcional)
	 *	@return mixed
	*/
	public function getSessionValue($nome, $unset = false) {
		$value = (isset($_SESSION[$nome])) ? $_SESSION[$nome] : '';
		if ($unset) $this->unsetSessionValue($nome);
		return $value;
	}
	
	public function getSessionValue2($nome, $nome2 = '') {
		$value = (isset($_SESSION[$nome][$nome2])) ? $_SESSION[$nome][$nome2] : '';

		return $value;
	}
	
	/**
	 *	Atribui um valor para uma dada variável de sessão
	 *
	 *	@access public
	 *	@param string $nome nome da variável
	 *	@param string $valor valor da variável
	 *	@return mixed
	*/
	public function setSessionValue($nome, $valor = '') {
		$_SESSION[$nome] = $valor;
	}
	public function isAdmin() {
		return (isset($_SESSION['admin']) && $_SESSION['admin'] ? true : false);
		
	}
	public function setSessionValue2($nome, $valor = '', $nome2 = '') {
		$_SESSION[$nome][$nome2] = $valor;
	}

	/**
	 *	Destrói uma dada variável de sessão
	 *
	 *	@access public
	 *	@param string $nome nome da variável
	 *	@return
	*/
	public function unsetSessionValue($nome) {
		unset($_SESSION[$nome]);
	}
	
	/**
	 *	Destrói uma sessão
	 *
	 *	@access public
	 *	@return
	*/
	public function destroySession() {
		$_SESSION = array();

		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		session_destroy();
	}
	
	/**
	 *	Atualiza o cookie da sessão para que o usuário não deslogue ao fechar o browser
	 *
	 *	@access public
	 *	@return
	*/
	public function keepConnected() {
		setcookie(session_name(),session_id(),time()+99999999, '/');
	}
	
	public function setMessage($text, $tipo) {
		$this->setSessionValue('msgtipo_admin', $tipo);
		$this->setSessionValue('msg_admin', $text);
	}
	public function isMessage() {
		return ($this->getSessionValue('msg_admin') ? true : false);
	}
	public function getMessage() {
		$vals = array($this->getSessionValue('msgtipo_admin'), $this->getSessionValue('msg_admin'));
		$this->unsetSessionValue('msgtipo_admin');
		$this->unsetSessionValue('msg_admin');
		return $vals;
	}
}