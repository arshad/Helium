<?php
 
class User{
	
	var $id = "";
	var $fname = "";
	var $lname = "";
	var $email = "";
	var $password = "";
	var $status = "";
	
	
	public function display($path){
		$task = $path[1];		
		return $this->$task();
	}
	
	
	public function register(){
		
		if(isset($_POST["register"])){
			
			$_POST = array_slice($_POST,0,sizeof($_POST)-1);
			
			foreach($_POST as $field=>$value){
				$$field = $value;
			}
			
			$password = $this->_generateRandomPassword();
			
			$query = '
				INSERT INTO '._DB_PREFIX_.'user(user_fname,user_lname,user_email,user_password)
				VALUES("'.$fname.'","'.$lname.'","'.$email.'","'.md5($password).'")
				';
				
			if(Db::execute($query)){
				$output .= Tool::displayMsg("success","You have been successfully registered");
				
				if($this->_sendRegistrationEmail($fname." ".$lname,$email,$password))
					Tool::displayMsg("success","An email has been sent to your email account with a password");
			}
			else
				$output .= Tool::displayMsg("error","Registration Failed");
			
		}		
		
		$output .= "<h2 class='title'>Create a new account</h2>";
		
		$output.='<form action="" method="POST" id="form-user-registration">
				<label>First Name</label>
				<input type="text" name="fname" class="text required"/>
				<em>Enter your first name</em>
				
				<label>Last Name</label>
				<input type="text" name="lname" class="text required"/>
				<em>Enter your last name</em>
			    
				<label>Email</label>
				<input type="text" name="email" class="text required email"/>
				<em>Enter your email address</em>
			    
				<input type="submit" name="register" value="submit" class="submit button"/>
				<input type="button" value="reset" class="reset button" />
			    </form>';
			    
		return $output;
	}
	
	public function login(){
		
		if(isset($_POST["login"])){
			
			$_POST = array_slice($_POST,0,sizeof($_POST)-1);
			
			foreach($_POST as $field=>$value){
				$$field = $value;
			}
			
			$query = '
				SELECT * FROM '._DB_PREFIX_.'user
				WHERE user_email="'.$email.'" AND user_password = "'.md5($password).'"
				LIMIT 1
				';
											
			$result = Db::execute($query);
						
			if(Db::numRows() > 0){
				session_start();				
				$row = Db::getResultSet();				
				$_SESSION["email"] = $email;
				$_SESSION["user_id"] = $row['user_id'];
				$_SESSION["user"] = $fname." ".$lname;
				$_SESSION["passphrase"] = _SESSION_PASSPHRASE_;
				Tool::redirect("index.php");
			}
			else{
				$output .= Tool::displayMsg("error","Wrong email or password");
			}
			
			
		}
		
		$output .= "<h2 class='title'>User Login</h2>";
		
		$output .= '<form action="" method="POST" id="form-user-login">
					<label>Email</label>
					<input type="text" name="email" class="text required email"/>
					<em>Enter your email address</em>
					
					<label>Password</label></td>
					<input type="password" name="password" class="text required"/>
					<em>Type in your password</em>
					
					<input type="submit" name="login" value="login" class="submit button" />
					<input type="button" value="reset" class="reset button" />
				</form>';
		return $output;		
	}
	
	
	public function logout(){
		session_unset($_SESSION["email"]);
		session_unset($_SESSION["passphrase"]);
		
		session_unregister($_SESSION["email"]);
		session_unregister($_SESSION["passphrase"]);
		
		session_destroy();
		
		Tool::redirect("index.php");
		//Tool::displayMsg("success","You have been logged out");		
	}
	
	public function isLoggedIn(){
		
		if(isset($_SESSION["email"]) && isset($_SESSION["passphrase"]) && $_SESSION["passphrase"] == _SESSION_PASSPHRASE_)
			return true;
		
		else return false;
	}

	
	private function _generateRandomPassword($length=8){
		
		//possible values in password
		$set = "abcdefghijklmnopqrstuvwxyz0123456789";
		
		for($i=0;$i<$length;$i++){
			$password.=substr($set,rand(0,strlen($set)),1);
		}
		
		return $password;
	}
	
	private function _sendRegistrationEmail($name,$email,$password){
		
		$to = $name.'<'.$email.'>';
		$subject = 'New Account created!';
		$message .= '<p>Hello <strong>'.$name.'</strong>, welcome to the community';
		$message .= '<p>Account Details : </p>';
		$message .= '<p>Email : <strong>'.$email.'</strong></p>';
		$message .= '<p>Password : <strong>'.$password.'</strong></p>';
		
		
		//send the email
		if(Tool::sendEmail($to,$subject,$message))
			return true;
		
		return false;
	}
	
	
	public function menu(){
		$output .= '<ul id="user-menu">';
		
		if($this->isLoggedIn()){
			$output.='<li>'.$_SESSION["user"].'</li>';
			$output.= '<li><a href="?m=user/logout">Logout</a></li>';
		}
		else{
			$output.= '<li><a href="?m=user/register">Sign Up</a></li>
			      <li><a href="?m=user/login">Login</a></li>
			';
		}
		
		$output .= "</ul>";
		
		return $output;
	}
	
	public function getUser($var){
		$query = '
			SELECT * FROM '._DB_PREFIX_.'user
			WHERE user_id="'.$_SESSION["user_id"].'"
			LIMIT 1
			';
							
		$result = Db::execute($query);
		
		$row = Db::getResultSet();
		
		return $row[$var];
	}
	
	public function profile(){
		Global $Module;
		
		$output .= '<h1 class="title">'.$this->getUser("user_fname").' '.$this->getUser("user_lname").' - Account </h1>';
		
		$output .= '<form action="" method="post" id="user-profile-form">
		
			    <label>First Name</label>
			    <input type="text" name="fname" value="'.$this->getUser("user_fname").'" class="required" />
			    <em>Your first name</em>
			    
			    <label>Last Name</label>
			    <input type="text" name="lname" value="'.$this->getUser("user_lname").'" class="required" />
			    <em>Your last name</em>
			    
			    <label>Email</label>
			    <input type="text" name="email" value="'.$this->getUser("user_email").'" class="required email" />
			    <em>Your email address</em>
			    
			    <label>Password</label>
			    <input type="password" name="password" value="" />
			    <em>Enter a new password here if you want to change password. Leave blank otherwise.</em>
			    
			    <input type="submit" name="save" value="save" class="submit button" />
			    
			    </form>
		';
		
		
		$output .= $Module->hook("profile");
		
		return $output;
	}
	
	public function updateProfile(){
		if(isset($_POST["save"])){
			
			//$_POST = array_slice($_POST,0,sizeof($_POST)-1);
			
			foreach($_POST as $field=>$value){
				$$field = $value;
			}
			
			$query = '
				UPDATE '._DB_PREFIX_.'user
				SET user_fname = "'.$fname.'",
				user_lname = "'.$lname.'",
				user_email = "'.$email.'"
			';
			
			if($password!="")
			$query .= ' ,user_password = "'.md5($password).'"';
			
			$query .= ' WHERE user_id = '.$_SESSION["user_id"];
			
			if(Db::execute($query)){
				return 'Your profile has been updated.';
			}
			else
				return 'Your profile was not saved';
			
		}
	}
	
	
} 



?>