<?php
class Redirect{
	
	public static function to($location=null){
		$base = $_SERVER['DOCUMENT_ROOT'];
		if($location){

			if(is_int($location)){

				switch($location){
					
					case 403:
						header('HTTP/1.0 403 Forbidden Access');
						include $base . '/includes/errors/403.php';
						exit();
						break;
						
					case 404:
						header('HTTP/1.0 404 Not Found');
						include $base . '/includes/errors/404.php';
						exit();
						break;

					case 502:
						break;
				}
				
			}

			header('Location: ' . $location);
			exit();
		}
	}
	
}