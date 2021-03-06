<?php


function public_path(string $file = null){
	if($file){
		return dirname(dirname(dirname(__FILE__))).'/public'.'/'.$file;
	}

	return dirname(dirname(dirname(__FILE__))).'/www';
}


function base_path(string $file = null){
	if($file){
		return dirname(dirname(dirname(__FILE__))).'/'.$file;
	}

	return dirname(dirname(dirname(__FILE__)));
}
