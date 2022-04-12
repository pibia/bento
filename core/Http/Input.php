<?php

namespace Core\Http;

class Input {

	public static function hasPost(string $name) :bool {
		return array_key_exists($name, $_POST);
	}

	public static function hasGet(string $name) :bool {
		return array_key_exists($name, $_GET);
	}

	public static function hasServer(string $name) :bool {
		return array_key_exists($name, $_SERVER);
	}

	public static function hasFile(string $name) :bool {
		return array_key_exists($name, $_FILES);
	}

	public static function get(string $name){

		return (isset($_GET[$name]) ? $_GET[$name] : null);
	}

	public static function post(string $name) {
		return (isset($_POST[$name]) ? $_POST[$name] : null);
	}

	public static function server(string $name){
		return (isset($_SERVER[$name]) ? $_SERVER[$name] : null);
	}

	public static function file(string $name) {
		return (isset($_FILES[$name]) ? $_FILES[$name] : null);
	}

	public static function setGet(string $name, string $value){
		return $_GET[$name] = $value;
	}

	public static function setPost(string $name, string $value){
		return $_POST[$name] = $value;
	}

	private static function getAuthorizationHeader(){
		$headers = null;
		if (isset($_SERVER['Authorization'])) {
			$headers = trim($_SERVER["Authorization"]);
		}
		else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
			$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
		} elseif (function_exists('apache_request_headers')) {
			$requestHeaders = apache_request_headers();
			$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
			if (isset($requestHeaders['Authorization'])) {
				$headers = trim($requestHeaders['Authorization']);
			}
		}
		return $headers;
	}

	public static function getBearer() {
		$headers = self::getAuthorizationHeader();
		if (!empty($headers)) {
			if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
				return $matches[1];
			}
		}
		return null;
	}

	public static function methodPost() : array {
		return $_POST;
	}

	public static function methodGet() : array {
		return $_GET;
	}
}
