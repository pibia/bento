<?php

namespace Core\Errors;

use Core\Utilities\Util;

class Handler {

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

		$html = '
		<!doctype html>
			<html>
			<head>
			<meta charset="utf-8">
				<title>Fatal Error</title>
				<style type="text/css">
					body {
						margin: 0;
						padding: 0;
						width: 100%;
						margin: 0px auto 0px;
						background-color: #18181a;
					}
					#json-input {
						display: none;
					}
					#json-display {
						margin: 0;
						padding: 10px 20px;
					}
				</style>
			</head>
			<body>

			<script src="https://jophiel.k-websrv-dev.it/assets/template/vendor/jquery/jquery-3.3.1.js"></script>
			<script type="text/javascript" src="https://jophiel.k-websrv-dev.it/assets/template/vendor/json-viewer/dist/jquery.json-editor.min.js"></script>
				<textarea id="json-input">
					'.json_encode($error).'

				</textarea>
				<pre id="json-display"></pre>

				<script type="text/javascript">

					function getJson() {
						try {
							return JSON.parse($("#json-input").val());
						} catch (ex) {
							alert("Wrong JSON Format: " + ex);
						}
					}

					var editor = new JsonEditor("#json-display", getJson());
				</script>
			</body>
			</html>
		';
		echo $html;
	}
}
