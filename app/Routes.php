<?php

/** 
 | |			  | |	   
 | |__   ___ _ __ | |_ ___  
 | '_ \ / _ \ '_ \| __/ _ \ 
 | |_) |  __/ | | | || (_) |
 |_.__/ \___|_| |_|\__\___/ 
					routing
 ---------------------------

* [API QUEUE] Aprire la coda e avere risposta *
	# Per abilitare o disabilitare l'api queue
	# bisogna inserire nel .env il boolean true/false la variabile WAIT_API

	# .env
	WAIT_API=true

	# Per sbloccare una chiamata api in richiesta
	# Il cron è gestito dal controller Cron
	# creare automatismo per eseguire chiamata e inserire il parametro cron:

	# Curl POST http://localhost:3200/api/v1/cron
	{
		"cron":1
	}

	# Per impedire che una rotta non passi attraverso il cron API QUEUE (se abilitata)
	# aggiungere il metodo ->cron(false)

	$router->map('GET', 'user/view/{id}', 'v1\User@view')->cron(false);
*/


 
$router->prefix('api/', function() use ($router){

	$router->group('v1/', function() use ($router) {
		$router->map('GET', 'composer', 'v1\Test@composer');
		$router->map('GET', 'test/{id}', 'v1\Test@view');
		$router->map('POST', 'test/{id}/{name}/{surname}', 'v1\Test@create');
	});

});
/*
$router->prefix('api/', function() use ($router){

	$router->group('v1/', function() use ($router) {
		$router->group('dashboard/', function() use ($router) {
			$router->map('GET', 'test/{id}', 'v1\Test@view');
			$router->map('GET', 'test2/{id}', 'v1\Test@view');
		});
	});

});
*/
/*
* Gestione auth con varie tipologie di autenticazione *
	
	# Il sistema accetta come middleware più tipologie di auth tra cui:
	#
	# Tipologia	 Metodo			  Parametri
	# -------------------------------------------
	# Bearer		Header			   Authorization: Bearer <token>
	# Token		 Header|POST		  token=<token>
	# User		  POST				 username=<username>&&password=<password>
	# Basic		 Header			   username:password || base64_encode(username:password)
	# JWT		   POST|Cookie		  auth_jwt=<token>
*/
$router->prefix('auth/', function() use ($router){
	$router->group('v1/', function() use ($router) {
		$router->map('GET|POST', 'bearer/{id}', 'v1\User@view')->middleware('v1\Auth\Bearer@index');
		$router->map('GET|POST', 'token/{id}', 'v1\User@view')->middleware('v1\Auth\Token@index');
		$router->map('GET|POST', 'username/{id}', 'v1\User@view')->middleware('v1\Auth\Username@index');
		$router->map('GET|POST', 'basic/{id}', 'v1\User@view')->middleware('v1\Auth\Basic@index');
		$router->map('GET|POST', 'jwt/{id}', 'v1\User@view')->middleware('v1\Auth\Jwt@index');
	});
});

$router->prefix('dev/', function() use ($router){
	$router->group('v1/', function() use ($router) {
		$router->map('GET', 'user/{id}', 'v1\User@view')->cron(false);
		$router->map('POST', 'user/{id}/{username}/{password}', 'v1\User@create')->cron(false);
	});
});


/**
 * TESTING
 */
$router->prefix('api/', function() use ($router){
	$router->group('v1/', function() use ($router) {
		$router->map('GET', 'time', 'v1\Test@time');
		$router->map('GET', 'utc/{timezone}', 'v1\Test@time');
	});
});