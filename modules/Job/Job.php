<?php

include_once("Industry.php");
include_once("Cv.php");
$Industry = new Industry;
$Cv = new Cv;

class Job{
	
        var $ModuleName = 'Job';
        var $ModuleDescription = 'Provides an interface for users to post jobs.';
        var $Stylesheets = array('job.css');
        var $Scripts = array('dashboard.js');
        
	var $id = "";
	var $title = "";
	
	public function display($path){
		switch($path[1]){
			
			case "post":{
				return $this->postJob();
				break;
			}
			
			case "profile":{
				return $this->profile();
				break;
			}
			
			case "view":{
				return $this->view($path[2]);
				break;
			}
			
			case "apply":{
				return $this->apply($path[2]);
				break;
			}
			
			case "user":{
				return $this->viewUserJobs($path[2]);
			}
			
			case "industry":{
				return $this->viewIndustryJobs($path[2]);
			}
			
			case "toggle":{
				return $this->toggle();
                                break;
			}
                        
                        case "xmljob":{
                                return $this->xmljob($path);
                                break;
                        }
			
			default:{
				return $this->viewJob($path[1]);
			}
			
		}
	}
	
	
	public function menu(){
		Global $User;
		$output .= '<ul id="job-menu">';
		
		$output.= '<li><a href="?m=job/new">New Jobs</a></li>';
		
		if($User->isLoggedIn()){
			$output.= '<li><a href="?m=job/post">Post Job</a></li>';
			$output.= '<li><a href="?m=job/user/'.$_SESSION["user_id"].'">My Jobs</a></li>';			
			$output.= '<li><a href="?m=user/profile">My Profile</a></li>';
		}
		
		$output .= "</ul>";
		
		return $output;
	}
	
	public function postJob(){		
		
		Global $Industry;
		Global $User;
		
		if(isset($_POST["post"])){
			$_POST = array_slice($_POST,0,sizeof($_POST)-1);
			
			foreach($_POST as $field=>$value){
				$$field = $value;
			}
			
			if($title!="" && $teaser!="" && $industry!="" && $description!=""){
				$query = '
				INSERT INTO '._DB_PREFIX_.'job(job_title,job_teaser,job_description)
				VALUES("'.$title.'","'.mysql_escape_string($description).'","'.mysql_escape_string($description).'")
				';
					
				if(Db::execute($query)){
					$job_id = mysql_insert_id();
					
					//insert the job user
					$query = '
						INSERT INTO '._DB_PREFIX_.'user_job(user_id,job_id)
						VALUES('.$User->getUser("user_id").','.$job_id.');
						';
						
						if(Db::execute($query)){
							$valid = true;
						}
						else
							$valid = false;
							
					//insert the job industries
					foreach($industry as $i){
						$query = '
						INSERT INTO '._DB_PREFIX_.'job_industry(job_id,industry_id)
						VALUES('.$job_id.','.$i.')
						';
						
						if(Db::execute($query)){
							$valid = true;
						}
						else
							$valid = false;
					}
					
					if($valid)
						Tool::displayMsg("success","New Job posted successfully");
					
				else
					Tool::displayMsg("error","Job post was not saved");	
					
				}
			}
			else{
				Tool::displayMsg("error","Job post was not saved");
				Tool::displayMsg("error","Required Fields left blank");
			}
			
			
			
		}
		
		$output .= '<h1 class="title">Post a new job</h1>';
		$output .= '<form action="" method="post">
				<label>Title</label>
				<input type="text" name="title" class="text required" />
				<em>Job Title. You are recommended to keep it concise</em>
			';
			
		$output .= $Industry->formElement("Industry","Select industries associated with your job post","checkbox");
				
		$output .= '<label>Teaser</label>
			    <textarea name="teaser" class="textarea"></textarea>
			    <em>Brief description of the job</em>
				
			    <label>Description</label>
			    <textarea name="description" class="textarea"></textarea>
			    <em>Description of the job</em>
			';
		
		$output .= '<input type="submit" name="post" value="save" class="submit button" />
			    <input type="button" value="reset" class="reset button" />				
			';
			
		return $output;	
		
	}
	
