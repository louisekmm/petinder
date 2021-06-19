<?php
/**
 *	Gera o arquivo de cache de CSS
 *
 *	@author Otávio Tralli <otavio@tralli.org>
 *	@version v 1.0
*/

//require_once "../../init.inc.php";
//require_once "../../../functions.php";
header('Content-type: text/css');


$cache_dir = realpath(dirname(__FILE__)) . '/../cache/';
$custom_dir = realpath(dirname(__FILE__)).'/';
$name=md5_of_dir($custom_dir).'.css';


if(file_exists($cache_dir.$name)) {
	readfile($cache_dir.$name);
} else {
	$js = '';
	foreach (glob($custom_dir."*.css") as $filename) {
	$js.=file_get_contents($filename);
	}

	require realpath(dirname(__FILE__)).'/../../../libs/YUICompressor.php';
	
	Minify_YUICompressor::$jarFile = realpath(dirname(__FILE__)) . '/../../../libs/yuicompressor-2.4.8.jar';
	Minify_YUICompressor::$tempDir = realpath(dirname(__FILE__)) . '/../../../libs/tmp/';
	
	$js = Minify_YUICompressor::minifyCss($js, array('nomunge' => true, 'break-line'=>10));
	delete_old_md5s($cache_dir, $name);
	file_put_contents($cache_dir.$name, $js);
	file_put_contents($cache_dir.'cache_name.php', '<?php $css_file="'.$name.'" ?>');
	

	echo $js;
}

function delete_old_md5s($folder, $except = '') {
	//$olddate=time()-3600;
	$dircontent = scandir($folder);
	foreach($dircontent as $filename) {
		//if (strlen($filename)==32 && filemtime($folder.$filename) && filemtime($folder.$filename)<$olddate) unlink($folder.$filename);
		if ($filename != $except && $filename != '.' && $filename != '..') unlink($folder.$filename);
	}
}

function md5_of_dir($folder) {
	$dircontent = scandir($folder);
	$ret='';
	foreach($dircontent as $filename) {
		if ($filename != '.' && $filename != '..') {
			if (filemtime($folder.$filename) === false) return false;
			$ret.=date("YmdHis", filemtime($folder.$filename)).$filename;
		}
	}
	return md5($ret);
}