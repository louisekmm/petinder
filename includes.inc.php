<?php

/**
 *	Arquivo de inclusão de arquivos e bibliotecas
 *
 *	@author Otávio Tralli <otavio@tralli.org>
 *	@version v 1.0
*/

include_once 'config.inc.php';


/* inclusão de bibliotecas e classes externas */

include_once 'libs/Savant3.php'; // classe para templates
include_once 'libs/SavantCustom.php';


if (defined('SANDBOX_LOCAL') || defined('DEBUG_SQL')) include_once 'class/DB.class.php';
