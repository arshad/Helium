<?php

class Cv{
    
    var $cvs = array();
    
    function __construct(){
        $query = '
		SELECT * FROM '._DB_PREFIX_.'cv c,'._DB_PREFIX_.'user_cv uc
		WHERE c.cv_id = uc.cv_id
		';
			
		$result = Db::execute($query) or Db::getError();
		
		if(Db::numRows()>0){
		    while($row = Db::getResultSet()){
			if($row["cv_title"]!="")
			    $this->cvs[] = array(
						  "cv_id" => $row["cv_id"],
						  "cv_title" => $row["cv_title"],
                                                  "cv_file_name" => $row["cv_file_name"],
                                                  "user_id" => $row["user_id"]
					    );
		    }
		}
    }
    
    function post(){
        
        if(isset($_POST["upload"])){
                $target_path = "files/cv/".basename( $_FILES['cv']['name']); 
                
                if(move_uploaded_file($_FILES['cv']['tmp_name'], $target_path)){
                    
                    $_POST = array_slice($_POST,0,sizeof($_POST)-1);
			
                    foreach($_POST as $field=>$value){
                            $$field = $value;
                    }
			
                    if($title!=""){
                        
                        $query = '
                        INSERT INTO '._DB_PREFIX_.'cv(cv_title,cv_file_name)
                        VALUES("'.$title.'","'.basename( $_FILES['cv']['name']).'")
                        ';
                                                
                        if(Db::execute($query)){
                            $cv_id = mysql_insert_id();
                            
                            $query = '
                            INSERT INTO '._DB_PREFIX_.'user_cv(cv_id,user_id)
                            VALUES("'.$cv_id.'","'.$_SESSION["user_id"].'")
                            ';
                            
                            if(Db::execute($query)){
                                Tool::displayMsg("success","Your Curriculum vitae has been successfully uploaded");
                            }                               
                            
                        }
                        
                        
                    }
                } else{
                    Tool::displayMsg("error","Your file has not been uploaded. Please try again. !");
                }
        }
        
        $output .= '<h4 class="title">Upload a new CV</h4>';
        
        $output .= '<form action="" method="post" enctype="multipart/form-data" id="form-upload-cv" >
                    <input type="hidden" name="MAX_FILE_SIZE" value="100000" class="hidden" />
                    
                    <label>Title</label>
                    <input type="text" name="title" class="text" />
                    <em>Title for this cv</em>
                    
                    <label>File</label>
                    <input type="file" name="cv" />
                    <em>Upload a new cv</em>';
        
        $output .= '<input type="submit" name="upload" value="upload" class="submit button" />';
        $output .= '</form>';
        
        return $output;            
        
    }
    
    public function getCv($id,$var){
        $query = '
                SELECT * FROM '._DB_PREFIX_.'cv c,'._DB_PREFIX_.'user_cv uc
                WHERE c.cv_id = uc.cv_id
                AND c.cv_id = '.$id
                ;     
                
        $result = Db::execute($query);
        
        $row = Db::getResultSet();
        
        if($var = "cv_file_name")
            return _SITE_URL_."/files/cv/".$row[$var];
        
        return $row[$var];
    }
    
}


?>