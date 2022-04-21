<?php

namespace Core\Response;

use Core\Utilities\Util;

class Json {

	private $data = null;
	private $response_code = 200;
	private static $_data = null;

	public function parse($data){

		$this->data = json_encode($data);
		return $this;
	}

	public static function set($json){
		self::$_data = json_encode($json);

		return new self();
	}

	public function send($code = 200, $json = false) : string {
		header("Content-Type: application/json");
		http_response_code($code);

		$this->data = self::$_data;

		if($json){ $this->data = $json; }
		if(is_array($this->data)){
			$this->data= json_encode($this->data);
			if ($this->data === false) {
				$this->data = json_encode(["jsonError" => json_last_error_msg()]);
				if ($this->data === false) {
					$this->data = '{"jsonError":"unknown"}';
				}
				http_response_code(500);
			}
		}
		
		http_response_code($this->response_code);
		echo $this->data; exit;
	}

	public function header($response_code = 200){
		$this->response_code = $response_code;

		return $this;
	}

	public function return(){
		return $this->data;
	}

}
