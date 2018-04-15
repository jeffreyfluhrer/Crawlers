<?php
class Validate {
	private $_passed = false,
			$_errors = array(),
			$_db = null;
	
	public function __construct(){
		$this->_db = DB::getInstance();
	}
	
	public function check($source, $items = array()){
		foreach($items as $item => $rules){
			$value = trim($source[$item]);
			$item_str = ucfirst(escape($item));
			foreach($rules as $rule => $rule_value){
				if($rule === 'required' && empty($value)){
					$this->addError("{$item_str} is required.");
				} else if(!empty($value)){
					switch($rule){
						case 'min':
							if(strlen($value) < $rule_value){
								$this->addError("{$item_str} must be a minimum of {$rule_value} characters.");
							}
						break;
						case 'max':
							if(strlen($value) > $rule_value){
								$this->addError("{$item_str} must be a maximum of {$rule_value} characters.");
							}
						break;
						case 'matches':
							if($value != $source[$rule_value]){
								$this->addError("{$item_str}" . 's' . " must match.");
							}
						break;
						case 'different':
							if($value == $source[$rule_value]){
								$this->addError("{$item_str}" . 's' . " must be different.");
							}
						break;
						case 'unique':
							$check = $this->_db->get($rule_value[0], array($item, '=', $value));
							if($check->count() && $rule_value[1] === 'onCreate'){
								$this->addError("{$item_str} is taken.");
							} else if($check->count() > 1 && $rule_value[1] === 'onUpdate'){
								$this->addError("{$item_str} is taken.");
							} 
						break;
						case 'email':
							$regexp = "/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i";
							if(!preg_match($regexp, $value)){
								$this->addError("{$item_str} is not a valid email.");
							}
						break;
						case 'phone':
							$value = str_replace('-', '', $value);
							$regexp = "/^[0-9]{10}$/";
							if(!preg_match($regexp, $value)){
								$this->addError("{$item_str} is not a valid phone number.");
							}
						break;
						case 'exists':
							$check = $this->_db->get($rule_value[0], array($rule_value[1], '=', $value)); 
							if(!$check->count()){
								$this->addError("{$item_str} does not exist.");
							}
						break;
						case 'password':
// 							echo $rule_value;
							$get = $this->_db->get('users', array("uid", '=' , "{$rule_value}"));
							$user = $get->first();
							if(!Hash::validate_password($value, $user->salt, $user->password)){
								$this->addError("Incorrect password.");
							}
						break;
					}
				}
			}
		}
		
		if(empty($this->_errors)){
			$this->_passed = true;
		}
		return $this;
	}
	
	private function addError($error){
		$this->_errors[] = $error;
	}
	
	public function errors(){
		return $this->_errors;
	}
	
	public function passed(){
		return $this->_passed;
	}
}