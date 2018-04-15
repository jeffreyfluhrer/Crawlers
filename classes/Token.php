<?php
class Token{
	public static function generate($name = null){
		if($name){
			return Session::put($name, md5(uniqid()));
		}
		return Session::put(Config::get('session/token_name'), md5(uniqid()));
	}

	public static function check($token, $tokenName = null){
		if(!$tokenName){
			$tokenName = Config::get('session/token_name');
		}
		
		if(Session::exists($tokenName) && $token === Session::get($tokenName)){
			Session::delete($tokenName);
			return true;
		}
		return false;
	}
}