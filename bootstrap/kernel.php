<?php

use Core\Routing\Router;
use Core\Utilities\Util;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../core/Support/helpers.php';


class kernel {

    private $mode = 'stage';

    private function invokeHandlers(){
        set_error_handler('Core\Errors\Handler::errorHandler');
        set_exception_handler('Core\Errors\Handler::exceptionHandler');
    }

    public function mode($mode = false){
        if(!$mode&&in_array($mode, ['stage', 'prod'])){
            $this->mode = $mode;
        }

        return $this;
    }

    public function run(){
        
        $this->invokeHandlers();

        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();

        $router = new Router();
        require_once __DIR__.'/../app/Routes.php';
        $router->run();
        
    }
}


