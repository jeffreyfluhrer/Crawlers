<?php
class Config {
	/**
	 * Retrieves config key values by config key path, as defined in init.php.
	 */
	public static function get($path = NULL){
		if($path){
			$config = $GLOBALS['config'];
			$path = explode('/', $path);
			
			foreach($path as $bit){
				if(isset($config[$bit])){
					$config = $config[$bit];
				} else { 
					return false;
				}
			}
			
			return $config;
		}
	}
}