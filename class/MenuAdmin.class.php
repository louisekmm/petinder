<?php

class MenuAdmin  {
	//use ComumAdmin, ContentFilter;
	
	const TABLE = 'menu_admin';
	
	public static function getMenu($di) {
		$db= $di->getDb();
		$r = $db->query('SELECT t.*,i.valor as icone from '.self::TABLE.' t LEFT JOIN '.'icone i ON i.id=t.idIcone ORDER BY top DESC, nome ASC, controller ASC')->fetchAll(PDO::FETCH_ASSOC);

		$sorted = array();
		
		foreach($r as &$category)   {
			
			if (!isset($category['children'])) {
				// set the children
				$category['children'] = array();
				foreach($r as &$subcategory ) {
					if($category['id'] == $subcategory['idMenu_Admin']) {
						$category['children'][] = &$subcategory;
					}
				}
			}

			if ($category['default']) {
				$category['children'] = array();
				$category['children'][] = array('icone'=> 'plus', 'nome'=> 'Adicionar', 'controller'=> $category['controller'], 'action'=> 'add', 'args'=> '', 'children' => array());
				$category['children'][] = array('icone'=> 'list-alt', 'nome'=> 'Listar', 'controller'=> $category['controller'], 'action'=> 'list', 'args'=> '', 'children' => array());
				
			}
			
			if ( is_null( $category['idMenu_Admin'] ) )
			{
				$sorted[] = &$category;
			}

		}

		return $sorted;
		
	}
	
}

