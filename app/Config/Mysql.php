<?php

namespace App\Config;

class Mysql {

	private static $master = [
        'created' => 'created',
        'updated' => 'updated',
        'deleted' => 'deleted',
    ];

    public static function getAccess(){
        return [
			'host' => '0.0.0.0',
			'user' => $_ENV['DB_USERNAME'],
			'pass' => $_ENV['DB_PASSWORD'],
			'dbname' => $_ENV['DB_DATABASE'],
		];
    }

    public static function getMaster(){
        return self::$master;
    }
}
