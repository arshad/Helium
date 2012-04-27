<?php
 
class Settings{
	
	public function valueOf($var){
		
		$query = '
		SELECT * FROM '._DB_PREFIX_.'settings
                WHERE settings_variable = "'.$var.'"
		';
		
		$result = Db::execute($query) or Db::getError();
		
		if(Db::numRows()>0){
				$row = Db::getResultSet();
				return $row["settings_value"];
		}
		
	}
	
} 


?>