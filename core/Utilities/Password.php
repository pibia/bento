<?php

namespace Core\Utilities;

class Password {

	public static function encode($password) : string{
		return password_hash($password, PASSWORD_BCRYPT);
	}

	public static function hash($password, $hashed) : bool {
		return password_verify($password, $hashed);
	}

	public static function generate($len = 8){

		$sets = [];
		$sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		$sets[] = '23456789';

		$password = '';

		//append a character from each set - gets first 4 characters
		foreach ($sets as $set) {
			$password .= $set[array_rand(str_split($set))];
		}

		//use all characters to fill up to $len
		while(strlen($password) < $len) {
			//get a random set
			$randomSet = $sets[array_rand($sets)];

			//add a random char from the random set
			$password .= $randomSet[array_rand(str_split($randomSet))];
		}

		//shuffle the password string before returning!
		return str_shuffle($password);
	}
}
