<?php


trait parseOptions {
	


	function isVisible() {
		
		
	}

	public static function isCheckBox($list) {
		return (in_array('check', $list));
	}
	
	public static function isFilterDate($list) {
		return (in_array('filterdate', $list));
	}
	
	public static function isDate($list) {
		return (in_array('d', $list) || in_array('date', $list));
	}
	
	public static function isColumn($list) {
		return (in_array('c', $list) || in_array('column', $list));
	}
	
	public static function isForeign($list) {
		return (in_array('f', $list) || in_array('foreign', $list));
	}
	
	public static function isMoney($list) {
		return (in_array('money', $list));
	}
}