	public function profile(){
		
		Global $User;
		Global $Cv;
		
		$output .= '<div id="cv-view">';
		
		$output .= "<h4 class='title'>My Curriculum Vitae</h4>";
		$output .= "<ul>";
		
		foreach($Cv->cvs as $c){
			if($c["user_id"]==$User->getUser("user_id"))
				$output .= '<li><a href="files/cv/'.$c["cv_file_name"].'" target="_blank">'.$c["cv_title"].'</a>.</li>';
		}
		
		$output .= "</ul>";
		
		$output .= $Cv->post();
		
		$output .= "</div>";	
				
		return $output;
	}
	
	public function viewJob($param){
		
		Global $Industry;		
		$output .= '<div class="view job-dashboard">';
		
		switch($param){
			case "new":{
				foreach($Industry->industries as $industry){
					
					$query = '
					SELECT DISTINCT * FROM '._DB_PREFIX_.'job j,'._DB_PREFIX_.'job_industry ji,'._DB_PREFIX_.'user_job uj
					WHERE j.job_id = ji.job_id AND j.job_id = uj.job_id
					AND ji.industry_id='.$industry["industry_id"].'
					AND j.job_status=1
					ORDER BY job_post_date DESC
					LIMIT 5
					'
					;
					
					$result = Db::execute($query);                                        
                                        
					$output .= '<div class="view-content" id="industry-'.$industry["industry_id"].'">';
                                        $output .= '<a href="#" class="update-list">0</a>';
					$output .= '<h4 class="job-title"><a href="?m=job/industry/'.$industry["industry_id"].'">'.$industry["industry_name"].'</a></h4>';
					$output .= '<ul class="job-list">';
					
					while($row = Db::getResultSet()){
						$output .= '<li class="job-title" id="job-'.$row['job_id'].'"><a href="?m=job/view/'.$row["job_id"].'" class="tooltip">'.substr($row["job_title"],0,80).'</a>';
						$output .= '<div class="job-teaser">'.substr(strip_tags($row["job_teaser"]),0,300).'...</div>';
					}
					
					$output .= '</ul>';
				
					$output .= '</div>';
				}		
			}
			
		}
		
		
		$output .= '</div>';
		
		return $output;
	}
	
	
	public function view($id){
		Global $User;
                
                if($this->getJob($id,"job_status")){
                  $output .= '<h2 class="job-title">'.$this->getJob($id,"job_title").'</h4>';
                  $output .= '<div class="job-description">'.$this->getJob($id,"job_description").'</div>';
                  
                  if($User->isLoggedIn()){	
                    $output .= '<p><a href="?m=job/apply/'.$this->getJob($id,"job_id").'" class="application-button submit">Apply</a></p>';
                  } else{
                    $output .= '<p><a href="?m=user/register" class="application-button submit">Register to apply</a></p>';
                  }
                } else {
                  $output .= '<p>Application for this job is closed.</p>';			
                }
		return $output;
	}
	
	
	public function apply($id){
				
		Global $User;
		Global $Cv;
		
		if(isset($_POST["apply"])){
			$_POST = array_slice($_POST,0,sizeof($_POST)-1);
			
			foreach($_POST as $field=>$value){
				$$field = $value;
			}
            
            //print_r($_POST);
			
			if($cvId!=""){
				$job_user = $this->getJob($id,"user_fname")." ".$this->getJob($id,"user_lname");
				
				$to = $job_user.'<'.$this->getJob($id,"user_email").'>';
				$subject = 'New Application for : '.$this->getJob($id,"job_title");
				$msg .= '<p>Hello <strong>'.$job_user.'</strong>, you have a new application for one of your job post.';
				$msg .= '<p><u>Application Details</u></p>';
				$msg = 'Job post : <strong>'.$this->getJob($id,"job_title").'</strong>';
				$msg .= '<p>Applicant : <strong>'.$User->getUser("user_fname").' '.$User->getUser("user_lname").'</strong></p>';
				$msg .= '<p>Message :</p>';
				$msg .= $message;
				$msg .= '<p>You can access the CV at <a href='.$Cv->getCv($cvId,"cv_file_name").'>'.$Cv->getCv($cvId,"cv_file_name").'</a></p>';
								
				//send the email
				if(Tool::sendEmail($to,$subject,$msg))
					Tool::displayMsg("success","Your Application has been sent");
				else
				    Tool::displayMsg("error","Your application was not sent.");
					
			}
			else
				Tool::displayMsg("error","Required fields left blank");
		}
		
		$output .= '<h1 class="title">Application for : '.$this->getJob($id,"job_title").'</h2>';
		
		$output .= '<p><em>'.$this->getJob($id,"job_teaser").'</p></em>';
		
		$output .= '<form action="" method="post">';
		$output .= '<input type="hidden" name="id" value="'.$id.'" class="hidden required" />';
		
		$output .= '<label>Curriculum vitae</label>';
		$output .= '<select name="cvId">';
		foreach($Cv->cvs as $c){
			if($c["user_id"]==$User->getUser("user_id"))
				$output .= '<option value='.$c["cv_id"].'>'.$c["cv_title"].'</option>';
		}			
		$output .= '</select>
			    <em>Select a cv to use for this application</em>
				
			    <label>Message</label>
			    <textarea name="message" class="textarea"></textarea>
			    <em>Type in a message for this application. This message will be sent along with the cv.</em>
			';
		
		$output .= '<input type="submit" name="apply" value="apply" class="submit button" />';
		$output .= '<input type="button" class="reset button" value="reset" />';
		$output .= '</form>';	
		
		return $output;
	}
	
