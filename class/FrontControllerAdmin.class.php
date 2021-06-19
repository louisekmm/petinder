<?php

class FrontControllerAdmin {
    const DEFAULT_CONTROLLER = "Index";
    const DEFAULT_ACTION     = "_default";
     
    protected $controller    = self::DEFAULT_CONTROLLER;
    protected $action        = self::DEFAULT_ACTION;
    protected $params        = array();


    public function __construct(array $options = array()) {
        if (empty($options)) {
           $this->parseUri();
        }
        else {
            if (isset($options["controller"])) {
                $this->setController($options["controller"]);
            }
            if (isset($options["action"])) {
                $this->setAction($options["action"]);     
            }
            if (isset($options["params"])) {
                $this->setParams($options["params"]);
            }
        }
    }
     
    protected function parseUri() {
		global $base_path;
		
        $path = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");
		
        if (strpos($path, $base_path) === 0) {
            $path = substr($path, strlen($base_path));
        }

		$path = trim($path, "/");
		//$path = preg_replace('/[^a-zA-Z0-9]/', "", $path);
		
        @list($controller, $action, $params) = explode("/", $path, 3);
		

		
		if (!$params) {
			$params = $controller;
		} else {
			$params = $controller.'/'.$params;
			
		}
		
		
		if (!$controller) {
			$controller = self::DEFAULT_CONTROLLER;
		}
	 	
        if (isset($controller)) {
            $this->setController($controller);
        }
		
        if (isset($action)) {
            $this->setAction('_'.$action);
        }
        if (isset($params)) {
            $this->setParams(explode("/", $params));
        }
    }
    
    public function setController($controller) {
        $controller = str_replace("admin", 'Admin', ucfirst($controller)) . "Controller";
		
		if (!is_file('class/'.$controller.'.class.php')) {
			$controller = 'DefaultController';
		}
        if (!class_exists($controller, true)) {
            $palavra = Meta::getLangFile('acao-front', $di);
            $palavra1 = Meta::getLangFile('nao-front', $di);
            throw new InvalidArgumentException($palavra.' '.$controller.' '.$palavra1);
        }
        $this->controller = $controller;
        return $this;
    }
     
    public function setAction($action) {
		
        $reflector = new ReflectionClass($this->controller);
        if (!$reflector->hasMethod($action)) {
			$this->setController(self::DEFAULT_CONTROLLER);
			$reflector2 = new ReflectionClass($this->controller);
			if (!$reflector2->hasMethod($action)) {
                $palavra = Meta::getLangFile('acao-front', $di);
                $palavra1 = Meta::getLangFile('nao-front', $di);
				throw new InvalidArgumentException($palavra.' '.$action.' '.$palavra1);
			}
            
        }
        $this->action = $action;
        return $this;
    }
     
    public function setParams(array $params) {
        $this->params = $params;
        return $this;
    }
     
    public function run() {
		
        call_user_func_array(array(new $this->controller, $this->action), $this->params);
    }
}