<?php

namespace App\Controllers\v1;

use Core\{
	Response\Json,
	Response\Api,
	Utilities\Util,
	Utilities\Password,
	Controller\Main,
};

use App\Classes\Composer;

class test extends Main {

	// $this->request
	
	public function composer(){
		util::dump(Composer::load());
	}

	public function time(){
		Json::set([
			'time' => date('c')
		])->send();
	}
}