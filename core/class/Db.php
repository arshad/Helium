<?php

class Db{
	
	var $host = "";
	var $database = "";
	var $user = "";
	var $password = "";
	var $type = "";
	var $result = "";
	var $link = "";
	
	//singleton implementation
	private static $instance; 
	public static function getInstance(){
	    if (!self::$instance){
	        self::$instance = new Db();
	    }
	    return self::$instance;
	}
	
	private function __construct(){
		$this->host = _DB_SERVER_;
		$this->user = _DB_USER_;
		$this->password = _DB_PASSWD_;
		$this->type = _DB_TYPE_;
		$this->database = _DB_NAME_;
		$this->_connect();
	}
	
	public function __destruct() {
		mysql_close($this->link);
   	}
	
	
	/**
	 * Open a connection
	 */
	private function _connect(){
			$this->link = @mysql_connect($this->host,$this->user,$this->password) or die(mysql_error());
			mysql_select_db($this->database,$this->link) or die(mysql_error());
			
			return $this->link;	
	}
	
	/**
	 * Executes a query
	 */	 
 	public function	execute($query)
	{
		$this->result = mysql_query($query);
		return $this->result;
	}
	
	public function executes($query){
		return mysql_query($query);
	}
	
	public function numRows(){
		return mysql_num_rows($this->result);			
	}
	
	public function getResultSet(){
		return mysql_fetch_assoc($this->result);	
	}
	
	public function getResultArray(){
		return mysql_fetch_array($this->result);	
	}
	
	public function getError(){
		return mysql_error();
	}
}

?>