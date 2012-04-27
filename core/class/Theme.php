<?php
 
class Theme{	
	
	public function renderTheme($theme){
		global $Module;      
		include_once("themes/".$theme."/".$theme.".php");
		$themeClass = ucwords($theme);
		
		$$theme = new $themeClass;
		include_once("themes/".$theme."/".$$theme->front);
		
	}
	
} 



?>