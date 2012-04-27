<?php

/**
 * @author arshad
 * @copyright 2009
 */

class Tool{
	
	var $msgClass = "";
	var $msgValue = "";
	public static $msg = "";
	
	//singleton implementation
	private static $instance; 
	public static function getInstance(){
	    if (!self::$instance){
	        self::$instance = new Tool();
	    }
	    return self::$instance;
	}
	
	
	public function redirect($to){
		header("location:$to");
	}
	
	public function displayMsg($class,$value){
		if($value!="")
			self::$msg .= "<p class='response-msg ".$class." ui-corner-all'>".$value."</p>";
	}
	
	public function getMsg(){
		if(self::$msg!="")
			return '<div id="message" class="ui-corner-all">'.self::$msg.'</div>';
		
		return "";
	}
	
	public function getURL(){
		return $_SERVER['REQUEST_URI'];
	}
	
    
	public function sendEmail($to,$subject,$message){
	    
	    $from = _EMAIL_;
	    
	    $headers  = 'MIME-Version: 1.0' . "\r\n";
	    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	    $headers .= 'To: '.$to. "\r\n";
	    $headers .= 'From: '.$from. "\r\n";
	    
	    //send the email
	    if(mail($to, $subject, $message, $headers))
		return true;
	    
	    return false;
	    
	} 
	
}


?>