<?php
class DB {
	private static $_instance = null;
	private $_pdo,
        $_query,
        $_error = false,
        $_results,
        $_count = 0,
        $_assoc;
	
	private function __construct(){
		try{
            $connectionString = 'mysql:host='
                . Config::get('mysql/host')
                . ';dbname='
                . Config::get('mysql/db');

			$this->_pdo = new PDO(
                $connectionString,
                Config::get('mysql/username'),
                Config::get('mysql/password'));

		} catch (PDOException $e){
			die($e->getMessage());
		}
	}
	
	public static function getInstance(){
		if(!isset(self::$_instance)){
			self::$_instance = new DB();
		}
		return self::$_instance;
	}
	
	public function query($sql, $params = array()){
		$this->_error = false;
		if($this->_query = $this->_pdo->prepare($sql)){
			$x = 1;
			if(count($params)){
				foreach($params as $param){
					$this->_query->bindValue($x, $param);
					$x++;
				}
			}
			if($this->_query->execute()){
				$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_count = $this->_query->rowCount();
			} else{
				error_log(print_r($this->_query->errorInfo()));
				$this->_error = true;
			}
		}
		return $this;
	}
	
	public function order($table, $field){
		$sql = "SELECT * FROM {$table} ORDER BY {$field}";
		if(!$this->query($sql)->error()){
			return $this;
		} 
		return $this;
	}
	
	public function action($action, $table, $where = array()){
		if(count($where) === 3){
			$operators = array('=' , '>', '<', '>=' , '<=', 'IN');
			
			$field 		= $where[0];
			$operator 	= $where[1];
			$value 		= $where[2];
			
			if(in_array($operator, $operators)){
				if($operator == 'IN'){
					$sql = "{$action} FROM {$table} WHERE {$field} IN (" . implode("," , $value) . ")";
					$params = null;
				} else {
					$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
					$params = array($value);
				}
				
				if(!$this->query($sql, $params)->error()){
					return $this;
				}
			}
		} else if (count($where) === 0){
			$sql = "{$action} FROM {$table}";
			
			if(!$this->query($sql)->error()){
				return $this;
			}
		} else if (count($where === 1)){
			$order = $where[0];			
			$sql = "{$action} FROM {$table} ORDER BY $order";
					
			if(!$this->query($sql)->error()){
				return $this;
			}
		} 
		else{
			$this->_error = true;
		}
		return $this;
	}
	
	public function get($table, $where = array(), $fields=null){
		if(!$fields){
			return $this->action('SELECT *', $table, $where);
		} else {
			$select = implode(", " , $fields);
			return $this->action("SELECT {$select}", $table, $where);
		}
	}
	
	public function delete($table, $where){
		return $this->action('DELETE', $table, $where);
	}
	
	public function insert($table, $fields = array(), $return = false){
		if(count($fields)){
			$keys = array_keys($fields);
			$values = null;
			$x = 1;
			foreach($fields as $field){
				$values .= '?, ';
			}
			$values = rtrim($values, ', ');
			if(!$return){
				$sql = "INSERT INTO {$table} (" . implode(", " , $keys) . ") VALUES ({$values})";
			} else {
				$sql = "INSERT INTO {$table} (" . implode(", " , $keys) . ") VALUES ({$values}) RETURNING *";
			}
			
			$params = array_values($fields);
			if(!$this->query($sql, $params)->error()){
				return true;	
			}
		}
		return false;		print_r($this->_results);
		print_r($results);
		
	}
	
	
	public function update($table, $idPair = array(), $fields = array()){
		if(count($fields) && count($idPair) === 2){
			$keys = array_keys($fields);
			$set = '';
			foreach($fields as $name => $value){
				$set .= "{$name} = ?, ";					
			}
			$set = rtrim($set, ', ');
			
			$sql = "UPDATE {$table} SET {$set} WHERE {$idPair[0]} = ?";
			
			$binds = array_values($fields);
			$binds[] = $idPair[1];
			if(!$this->query($sql, $binds)->error()){
				return true;	
			}
		}
		return false;
	}
	
	public function getResults($str){
		$results = $this->_results;
		$return = array();
		foreach($results as $result){
			$return[] = $result->{$str};
		}
		return $return;
	}
	
	public function error(){
		return $this->_error;
	}
	
	public function count(){
		return $this->_count;
	}
	
	public function first(){
		$results =  $this->_results;
		return $results[0];
	}
	
	public function results(){
		return $this->_results;
	}
}