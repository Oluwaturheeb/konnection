<?php

class Action{
	private $_db, $_check = false, $_error = null;
	public $data, $login = false;
	
	public function __construct(){
		$this->_db = Db::instance();
	}
	
	public function create($table, $fields = array()){
		if(count($fields)){
			$query = $this->_db->insert($table, $fields);
			if($this->_db->error()){
				$this->_error = $this->_db->error();
			}else{
				$this->_check = true;
			}
		}
		return false;
	}
	
	public function id(){
		return $this->_db->lastId();
	}
	
	public function user_profile($table, $id = 0, $arr = []) {
	    if(empty($arr)){
	        $arr = ["*"];
	    }
	    $this->_db->colSelect($table, $arr, [["id", "=", $id]]);
	    
	    $this->data = $this->_db->first();
	}
	
	public function counter($table, $id){
	    $this->_db->colSelect($table, array("views"), [["id", "=", $id]]);
	    $this->update($table, ["views" => $this->_db->first()->views + 1], $id);
	}
	
    /*
    And this particular login function workd with selecting from 2 tables
    
    [
    table -- 
    
    ]
    */
    
    
    public function login ($table, $col = [], $credit = [],  $sess = "") {
    	   if(!is_array($credit[0])) {
    	   	$credit = [
    	   		["email", "=", $credit[0]],
    	   		["password", "=", $credit[1]]
    	   	];
    	   }
        if(!is_array($table)) {
            $this->_db->colSelect($table, $col, $credit);
            
            if($this->_db->error()){
                $this->_error = $this->_db->error();
            }else{
            	if($this->_db->count()) {
            		$this->login = true;
            		$this->data = $this->_db->first();
            		if($sess) {
            			if($this->id()){
            				Session::set($sess, $this->id());
            			}else{
            				Session::set($sess, $this->data->id);
            			}
           	 	}
           	}
          }
        } else {
            foreach ($table as $num => $tab){
                $this->_db->colSelect($tab, $col, $credit);
            
                if($this->_db->error()){
                    $this->_error = $this->_db->error();
                    break;
                }else{
                    if($this->_db->count() == 1){
                        Session::set($sess, $this->id());
                        $this->data = ["id" => $this->_db->first(), "key" => $num +1];
                        break;
                    }
                }
            }
        }
    }
	
	/*
	Action update usage 
	
	$table -> Any table name
	$set -> column to update. An array of key/value pair
	array("name" => "muhammad-turyeeb", "author" => "bello")
	$user -> this can be really triock but not a simple explainastion cannot handle 
	
	1. the default is to update using id so the user of the function is to provide only the of the column to update
	
	2. but if the user of this function have any other column to use instead of the id, the user should provide an array of the the column and the value in a single array e.g
	array("some column name", "value to use");
	
	3. lastly if the user have to update the database based on using 2 or more where clause the user is require to use a multi-dimensional array example
	
	array(array("first column", "comparison", "value"), array("second column", "comparison", "value"));
	
	And at the user will have 
	
	$a = new Action();
	
	1. $a->update("table", ["column" => "value"], 1);
	
	2. $a->update("table", ["column" => "value"], ["some other column apart from id", "value"]);
	
	3. $a->update("table", ["column" => "value"], [["first column", "comparison", "value"],["second column", "comparison", "value"]]);
	
	*/
	
	public function update($table, $set = array(), $user = 0, $con = ["and"]){
		if(is_array($user)){
			if(is_array($user[0])){
				$user = $user;
			} else {
				$col = $user[0];
				$val = $user[1];
				$user = [[$col, "=", $val]];
			}
		}  else {
			$col = "id";
			$val = $user;
			$user = [[$col, "=", $user]];
		}
		
		$this->_db->dbUpdate($table, $set, $user, $con);
		if(!$this->_db->error()){
			if ($this->_db->count()) {
		    	$this->_check = true;
		    } else {
		    	$this->_error = "error updating";
		    }
		}else {
		    $this->_error = $this->_db->error();
		}
	}
	
	public function delete($table, $user = 0, $con = "and"){
		if(is_array($user)){
			if(is_array($user[0])){
				$user = $user;
			} else {
				$col = $user[0];
				$val = $user[1];
				$user = [[$col, "=", $val]];
			}
		}  else {
			$col = "id";
			$val = $user;
			$user = [[$col, "=", $user]];
		}
		
		$this->_db->delete($table, $user, $con);
		if(!$this->_db->error()){
			$this->_check = true;
		}else {
		    $this->_error = $this->_db->error();
		}
	}
	
	public function send_mail($rep, $sub, $content){
		$headers = "MIME-Version: 1.0 \r\n";
		$headers .= "Content-Type: text/html \r\n";
		$headers .= "no-reply@halqah.ga\r\n";
		
		
		$mail = mail($rep, $sub, $content, $headers);
		
		if($mail){
			$this->_check = true;
		}else{
			$this->_error = $mail;
		}
	}
	
	public function logout($user = ""){
	    if(!$user){
	        Session::del();
	    }else{
	    	   Session::del($user);
	    }
	}
	
	public function check(){
		return $this->_check;
	}
	
	public function error(){
		return $this->_error;
	}
}