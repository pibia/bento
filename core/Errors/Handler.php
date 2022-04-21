<?php

namespace Core\Errors;

use Core\Utilities\Util;

final class Handler {

	public static function errorHandler($level, $message, $file, $line){
		if (error_reporting() !== 0) {
			throw new \ErrorException($message, 0, $level, $file, $line);
		}
	}

	public static function exceptionHandler($e){

		http_response_code(500);

		$error = [
			'Title' => 'Fatal Error',
			'Error' => get_class($e),
			'Message' => $e->getMessage(),
			'File' => $e->getFile(),
			'Line' => $e->getLine(),
			'Datetime' => date('c'),
			'Addr' => $_SERVER['REMOTE_ADDR'],
			'URI' => $_SERVER['REQUEST_URI'],
			'logFile' => 'logs/errors/'.date('Y-m-d').'.log',
			'Method' => $_SERVER['REQUEST_METHOD'],
			'_REQUEST' => $_REQUEST,
			'_HEADERS' => getallheaders(),
		];
		
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($error);
	}
}
