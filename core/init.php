<?php

session_start();

$GLOBALS['config'] = array(
	'mysql' => array(
		'host' => 'localhost',
		'username' => 'cs411vacafun_group',
		'password' => 'rock2RULE',
		'db' => 'cs411vacafun_test'
    ),
    
	'remember' => array(
		'cookie_name' => 'login_cookie',
		'cookie_expiry' => 604800,
    ),
    
	'session' => array(
		'session_name' => 'user',
		'token_name' => 'token'
	)
);

spl_autoload_register(function($class) {
	if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/classes/" . $class . '.php')){
		require_once $_SERVER['DOCUMENT_ROOT'] . "/classes/" . $class . '.php';
	} else if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/classes/views/" . $class . '.php')){
		require_once $_SERVER['DOCUMENT_ROOT'] . "/classes/views/" . $class . '.php';
	} else{
		require_once $_SERVER['DOCUMENT_ROOT'] . "/classes/controller/" . $class . '.php';
	}
});

require_once $base . "/functions/sanitize.php";
require_once $base . "/functions/header.php";
require_once $base . "/functions/misc.php";

if(Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))){
	$hash = Cookie::get(Config::get('remember/cookie_name'));
	$hashCheck = DB::getInstance()->get('UserSession', array('hash', '=', $hash));
	
	if($hashCheck->count()){
		Session::put(Config::get('session/session_name'), $hashCheck->first()->uid);
		$user = new User($hashCheck->first()->uid);
		$user->login();
	}
}
