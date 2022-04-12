<?php

namespace App\Models\v1;

use Core\{
	Utilities\Util,
};

use \Core\Model\{
	Mysql,
	Redis,
};

class cron {

	private $mysql = null;
	private $redis = null;

	public function __construct(Mysql $mysql, Redis $redis){
		$this->mysql = $mysql;
		$this->redis = $redis;
	}
	
}
