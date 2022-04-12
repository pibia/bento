<?php

namespace App\Controllers\v1;

use Core\{
	Response\Json,
	Utilities\Util,
	Controller\Main,
};

class cron extends Main {

	public function run(){
		
		if ($handle = opendir($_ENV['QUEUE_API_DIR'])) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					if(substr($entry, strpos($entry, '.')+1, strlen($entry)) != 'json') continue;

					$this->processfile($_ENV['QUEUE_API_DIR'].$entry);

					break; // only one file per time
				}
			}
			closedir($handle);
		}	  
		
	}

	private function processfile($filepath){

		$oldname = $filepath;
		$newname = substr($filepath, 0, -4).'proc';

		rename($oldname, $newname);
		
		if($content = json_decode(file_get_contents($newname))){

			$host = $content->request->headers->Host;
			$scheme = $content->request->headers->Scheme;
			$uri = $content->request->headers->Uri;
			$method = $content->request->headers->Method;
			
			switch(strtolower($method)){
				case 'post':
					$postvariables = $content->request->post;
					break;
				case 'put':
					$postvariables = $content->request->put;
					break;
				case 'patch':
					$postvariables = $content->request->patch;
					break;
			}			
		}

		$result = $this->makeApiCall($scheme, $host, $uri, $method, $postvariables);

		$this->processResults($result, $newname, (array)$content);
	}

	private function makeApiCall($protocol, $host, $url, $method, $postvariables = []){
		
		$post_fields = [
			'cron'=>'1'
		];

		$ch = curl_init($protocol.'://'.$host.$url);													 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);																	
		curl_setopt($ch, CURLOPT_HTTPHEADER, [																		  
				'Content-Type: application/json',																				
				'Accept: application/json'
			]																	   
		);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		
		if(!empty($postvariables))
			array_push($post_fields, $postvariables);


		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));
		
		$result = curl_exec($ch);

		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($result, 0, $header_size);
		$body = (array)json_decode(substr($result, $header_size));
		$body['httpcode'] = $httpcode;

		return $body;
	}

	private function processResults($results=null, $filename, $originalcontent){
		if($results['httpcode'] == 200){
			if(isset($originalcontent)){
				unset($results['httpcode']);
				$originalcontent['result'] = $results;
			}
			$originalcontent['httpcode'] = 200;
			$newname = substr($filename, 0, -4).'ela';
		} else {
			$originalcontent['httpcode'] = $results['httpcode'];
			unset($results['httpcode']);
			$originalcontent['error'] = $results;
			$newname = substr($filename, 0, -4).'err';
		}

		file_put_contents($filename, Util::prettyPrint(json_encode($originalcontent)));
		rename($filename, $newname);

		return;
	}
}