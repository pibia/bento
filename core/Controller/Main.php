<?php

namespace Core\Controller;

use Core\Http\{
	Input,
	Request,
	Response,
};

use Core\{
	Response\Api,
	Response\Json,
	Config\Api as apiConfig,
	Utilities\Util,
};

abstract class Main {

	public $params = [];
	public $request = [];
	public $auth = [];
	public $method = NULL;
	public $jwt = null;
	public $user = null;
	public $model = null;
	public $api = null;
	public $middleware = null;
	public $fileforresults = null;

	public function __construct(){
		
		$this->api = new Api();
	}

	public function setParams(array $args){
		$this->params = $args;
	}

	public function setRequest(object $args){
		$this->request = $args;
	}

	public function setMiddleware($middleware){
		$this->middleware = $middleware;
	}

	public function setAuth(array $args){
		$this->auth = $args;
	}

	public function setMethod(string $args){
		$this->method = $args;
	}

	public function setSlave(string $slave){
		$this->slave = (new apiConfig)->returnCluster($slave);
	}

	public function setModel($model){
		
		$this->model = $model;
	}
}
