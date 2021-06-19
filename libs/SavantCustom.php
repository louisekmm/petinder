<?php

/**
 * Classe modificada para funções específicas para browser
 *
 * @author Otávio Tralli <otavio@tralli.org>
 * @version v 1.0
*/
class SavantCustom extends Savant3 {

	function __construct() {
		parent::__construct();
	}
	
	function addTitle($title) {
		$this->titulo .= ' | '.$title;
	}
	
	function setTitle($title) {
		$this->titulo = $title;
	}
	
	/**
	 *	Carrega um template
	 *
	 *	@access public
	 *	@param string $template arquivo de template
	 *	@param bool $header incluir ou não o header (opcional)
	 *	@param bool $footer incluir ou não o footer (opcional)
	*/
	function load($template, $header = true, $footer = true, $menu = true) {
		
		$this->show_menu = $menu;
		
		if ($header) {
			$this->header = $this->display('header.tpl');
		}
		
		
		$this->display(strtolower($template).'.tpl');
		
		if ($footer) {
			$this->footer = $this->display('footer.tpl');
		}
	}
}