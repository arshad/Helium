<?php

class Module{
	var $name = "";
	var $displayName = "";
	var $anchor = "";
	var $parent = "";
	
	//singleton implementation
	private static $instance; 
	public static function getInstance(){
	    if (!self::$instance){
	        self::$instance = new Module();
	    }
	    return self::$instance;
	}
	
	public function display(){
		
		//if the tab is module and no module selected
		if(isset($_GET["tab"]) && !isset($_GET["m"]))
			$this->getAll();
		
		//if the tab is module and a module is selected called the display method for this module
		if(isset($_GET["tab"]) && isset($_GET["m"])){
			$m = "Admin".ucwords($_GET["m"]);
			$$m =  new $m;
			
			if(isset($_GET["task"])){
				$task = $_GET["task"];
				if($_GET["task"]=="install"){
					if(method_exists($$m,"install")){
						$$m->install();
					}
					$this->install($_GET["m"]);
				}	
				else	
					$$m->$task();
			}
			else
				$$m->display();
		}
		
	}
	
	public function displaySidebar(){
	}
	
    /*
	public function getAll(){
		$inc = new Inc;
		$output="<p>This is the list of <b>modules</b> currently <b>available</b> in your <b>module directory</b></p>";
		$output.="<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam</p>";	
		$install.="<div class='other'><p>This is a list of <b>modules</b> installed in your <b>system</b>. Click on any module to access it.</p>";
		$notInstall.="<div class='other'><p>This is a list of <b>modules</b> that are <b>not</b> installed in your <b>system</b>. Learn how to install them by clicking <a href='#'>here</a></p>";
			
		//set the modules directory
		$inc->dir = "../modules/";
		//read each module directory
		foreach($inc->subRead() as $file){
			$moduleDir = "../modules/".$file."/";
			$module=""."Admin".$file.".php";
			$moduleFile = $moduleDir.$module;
			//check if the module has a proper .module file
			if(file_exists($moduleFile)){
				//include the .module file
				include_once($moduleFile);
				
				$moduleName = $file;
				
				//get the Admin class
				$file = "Admin".$file;
				
				//create a new instance of that class
				if(class_exists($file))
					$$file = new $file;
				
				$icon = $moduleDir.$$file->icon;
				//if there is no icon set, display the default module icon
				if($$file->icon=="")
					$icon = "../images/icons/module.png";	
					
				if($this->_isInstalled($moduleName)){	
					$_install.="<div class='moduleList ui-corner-all ui-widget-content tooltip' >
									 <img src='".$icon."' align='center'/><br />
									 <a href='?tab=module&m=".$$file->name."' class='ui-corner-all'>".$$file->displayName."</a></div";
				}
				else{
					$_notInstall.="<div class='moduleList ui-corner-all ui-widget-content tooltip' >
									 <img src='".$icon."' align='center'/><br />
									 <a href='?tab=module&m=".$$file->name."&task=install' class='ui-corner-all'>".$$file->displayName."</a></div";
				}
			}
		}
		
		if($_install=="") $_install="<p><em>No module installed.</em></p>";
		
		if($_notInstall=="") $_notInstall="<p><em>No new module.</em></p>";
		
		$install.=$_install."</div>";
		$notInstall.=$_notInstall."</div>";
				
		$output.=$install.$notInstall;
					
		echo $output;
	}
    */
	
	private function _isInstalled($module_name){
		$query = '
		SELECT * FROM '._DB_PREFIX_.'module
		WHERE module_name="'.mysql_real_escape_string($module_name).'"
		LIMIT 1
		';
			
		$result = Db::execute($query) or Db::getError();
		
		if(Db::numRows()>0){
				return true;
		}
				
		return false;	
	}
	
	public function render($module,$method=""){
		$$module = new $module;
		
		if($method!="")
			return $$module->$method();
			
		return $$module->display();
	}
	
	//install method for this class
	public function install($module){
		$query = '
		INSERT INTO '._DB_PREFIX_.'module(module_name)
		VALUES("'.$module.'")
		';
			
		if(Db::execute($query))
				Tool::displayMsg("success","Module <b>".$module."</b> successfully installed.");
		else
				Tool::displayMsg("error","Module <b>".$module."</b> not installed.");
	}
	
	public function hook($method){
		$inc = new Inc;
		$inc->dir = "modules/";
		//read each module directory
		foreach($inc->subRead() as $file){
			$moduleDir = "modules/".$file."/";
			$module = $file.".php";
			$moduleFile = $moduleDir.$module;
			
			//check if the module has a proper .module file
			if(file_exists($moduleFile)){
				
				$$file = new $file;
				
				if(method_exists($file,$method))
					return $$file->$method();
			}
		}
	}
	
}

?>