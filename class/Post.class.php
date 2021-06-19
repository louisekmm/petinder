<?php

/**
 *	Classe para manipulação de dados enviados pelo formulário
 *
 *	@author Otávio Tralli <otavio@tralli.org>
 *	@version v 1.0
*/
class Post {
	/**
	 *	Verifica se há algo postado
	 *
	 *	@access public
	 *	@return bool
	*/
	public function postado() {
		return (count($_POST) > 0);
	}
	
	/**
	 *	Retorna o valor de uma variável de postagem
	 *
	 *	@access public
	 *	@return mixed
	*/
	public function getDado($nome, $strip = false) {
		if (!$strip) return (isset($_POST[$nome])) ? $_POST[$nome] : '';
		else return (isset($_POST[$nome])) ? str_replace("'", '', $_POST[$nome]) : '';
	}
	
	/**
	 *	Retorna uma lista de dados
	 *
	 *	@access public
	 *	@param array(string) $nomes vetor com os nomes dos campos
	 *	@param string $func função de mapeamento utilizado em cada valor (opcional)
	 *	@return mixed
	*/
	public function getDados($nomes, $func = '') {
		$arr = array();
		foreach($nomes as $nome) {
			$arr[$nome] = $this->getDado($nome);
		}
		
		if ($func) {
			$arr = array_map($func, $arr);
		}
		
		return $arr;
	}
	
	/**
	 *	Transforma caracteres específicos em entidades HTML
	 *
	 *	@access public
	 *	@param array(string) $dados vetor com o nome dos campos
	 *	@return mixed
	*/
	public function doStrip($dados) {
		foreach($dados as $k=>$dado) {
			$dados[$k] = htmlspecialchars($dado, ENT_QUOTES);
		}
		
		return $dados;
	}

	public static function doGet($nomes) {
		$arr = array();
		foreach($nomes as $nome) {
			if (isset($_GET[$nome]) && is_array($_GET[$nome])) {
				$arr[$nome] = $_GET[$nome];
				echo("a-".$arr[$nome]);
			} else {
				$arr[$nome] = (isset($_GET[$nome])) ? ($_GET[$nome]) : '';
				if ($nome == 'senha' && $arr[$nome]) {
					$arr[$nome] = hash('sha512', $arr[$nome]);
				}
				
				if ($nome == 'data' || $nome == 'nascimento') {
					$arr[$nome] = implode('-', array_reverse(explode('/', $arr[$nome])));
				}
			}
		}
		return $arr;
	}

	public static function doPost($nomes) {
		$arr = array();
		foreach($nomes as $nome) {
			if (isset($_POST[$nome]) && is_array($_POST[$nome])) {
				$arr[$nome] = $_POST[$nome];
				echo("a-".$arr[$nome]);
			} else {
				$arr[$nome] = (isset($_POST[$nome])) ? ($_POST[$nome]) : '';
				if ($nome == 'senha' && $arr[$nome]) {
					$arr[$nome] = hash('sha512', $arr[$nome]);
				}
				
				if ($nome == 'data' || $nome == 'nascimento') {
					$arr[$nome] = implode('-', array_reverse(explode('/', $arr[$nome])));
				}
			}
		}
		//return $arr;
	}
	
	public static function doPostInverse($nomes) {
		$arr = array();
		foreach($nomes as $k=>$nome) {
			if (is_numeric($k)) {
				$k = $nome;
			}
			if (isset($_POST[$nome]) && is_array($_POST[$nome])) {
				$arr[$k] = $_POST[$nome];
			} else {
				$arr[$k] = (isset($_POST[$nome])) ? ($_POST[$nome]) : '';
				if ($nome == 'senha' && $arr[$nome]) {
					$arr[$k] = hash('sha512', $arr[$nome]);
				}
			}
		}
		return $arr;
	}
}