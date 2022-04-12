<?php

namespace App\Config;

use App\Config\Mysql as MysqlConfig;
use Core\{
    Utilities\Util,
};

class Database {

	private static $instance;
	private static $_mysql;
	private static $_redis = [
		'user' => null,
		'pass' => null,
		'dbname' => null,
	];

	private function __construct(){ 
		self::$_mysql = MysqlConfig::getAccess();
		
	}

	public static function getInstance(){
		if(self::$instance === null){
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function mysql() : array{
		return self::$_mysql;
	}

	public function redis() : array{
		return self::$_redis;
	}
}
