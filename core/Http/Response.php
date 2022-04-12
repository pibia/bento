<?php

namespace Core\Http;

use Core\Utilities\Util;

class Response {

    public static function redirect($url, $status){

        header('Location: '.$url, true, $status);
    }

    public static function serverAddr() : string {
        return $_SERVER['SERVER_ADDR'];
    }
}
