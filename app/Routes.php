<?php

/** 
 | |			  | |	   
 | |__   ___ _ __ | |_ ___  
 | '_ \ / _ \ '_ \| __/ _ \ 
 | |_) |  __/ | | | || (_) |
 |_.__/ \___|_| |_|\__\___/ 
					routing
 ---------------------------
**/

/**	
	* Gestione auth con varie tipologie di autenticazione *
	
	# Il sistema accetta come middleware più tipologie di auth tra cui:
	#
	# Tipologia	 Metodo			  Parametri
	# -------------------------------------------
	# Bearer	Header			Authorization: Bearer <token>
	# Token		Header|POST		token=<token>
	# User		POST			username=<username>&&password=<password>
	# Basic		Header			username:password || base64_encode(username:password)
	# JWT		POST|Cookie		auth_jwt=<token>
*/
$router->prefix('auth/', function() use ($router){
	$router->group('v1/', function() use ($router) {
		$router->map('GET|POST', 'bearer/{id}', 'v1\User::view')->middleware('v1\Auth\Bearer::index');
		$router->map('GET|POST', 'token/{id}', 'v1\User::view')->middleware('v1\Auth\Token::index');
		$router->map('GET|POST', 'username/{id}', 'v1\User::view')->middleware('v1\Auth\Username::index');
		$router->map('GET|POST', 'basic/{id}', 'v1\User::view')->middleware('v1\Auth\Basic::index');
		$router->map('GET|POST', 'jwt/{id}', 'v1\User::view')->middleware('v1\Auth\Jwt::index');
	});
});


/**
 * TESTING
 */
$router->prefix('api/', function() use ($router){
	$router->group('v1/', function() use ($router) {
		$router->map('GET', 'time', 'v1\Test::time');
		$router->map('GET', 'utc/{timezone}', 'v1\Test::time');
	});
}); 

$router->get('/test-array', [v1\Index::class, 'array']);
$router->get('/test-string', 'v1\Index::string');

$router->group('/users', function () use ($router) {
	$router->map('GET', '{id}', [v1\Users::class, 'get']);
	$router->map('POST', '{id}', [v1\Users::class, 'create']);
	$router->map('DELETE', '{id}', [v1\Users::class, 'delete']);
});


// Anonymous function
$browser = $_SERVER['HTTP_USER_AGENT'];
$router->add('/', function () use ($browser) {
	echo 'You called anonymous function with browser '.$browser;
});