<?php

namespace Core\Routing;

use Core\Http\{
	Input,
	Request,
	Response
};

use Core\{
	Response\Api,
	Utilities\Util,
};

use Closure;
use DI\ContainerBuilder;

final class Router {

	protected static $instance;

	protected $uri = null;
	protected $routes = [];
	protected $lastPath = null;
	protected $path = null;
	protected $slave = null;
	protected $group = '';
	protected $prefix = '';

	public function __construct(){
		//$this->setUri();
	}

	public static function getInstance(){
		if(self::$instance === null){
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function setUri(){
		$this->uri = trim(Request::getRequestUri(), '/');
	}

	public function add(string $path, mixed $action): object
	{
		
		$r = $this->setParams($path);
		$this->path .= $path;

		if (is_callable($action)) {
			$this->routes[$r['path']]['callable'] = $action;
		} elseif (is_array($action)) {
			$this->routes[$r['path']]['action'] = [
				'Controller' => $action[0],
				'Method' => $action[1]
			];
		} else {
			$this->routes[$r['path']]['action'] = [
				'Controller' => explode('::', $action)[0],
				'Method' => explode('::', $action)[1]
			];
		}

		$this->routes[$r['path']]['params'] = $r['params'];
		$this->routes[$r['path']]['request'] = $r['request'];
		$this->routes[$r['path']]['auth'] = isset($r['auth']) ?? $r['auth'];

		$this->routes[$r['path']]['method'] = $r['method'];
		$this->lastPath = $r['path'];

		return $this;
	}

	public function get(string $path, mixed $action) : object {
		$this->prefix = '';
		$this->group = '';
		
		return $this->map('GET', $path, $action);
	}

	public function post(string $path, mixed $action) : object {
		$this->prefix = '';
		$this->group = '';
		
		return $this->map('POST', $path, $action);
	}

	public function put(string $path, mixed $action) : object {
		$this->prefix = '';
		$this->group = '';
		
		return $this->map('PUT', $path, $action);
	}

	public function delete(string $path, mixed $action) : object {
		$this->prefix = '';
		$this->group = '';
		
		return $this->map('DELETE', $path, $action);
	}

	public function options(string $path, mixed $action) : object {
		$this->prefix = '';
		$this->group = '';
		
		return $this->map('OPTIONS', $path, $action);
	}

	public function patch(string $path, mixed $action) : object {
		$this->prefix = '';
		$this->group = '';
		
		return $this->map('PATCH', $path, $action);
	}

	public function map(string $method, string $path, mixed $action){

		$path = "{$this->prefix}/{$this->group}/{$path}";
		$path = trim($path, '/');
		
		$r = $this->setParams($path);
		$this->path .= $path;

		$this->routes[$r['path']]['action'] = [];
		if (is_callable($action)) {
			$this->routes[$r['path']]['callable'] = $action;
		} elseif (is_array($action)) {
			$this->routes[$r['path']]['action'] = [
				'Controller' => $action[0],
				'Method' => $action[1]
			];
		} else {
			$this->routes[$r['path']]['action'] = [
				'Controller' => explode('::', $action)[0],
				'Method' => explode('::', $action)[1]
			];
		}

		$this->routes[$r['path']]['params'] = $r['params'];
		$this->routes[$r['path']]['request'] = $r['request'];
		$this->routes[$r['path']]['method'] = $r['method'];
		
		foreach(explode('|', $method) as $m){
			$this->routes[$r['path']]['methods'][$m] = $this->routes[$r['path']]['action'];
		}
		
		if(!empty($this->routes[$r['path']]['map'])){
			
			$this->routes[$r['path']]['map'][] = $method;
		} else {
			$this->routes[$r['path']]['map'] = explode('|', $method);
		}
		
		$this->lastPath = $r['path'];

		return $this;
	}

	public function prefix($prefix, Closure $r){
		$prefix = trim($prefix, '/');
		$this->prefix = $prefix;
		$r();
		
	}

	public function group($path, Closure $r){
		
		$path = trim($path, '/');
		$this->group = $path;
		$r();
		return $this;
	}

	public function tokens(array $regex = []){

		if(is_null($this->lastPath)){ return $this; }
		if($this->uri!==$this->lastPath){ return $this; }

		if(file_get_contents("php://input")!==''){
			$input = json_decode(file_get_contents("php://input"), true);
		}

		foreach($regex as $name => $reg){

			if(is_null($reg)){ continue; } // se è nullo contino

			if(isset($input[$name])){
				$this->routes[$this->lastPath]['params'][$name] = $input[$name];
			}

			if(preg_match("/^\/.+\/[a-z]*$/i",$reg)){ // solo se è una regex
				if(isset($this->routes[$this->lastPath]['params'][$name])){
					$this->routes[$this->lastPath]['params'][$name] = preg_replace('/'.$reg.'/', '', $this->routes[$this->lastPath]['params'][$name]);
				}
			}
			
			@$c = $this->routes[$this->lastPath]['params'][$name];
			
			switch(strtolower($reg)){
				case '<int>':
					$this->routes[$this->lastPath]['params'][$name] = (is_int($c)? $c : ['error' => 'invalid integer', 'key' => $name, 'value' => $c]);
					break;
				case '<boolean>':
					$c = (in_array($c, array('true', 'false'))? ($c=='true'? true : false) : $c);
					$this->routes[$this->lastPath]['params'][$name] = (is_bool($c)? $c : ['error' => 'invalid boolean', 'key' => $name, 'value' => $c]);
					break;
				case '<string>':
					$this->routes[$this->lastPath]['params'][$name] = (is_string($c)? $c : ['error' => 'invalid string', 'key' => $name, 'value' => $c]);
					break;
				case '<json>':
					$this->routes[$this->lastPath]['params'][$name] = (is_array(json_decode($c, true))? json_decode($c, true) : ['error' => 'invalid json', 'key' => $name, 'value' => $c]);
					break;
				default:
					if(substr($reg, 0,12)=='<String => ('){
						preg_match('#\((.*?)\)#', $reg, $match);
						@$data = explode('|', $match[1]);
						$this->routes[$this->lastPath]['params'][$name] = (in_array($c, $data)? $c : ['error' => 'invalid string value', 'key' => $name, 'value' => $c]);
					}
			}
		}

		if(!empty($this->routes[$this->lastPath]['params'][$name]['error'])){
			(new Api())->send($this->routes[$this->lastPath]['params']);
		}


		$this->lastPath = null;
		return $this;

	}

	public function middleware($action){

		$class = [
			'controller' => explode('::', $action)[0],
			'method' => explode('::', $action)[1],
			'data' => [],
		];

		$this->routes[$this->lastPath]['middleware'][$this->routes[$this->lastPath]['method']] = $class;
		return $this;
	}

	public function api(bool $api = false){

		if(is_null($this->lastPath)){ return; }

		$this->routes[$this->lastPath]['api'] = $api;

		// Non ricordo perchè dovevo annullare la var lastPath se è un api
		//$this->lastPath = null;
		return $this;

	}

	public function login(bool $login = false){

		if(is_null($this->lastPath)){ return; }

		if(!$login){ $login = true; } else { $login = false; }

		$this->routes[$this->lastPath]['api'] = $login;

		// Non ricordo perchè dovevo annullare la var lastPath se è un api
		//$this->lastPath = null;
		return $this;

	}

	public function slave(string $slave){
		$this->slave = $slave;
		return $this;
	}

	private function setParams(string $path){

		$url = explode('/', trim(Request::getRequestUri(), '/'));
		$request = explode('/', trim($path, '/'));
		$search = [];

		foreach((array)$request as $i => $req){
			if(isset($url[$i])){
				$search[$req] = $url[$i];
			}
		}

		preg_match_all('/{(.*?)}/', $path, $matches);

		$params = [];
		
		if(file_get_contents("php://input")!==''&&util::isJson(file_get_contents("php://input"))){
			$_POST = json_decode(file_get_contents("php://input"), true);
			$_REQUEST = json_decode(file_get_contents("php://input"), true);
		}
		
		foreach((array)$matches[0] as $i => $match){

			if(Input::hasPost($matches[1][$i])){ $params[$matches[1][$i]] = Input::post($matches[1][$i]); }
			elseif(Input::hasFile($matches[1][$i])){ $params[$matches[1][$i]] = Input::file($matches[1][$i]); }
			else { $params[$matches[1][$i]] = null; }

			if(!isset($search[$match])||is_null($search[$match])){ continue; }
			$params[$matches[1][$i]] = $search[$match];
			foreach($request as $x => $v){
				if($v==$match){ $request[$x] = $search[$match]; }
			}
		}

		foreach((array)$matches[0] as $i => $match){
			foreach($request as $x => $v){
				if($v==$match){ unset($request[$x]); }
			}
		}
		
		$params = [
			'path' => trim(implode('/', $request), '/'),
			'params' => $params,
			'method' => $_SERVER['REQUEST_METHOD'],
			'request' => $this->getRequest($params),
		];

		return $params;

	}

	private function getRequest($params = []){

		$data = [
			'get' => [],
			'post' => [],
			'put' => [],
			'patch' => [],
			'delete' => [],
			'json' => [],
			'http' => [
				'auth' => [],
			],
			'headers' => [],
			'method' => $_SERVER['REQUEST_METHOD'],
		];

		if(!empty($_SERVER['PHP_AUTH_USER'])){ $data['http']['auth']['user'] = $_SERVER['PHP_AUTH_USER']; }
		if(!empty($_SERVER['PHP_AUTH_PW'])){ $data['http']['auth']['password'] = $_SERVER['PHP_AUTH_PW']; }
		if(!empty($_SERVER['HTTP_AUTHORIZATION'])){
			if(strpos($_SERVER['HTTP_AUTHORIZATION'], 'Bearer ')!== false){
				$data['http']['token'] = str_replace('Bearer ', '', substr($_SERVER['HTTP_AUTHORIZATION'], strpos($_SERVER['HTTP_AUTHORIZATION'], 'Bearer ')));
			} else {
				$data['http']['authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
			}
		}

		foreach($_SERVER as $key => $value) {
			if(
				substr($key, 0, 5)!=='HTTP_'
				&&substr($key, 0, 8)!=='REQUEST_'
			){
				continue;
			}
			$header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, strpos($key, '_')-strlen($key)+1)))));
			$data['headers'][$header] = $value;
		}

		foreach($_REQUEST as $field => $value){
			if(util::isJson($value)){
				$data['json'] = json_decode($value, true);
			}
			
			$data[strtolower($_SERVER['REQUEST_METHOD'])][$field] = $value;
		}

		if(file_get_contents("php://input")!==''&&util::isJson(file_get_contents("php://input"))){
			$data['json'] = json_decode(file_get_contents("php://input"), true);
		}

		foreach($params as $field => $value){
			$data[strtolower($_SERVER['REQUEST_METHOD'])][$field] = $value;
		}
		$data = (object) $data;

		return $data;
	}

	private function executeAction(array $action){
		
		if(!empty($action['callable'])&&is_callable($action['callable'])){
			$action['callable'](); exit;
		}
		if(!empty($action['middleware'])){
			
			$method = $action['middleware']['method'];
			$class = '\App\Middleware\\'.$action['middleware']['controller'];
			
			if(!class_exists($class)){
				Api::error('500', 'Class "'.$class.'" not found.');
			}
			
			
			/**
			 * Creo il controller eseguendo solo la creazione del controller
			 */
			//$controllerInstantiate = new $class();

			/**
			 * Creo il controller con la dependency injection
			 */
			$containerBuilder = new ContainerBuilder;
			$container = $containerBuilder->build();
			$controllerInstantiate = $container->get($class);

			$this->initializationController($controllerInstantiate, $action, $method);

			$action['middleware'] = call_user_func_array([$controllerInstantiate, $method], $action['params']);
			
		}
		
		$method = $action['action']['Method'];

		$controller = $action['action']['Controller'];
		$class = '\App\Controllers\\'.$controller;
		
		if(!class_exists($class)){
			Api::error('500', 'Class "'.$class.'" not found.');
		}
		
		/**
		 * Creo il controller eseguendo solo la creazione del controller
		*/

		/**
		 * Creo il controller con la dependency injection
		*/
		$containerBuilder = new ContainerBuilder;
		
		$container = $containerBuilder->build();
		$controllerInstantiate = $container->get($class);
		$this->initializationController($controllerInstantiate, $action, $method);

		$model = '\App\Models\\' . $controller;
		if (class_exists($model)) {
			
			$controllerInstantiate->setModel($container->get($model));
		}
		
		$r = call_user_func_array([$controllerInstantiate, $method], $action['params']);
		$this->generateReturn($r);
	}

	private function generateReturn(mixed $r) : void {
		if (is_string($r)) {
			echo $r;
		} elseif (is_array($r)) {
			util::dump($r);
		}
	}

	private function initializationController($controllerInstantiate, $action, $method) : void {
		if(!method_exists($controllerInstantiate, $method)){
			Api::error('500', 'Method "'.$method.'" not found in '.get_class($controllerInstantiate).'.');
		} 

		if(!empty($action['middleware'])){
			$controllerInstantiate->setMiddleware($action['middleware']);
		}

		if(!empty($action['params'])){
			$controllerInstantiate->setParams($action['params']);
		}

		if(!empty($action['request'])){
			$controllerInstantiate->setRequest($action['request']);
		}

		if(!empty($action['auth'])){
			$controllerInstantiate->setAuth($action['auth']);
		}

		if(!empty($action['method'])){
			$controllerInstantiate->setMethod($action['method']);
		}

		if(!is_null($this->slave)){
			$controllerInstantiate->setSlave($this->slave);
		}
	}

	public function run(string $uri){
		$this->uri = $uri;
		
		if(isset($this->routes[$this->uri])){
			
			if(
				!empty($this->routes[$this->uri]['map'])
				&&!in_array($this->routes[$this->uri]['method'], $this->routes[$this->uri]['map'])
			){
				Api::error('405', 'Error map route'); 
			}
			
			if(!empty($this->routes[$this->uri]['methods'])){
				
				if(!empty($this->routes[$this->uri]['middleware'])){
					$this->routes[$this->uri]['middleware'] = $this->routes[$this->uri]['middleware'][$this->routes[$this->uri]['method']];
				}
				
				$this->routes[$this->uri]['action'] = $this->routes[$this->uri]['methods'][$this->routes[$this->uri]['method']];
			}
			
			return $this->executeAction($this->routes[$this->uri]);
		} else {
			Api::error('403', 'Unfound route'); 
		}
	}
}
