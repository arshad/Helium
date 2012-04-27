<?php

class Inc{

	var $dir = "";
	var $extension = "";
	
	public function read(){
		$files = glob("".$this->dir."*.".$this->extension."");		
		return $files;
	
	}
	
	public function subRead(){
		return (array_diff(scandir($this->dir),array(".","..")));
	}

}

?>