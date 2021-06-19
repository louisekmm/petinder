<?php

function money($val) {
	return str_replace('.', ',', number_format($val, 2, ',', ''));
}

function urlAmigavel($str, $replace=array(), $delimiter='-') {
    if( !empty($replace) ) {
        $str = str_replace((array)$replace, ' ', $str);
    }
	
	$str = mb_convert_encoding($str, "UTF-8", "UTF-8");
	$a = 'ÁáÉéÍíÓóÚúÇçÃãÕõÊêÔôàüÜ';
    $b = 'aaeeiioouuccaaooeeooauu';
	$clean = strtr(utf8_decode($str), utf8_decode($a), utf8_decode($b));
	$clean = str_replace("'", $delimiter, $clean);
	
    //$clean = @iconv('UTF-8', 'ASCII//IGNORE', $clean);
	
    $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
    $clean = strtolower(trim($clean, '-'));
    $clean = preg_replace("/[_|+ -]+/", $delimiter, $clean);
	$clean= str_replace('/', '-', $clean);
	$clean = str_replace('---', '-', $clean);
 
    return $clean;
}