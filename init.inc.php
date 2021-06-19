<?php
/**
 *	Arquivo de inicialização de classes, variáveis, templates, etc. (BO)
 *
 *	@author Otávio Tralli <otavio@tralli.org>
 *	@version v 1.0
*/

include_once 'includes.inc.php';
include_once 'class/ComumAdmin.trait.php';

function my_autoloader($class) {
	if (is_file('class/' . ($class) . '.class.php')) {
		include_once 'class/' . ($class) . '.class.php';
	} else {
		//include_once '../class/' . ($class) . '.class.php';	
	}
}

spl_autoload_register('my_autoloader');

define('SESSION_NAME', str_replace(' ', '', PROJECT_NAME));
define('MAX_PAGES', 25);
define('RESULTADOSPAGINA', 50);

header('Content-Type: text/html; charset=utf-8');
setlocale(LC_ALL, 'en_US.UTF8');
ini_set('user_agent','Mozilla: (compatible; Windows XP)');

/* Inicialização do container */
$di = new DependencyContainerAdmin($config);
$db = $di->getDb();

/* inicialização da biblioteca Savant3 */

/* ----- */

include_once "view/css/cache/cache_name.php";
include_once "view/js/cache/cache_name.php";

$di->getView()->css_file = $css_file;
$di->getView()->js_file = $js_file;
$di->getView()->di = $di;

$menu = MenuAdmin::getMenu($di);
$di->getView()->menu = $menu;
$di->getView()->user_table = LoginController::getTable($di);
$di->getView()->user_id = LoginController::getUserId($di);
$di->getView()->user_name = LoginController::getUserName($di);



function t($nome, $texto = '', $rich = false, $update = false, $onlytext = false) {
	global $di;
	
	$db = $di->getDb();
//	echo 'SELECT * FROM '.PROJECT_PREFIX.'staticstring WHERE id=?';
	$query = $db->prepare('SELECT * FROM '.PROJECT_PREFIX.'staticstring WHERE id=?');
	$query->Execute(array($nome));
	
	if ($query->rowCount()) {
		$dados = $query->fetch(PDO::FETCH_ASSOC);
		$texto = $dados['valor'];
		$rich = $dados['rich'];
	} else {
		$query2 = $db->prepare('INSERT INTO '.PROJECT_PREFIX.'staticstring (id, valor, rich) values (?, ?, ?)');
		$query2->Execute(array($nome, $texto, $rich));
	}
	
	
	if ($onlytext) {
		return $texto;
	}
	//return '<div contenteditable="true" data-id="'.$nome.'" class="edit '.($rich ? 'rich' : '').'">'.$texto.'</div>';
	return '<div data-id="'.$nome.'" class="edit '.($rich ? 'rich' : '').'">'.$texto.'</div>';
	
}