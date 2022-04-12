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

    public function wait(){
        $counter = 0;
        
        while(!file_exists($_ENV['QUEUE_API_DIR'].$this->fileforresults.'.ela')){
            usleep(250000); //waits a quarter of second
            $counter ++;
            if($counter >= 200) break;
        }

        if(@$content = json_decode(file_get_contents($_ENV['QUEUE_API_DIR'].$this->fileforresults.'.ela'))){
            Json::set($content->result)->send();
        } else {
            Api::error('408', 'Timeout api call');
        }
    }
}
