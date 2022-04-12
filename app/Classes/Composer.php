<?php

namespace App\Classes;

class Composer {

	private static $path = '/var/www/html';

	public static function load(){
		return json_decode(file_get_contents(self::$path.'/composer.json'), true);
	}

	public static function require($package, $version = null){
		if(!is_null($version)){ $version = ':'.$version; }
		$cmd = 'composer --working-dir="'.self::$path.'" require '.$package.$version;
		return shell_exec(trim($cmd));
	}

	public static function update($package = null){
		$cmd = 'composer --working-dir="'.self::$path.'" update '.$package;
		return shell_exec(trim($cmd));
	}

	public static function remove($package){
		$cmd = 'composer --working-dir="'.self::$path.'" remove '.$package;
		return shell_exec(trim($cmd));
	}
}