	public function getJob($id,$var){
		$query = '
			SELECT DISTINCT * FROM '._DB_PREFIX_.'job j,'._DB_PREFIX_.'job_industry ji,'._DB_PREFIX_.'user_job uj, '._DB_PREFIX_.'user u
			WHERE j.job_id = ji.job_id AND j.job_id = uj.job_id AND u.user_id = uj.user_id
			AND j.job_id='.$id.'
			LIMIT 1
			';
		
		$result = Db::execute($query);
		$row = Db::getResultSet();
		
		return stripcslashes($row[$var]);
	}
	
	public function viewUserJobs($id){
		Global $Industry;
		
		foreach($Industry->industries as $industry){
			
			$output .= '<div class="view-content">';
			
			$query = '
			SELECT DISTINCT * FROM '._DB_PREFIX_.'job j,'._DB_PREFIX_.'job_industry ji,'._DB_PREFIX_.'user_job uj
			WHERE j.job_id = ji.job_id AND j.job_id = uj.job_id
			AND ji.industry_id='.$industry["industry_id"].'
			AND uj.user_id = '.$_SESSION["user_id"].'
			ORDER BY job_post_date DESC
			';
                        
                        //echo $query;
			
			$result = Db::execute($query);
                                                
			$output .= '<h4 class="job-title"><a href="?m=job/industry/'.$row["industry_id"].'">'.$industry["industry_name"].'</a></h4>';
			$output .= '<ul class="job-list">';
			
			if(Db::numRows() > 0){
				while($row = Db::getResultSet()){
					
					if($row["job_status"]==1)
						$class="active";
					else
						$class="inactive";
					
					$output .= '<li class="job-title"><a href="?m=job/view/'.$row["job_id"].'" class="tooltip '.$class.'">'.substr($row["job_title"],0,80).'</a>';
					$output .= '<span class="status"><a href="#" class="toggleJobStatus '.$class.'" title="Toggle Status" id='.$row["job_id"].'>toggle</a></span>';
					$output .= '<div class="job-teaser">'.substr(strip_tags($row["job_teaser"]),0,300).'...</div>';
					$output .= '</li>';
					
				}
			}
			else
				$output .= '<li>No post in this category</li>';
			
			$output .= '</ul>';
		
			$output .= '</div>';
		}
		
		return $output;
	}
	
	
	public function viewIndustryJobs($id){
		Global $Industry;
			
		$output .= '<div class="view-content">';
		
		$query = '
		SELECT DISTINCT * FROM '._DB_PREFIX_.'job j,'._DB_PREFIX_.'job_industry ji,'._DB_PREFIX_.'user_job uj,'._DB_PREFIX_.'industry i
		WHERE j.job_id = ji.job_id AND j.job_id = uj.job_id AND ji.industry_id = i.industry_id
		AND ji.industry_id='.$id.'
		AND j.job_status=1
		ORDER BY job_post_date DESC
		';
		
		$result = Db::execute($query);
		
		$row = Db::getResultSet();
		$output .= '<h4 class="job-title">Job available in industry : '.$row["industry_name"].'</a></h4>';
		$output .= '<ul class="job-list">';
		
		if(Db::numRows() > 0){
			while($row = Db::getResultSet()){
				$output .= '<li class="job-title"><a href="?m=job/view/'.$row["job_id"].'" class="tooltip">'.substr($row["job_title"],0,80).'</a>';
				$output .= '<div class="job-teaser" style="display:none;">'.substr(strip_tags($row["job_teaser"]),0,300).'...</div>';
				$output .= '</li>';
				
			}
		}
		else
			$output .= '<li>No post in this category</li>';
		
		$output .= '</ul>';
	
		$output .= '</div>';
		
		return $output;
	}
	
