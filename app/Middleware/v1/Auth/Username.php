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

use App\Models\v1\{
	User,
};

class username extends Main {

	public $user = null;
	private $_username = null;
	private $_password = null;

	public function __construct(User $user){
		$this->user = $user;
	}

	public function index(){
		
		if(empty($this->request->post['username'])){
			Api::error(500, 'Authorization: Username is not set.');
		} elseif(empty($this->request->post['password'])){
			Api::error(500, 'Authorization: Password is not set.');
		} else {
			$this->_username = $this->request->post['username'];
			$this->_password = $this->request->post['password'];
		}

		$user = $this->user->getUsername($this->_username);
		if(!$user){
			Api::error(500, 'Error. User_ or password not recognized.');
		} elseif(!password_verify($this->_password, $user['password'])) {
			Api::error(500, 'Error. User or password_ password.');
		} else {
			return [
				'auth' => [
					'username' => [
						'id' => $user['id'],
						'username' => $user['username'],
					],
				],
			];
		}
	}

}