<?php

namespace App\Middleware\v1\Auth;

use Core\Http\{
	Input,
	Request,
	Response,
};

use Core\{
	Response\Api,
	Response\Json,
	Utilities\Util,
	Controller\Main,
};



class jwt extends Main {

	public function index(){
		return [
			'auth' => [
				'jwt' => [
					'id' => 0,
					'username' => 'create jwt auth',
				],
			],
		];
	}

}