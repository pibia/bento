<?php

namespace Core\Http;

class Request {

	public static function getRequestUri(){
		return $_SERVER['REQUEST_URI'];
	}

	public static function getRequest(){
		return $_SERVER['REQUEST_METHOD'];
	}
}