	public function toggle(){
		$job_id = $_POST['job_id'];
                $status = $_POST['status'];
                
                $query = '
		UPDATE '._DB_PREFIX_.'job
                SET job_status = '.(int)($status).'
                WHERE job_id = '.$job_id
		;
		
		if(Db::execute($query)){
                  return '<p>Job updated successfully.</p>';
                }
                
                return '<p>Job was not updated.</p>';
                
	}
        
        public function xmljob($path){
          
          $limit = $path[2];
          $order = $path[3];
                    
          $query = '
          SELECT DISTINCT j.job_id,j.job_title, j.job_post_date,j.job_teaser,i.industry_name,i.industry_id,u.user_fname,u.user_lname FROM '._DB_PREFIX_.'job j,'._DB_PREFIX_.'job_industry ji,'._DB_PREFIX_.'user_job uj, '._DB_PREFIX_.'user u,'._DB_PREFIX_.'industry i
          WHERE j.job_id = ji.job_id AND j.job_id = uj.job_id AND uj.user_id = u.user_id AND ji.industry_id = i.industry_id
          AND j.job_status=1
          ';
          
          if($order == 'random'){
            $query .= 'ORDER BY RAND()';
          }
          else{
            $query .= ' ORDER BY job_post_date DESC';
          }
          
          if($limit){
            $query .= ' LIMIT '.$limit;
          }
          
          //return $query;
          
          $result = Db::execute($query);
          
          header("content-type: text/xml");
          $output .= '<?xml version="1.0" encoding="UTF-8"?>';
          $output .= '<jobs>';
          
          $jobs = array();
          
          while($row = Db::getResultSet()){
            array_push($jobs,$row);
          }
                    
          foreach($jobs as $job){
            $output .= '<job>';
            foreach($job as $j=>$val){
              $val = strip_tags($val);
              $output .= '<'.$j.'>'.$val.'</'.$j.'>';
            }
            $output .= '<url>'._SITE_URL_.'?m=job/view/'.$job['job_id'].'</url>';
            $output .= '</job>';
          }
          
          $output .= '</jobs>';
          
          return $output;
        }
	

}
?>