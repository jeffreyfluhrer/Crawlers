<?php
class User {
	private $_db,
        $_data,
        $_sessionName,
        $_cookieName,
        $_error,
        $_isLoggedIn = false,
        $_isAdmin = false;
	
	public function __construct($user = null){
		$this->_db = DB::getInstance();
		$this->_sessionName = Config::get('session/session_name');
		$this->_cookieName = Config::get('remember/cookie_name');
		
		if(!$user){
			if(Session::exists($this->_sessionName)){
				$session = Session::get($this->_sessionName);
				
				if($this->find($session)){
                    $this->_isLoggedIn = true;
                    
					if($this->data()->IsAdmin != 0){
						$this->_isAdmin = true;
					}
				}
			}
		} else {
			$this->find($user);	
		}
	}
	
	public function create($username, $password){
        $userCheck = $this->_db->get('UserInfo', array('username', '=', $username));
        
		if(!$userCheck->count()){
			$hashInfo = Hash::create_hash($password);
			if($this->_db->insert('UserInfo', array(
				'username' => $username,
				'password' => $hashInfo['hash'],
				'salt' => $hashInfo['salt']
			))){
				return true;
			} else {
				$this->setError('Error in registration.');
				return false;
			}
		} else {
			$this->setError('Username already exists.');
			return false;
		}
	}
	
	public function update($fields = array(), $username = null){
		if(!$username && $this->isLoggedIn()){
			$username = $this->data()->username;
		} 
		
		if($this->_db->update('UserInfo', array('username', $username), $fields)){
			return true;
		} else {
			$this->setError('Error updating details.');
		}
		return false;
	}
	
	
	public function find($user = null){
		if($user){
			$field = 'username';
			$data = $this->_db->get('UserInfo', array($field, '=', $user));
			
			if($data->count()){
				$this->_data = $data->first();
				return true;
			} 
		}
		return false;
	} 
	
	public function login($username = null, $password = null, $remember = false){
		if(!$username && !$password && $this->exists()){
			Session::put($this->_sessionName, $this->data()->username);	
		} else {
			$user = $this->find($username);
				
			if($user){	
				if(Hash::validate_password($password, $this->data()->salt, $this->data()->password)){
					Session::put($this->_sessionName, $this->data()->username);
					
					if($remember){
						$hashCheck = DB::getInstance()->get('UserSession', array('username', '=', $this->data()->username));
						if(!$hashCheck->count()){
							$hash = md5(uniqid());
							$hashCheck->insert('UserSession', array(
								'username' => $this->data()->username,
								'hash' => $hash
							));
						} else {
							$hash = $hashCheck->first()->hash;
						}
						
						Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
					}
					
					return true;
				} else{
					$this->setError('Invalid username password combination.');
				}
			} else {
				$this->setError('Invalid username password combination.');
			}
		}
		return false;
	}
	
	public function isAdmin(){
		return $this->_isAdmin;
	}
	public function logout(){
		$this->_db->delete('UserSession', array('username', '=' ,$this->data()->username));
		Session::delete($this->_sessionName);
		Cookie::delete($this->_cookieName);
	}
	
	public function data(){
		return $this->_data;
	}
	
	public function error(){
		return $this->_error;
	}
	
	
	public function isLeader(){
		return $this->_isLeader;
	}
	
	public function setError($error){
		$this->_error = $error;
	}
	
	public function exists(){
		return (!$this->data());
	}
	
	public function isLoggedIn(){
		return $this->_isLoggedIn;
	}
}