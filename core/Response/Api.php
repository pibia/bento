<?php

namespace Core\Response;

use Core\{
	Response\Json,
	Utilities\Util,
};

use DateTime;

class Api {

	static $errors = [
		'500' => [
			'type' => 'error',
			'result' => 'unrecognized error',
			'datetime' => null,
		],
	];

	public static function error($error = false, $text = false){

		self::$errors[$error]['datetime'] = (new DateTime())->format('c');
		
		if($text!==false&&isset(self::$errors[$error])){ self::$errors[$error]['result'] = $text; }
		$result = (new Json)->parse((isset(self::$errors[$error])? self::$errors[$error] : self::$errors['500']))->return();

		header('Content-Type: application/json');
		http_response_code((isset(self::$errors[$error])? $error : 500));
		
		echo $result; die;
	}

	public static function generate($code, $data){

		header('Content-Type: application/json');
		http_response_code($code);
		exit(json_encode($data));
	}

	public static function success(){
		header('Content-Type: application/json');
		http_response_code(200);
	}

	public static function send($data){
		header('Content-Type: application/json');
		http_response_code(200);

		exit((new Json)->parse($data)->return());
	}

}
