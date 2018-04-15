<?php
class Input {

	/**
	 * Checks if any POST, GET or both parameters are set.
	 */
	public static function exists($type = 'post'){
		switch($type){
			case 'post':
				return (!empty($_POST) && !Input::get('navigation')) ? true : false;
			break;
			case 'get':
				return (!empty($_GET)) ? true : false;
			break;
			default:
				return false;
			break;
		}
	}

	/**
	 * Validates the input token submitted by the user matches. This prevents Cross-Site Forgery Attacks.
	 */
	public static function validateToken()
	{
		return Token::check(Input::get(Config::get('session/token_name')));
	}
	
	public static function get($item){
		if(isset($_POST[$item])){
			return $_POST[$item];
		} else if(isset($_GET[$item])){
			return $_GET[$item];
		}
		return '';
	}
}