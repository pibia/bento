<?php

namespace Core\Utilities;

error_reporting(0);

use DateTime;

class Util {

	public static function dump($arr, $close = true) :string {
		var_dump($arr);
		if($close){ exit; }
	}

	public static function cron() : string {

		$string = bin2hex(openssl_random_pseudo_bytes(32));
		$divisors = [4,8,16,32];
		$string = str_split($string, $divisors[array_rand($divisors, 1)]);
		$string[] = time();
		array_unshift($string,date('His'));
		array_unshift($string,date('Ymd'));
		return implode('-', $string);
	}

	public static function isJson($string) : bool {
		return ((is_string($string) &&
				(is_object(json_decode($string)) ||
				is_array(json_decode($string))))) ? true : false;
	}

	public static function prettyPrint( $json ){
		$result = '';
		$level = 0;
		$in_quotes = false;
		$in_escape = false;
		$ends_line_level = NULL;
		$json_length = strlen( $json );

		for( $i = 0; $i < $json_length; $i++ ) {
			$char = $json[$i];
			$new_line_level = NULL;
			$post = "";
			if( $ends_line_level !== NULL ) {
				$new_line_level = $ends_line_level;
				$ends_line_level = NULL;
			}
			if ( $in_escape ) {
				$in_escape = false;
			} else if( $char === '"' ) {
				$in_quotes = !$in_quotes;
			} else if( ! $in_quotes ) {
				switch( $char ) {
					case '}': case ']':
						$level--;
						$ends_line_level = NULL;
						$new_line_level = $level;
						break;

					case '{': case '[':
						$level++;
					case ',':
						$ends_line_level = $level;
						break;

					case ':':
						$post = " ";
						break;

					case " ": case "\t": case "\n": case "\r":
						$char = "";
						$ends_line_level = $new_line_level;
						$new_line_level = NULL;
						break;
				}
			} else if ( $char === '\\' ) {
				$in_escape = true;
			}
			if( $new_line_level !== NULL ) {
				$result .= "\n".str_repeat( "\t", $new_line_level );
			}
			$result .= $char.$post;
		}

		return $result;
	}

}
