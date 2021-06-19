<?php
/**
	*	Arquivo de configuração
 *
 *	@author Otávio Tralli <otavio@tralli.org>
 *	@version v 1.0
*/

$env = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');

$lang = (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'pt');

define('PROJECT_NAME', 'petinder');

set_time_limit(0);
switch($env) {
	/* servidor local de teste */
	case 'localhost':
		$host = 'localhost';
		$dbname = 'petinder';
		$config = array(
			'db_dsn' => 'mysql:host='.$host.';dbname='.$dbname.';charset=utf8',
			'db_user' => 'root',
			'db_pwd' => ''
		);
		

		define('SANDBOX', true);
		define('SANDBOX_LOCAL', true);
		error_reporting(-1);
		ini_set("display_errors",1);
		$base_path = 'petinder';
		$url = 'http://localhost/'.$base_path;
	break;
	
	/* produção */
	default:
		$host = 'intercommgov-db.mysql.database.azure.com';
		$dbname = 'petinder';
		$config = array(
				'db_dsn' => 'mysql:host='.$host.';dbname='.$dbname.';charset=utf8',
				'db_user' => 'intercom@intercommgov-db',
				'db_pwd' => 'mgovadm1!'
			);
	

		if (isset($_GET['debugX'])) {
			error_reporting(-1);
			ini_set("display_errors",1);
			define('DEBUG_SQL', true);
		} else {
			ini_set("display_errors",0);
			error_reporting(0);
		}
		define('SANDBOX', false);
		$base_path = '';
		$url = 'http://intercom.mgovbrasil.com.br'.$base_path;
	break;
}
//$pagseguro_token = 'CD23A11FBC2849859B7AB2D7B53F8F9C';
date_default_timezone_set('America/Sao_Paulo');

define('URL', (string) $url);







include_once 'functions.inc.php';