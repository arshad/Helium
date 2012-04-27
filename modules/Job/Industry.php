<?php
class Industry{
    
    var $industries = array();
    
    function __construct(){
        $query = '
		SELECT * FROM '._DB_PREFIX_.'industry
		WHERE industry_status = 1
		';
			
		$result = Db::execute($query) or Db::getError();
		
		if(Db::numRows()>0){
		    while($row = Db::getResultSet()){
			if($row["industry_name"]!="")
			    $this->industries[] = array(
						  "industry_id" => $row["industry_id"],
						  "industry_name" => $row["industry_name"]
					    );
		    }
		}	
    }
    
    public function menu(){
	
    	$output .= '<ul id="industry-menu">';
    		
    	foreach($this->industries as $industry){
    	    $output.= '<li><a href="?m=job/industry/'.$industry["industry_id"].'">'.$industry["industry_name"].'</a></li>';
    	}
    	
    	$output .= "</ul>";
    	
    	return $output;
        }
        
        public function formElement($label="",$em="",$type=""){
    	
    	switch($type){
    	    case "checkbox":{		
    		$output .= '<label>'.$label.'</label>';
    		foreach($this->industries as $industry){
    			if($industry["industry_name"]!="")
    			    $output .= '<input type="checkbox" name="industry[]" class="checkbox" value='.$industry["industry_id"].' /> '.$industry["industry_name"];
    		}
    		$output .= '<em>'.$em.'</em>';
    	    }
    		
    	}
    	
    	return $output;
	
    }
    
}
?>