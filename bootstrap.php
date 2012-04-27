<?php
session_start();
include_once("config.php");
include_once("core/class/Inc.php");

//create a new instance of the Inc class
$inc = new Inc;

//set the directory to read and include all classes files
$inc->dir = "core/class/";
$inc->extension="php";

//read the classes files and include them
foreach($inc->read() as $file){
	include_once($file);
}

//create global objects
$Path = new Path;
$Db = Db::getInstance();
$Tool = Tool::getInstance();
$Module = Module::getInstance();
$Settings = new Settings;
$Theme = new Theme;
$Path = new Path;
$moduleScripts = array();

//set the modules directory
$inc->dir = "modules/";

//read each module directory
foreach($inc->subRead() as $file){
    
	$moduleDir = "modules/".$file."/";
	$module="".$file.".php";
        $modulePath = $moduleDir."/".$module;
        
        //check if the module has a ModuleName.php file
	if(file_exists($modulePath)){
		//include the .module file
		include_once($modulePath);
		
		//create a new instance of the class for this module
		if(class_exists($file)){
			$$file = new $file;
        }
                
        //create an array of scripts for each module
        if(file_exists("modules/".$file."/".$file.".js"))
           $moduleScripts[] = "modules/".$file."/".$file.".js";			
	}
        
        //add module custom scripts
        if(sizeof($$file->Scripts)){
          foreach($$file->Scripts as $f){
            $moduleScripts[] = "modules/".$file."/".$f;	
          }		
	}        
        
        //create an array of stylesheets for each module
        if(file_exists("modules/".$file."/".$file.".css")){
           $moduleStyleheets[] = "modules/".$file."/".$file.".css";			
	}
        
        //add module custom stylesheets
        if(sizeof($$file->Stylesheets)){
          foreach($$file->Stylesheets as $f){
            $moduleStyleheets[] = "modules/".$file."/".$f;	
          }
        }
}

//get content for display
//get current module call

$m = $_GET["m"];

if($m!=""){
		
    //load the path array
    $varArray = $Path->loadParameters($m);
    
    //Ajax Implementation
    if($varArray[0]=='ajax' || $varArray[0]=='xml'){
      
      $$m = new $varArray[1];
      
      //remove the ajax/xml string from the uri
      //pass the remaining arguments as an array to the display method of the class
      $varArray = array_slice($varArray,1);
      
      //echo the output for the ajax or xml call      
      if(method_exists($$m,"display")){
          echo $$m->display($varArray);
      }
      
      //do not render any html
      die();
    }
    else{
      //create an instance of this class
      $$m = new $varArray[0];
    
      //check if this module can render output
      //get the output and append it to variable content
      if(method_exists($$m,"display")){
          $content.=$$m->display($varArray);
      }
    }
    
    
}    


//load libraries
$inc->dir = "misc/libraries/";
foreach($inc->subRead() as $file){
    //if $file is a directory, recursively scan this directory for js files
    if(is_dir($inc->dir.$file)){
        //scan directory
        $inc->dir = "misc/libraries/".$file."/";
        foreach($inc->subRead() as $f){
            //check if this $f is a file
            if(is_file($inc->dir.$f))
                //check if it is a js file
                if(substr(strrchr($f, '.'), 1)=="js")
                    $header .= '<script type="text/javascript" src="'.$inc->dir.$f.'" ></script>';
        }    
    }
    else if(is_file($inc->dir.$file))
        if(substr(strrchr($file, '.'), 1)=="js")
            $header .= '<script type="text/javascript" src="'.$inc->dir.$file.'" ></script>';
}

//load scripts
$inc->dir = "misc/";
foreach($inc->subRead() as $file){
    if(is_file($inc->dir.$file))
        if(substr(strrchr($file, '.'), 1)=="js")
            $header .= '<script type="text/javascript" src="'.$inc->dir.$file.'" ></script>';
}

//load module scripts
foreach($moduleScripts as $ms){
    $header .= '<script type="text/javascript" src="'.$ms.'" ></script>';
}

//load library css
$inc->dir = "misc/libraries/";
foreach($inc->subRead() as $file){
    //if $file is a directory, recursively scan this directory for js files
    if(is_dir($inc->dir.$file)){
        //scan directory
        $inc->dir = "misc/libraries/".$file."/";
        foreach($inc->subRead() as $f){
            //check if this $f is a file
            if(is_file($inc->dir.$f))
                //check if it is a js file
                if(substr(strrchr($f, '.'), 1)=="css")
                    $header .= '<link href="'.$inc->dir.$f.'" type="text/css" rel="stylesheet" />';
        }    
    }
    else if(is_file($inc->dir.$file))
        if(substr(strrchr($file, '.'), 1)=="css")   
            $header .= '<link href="'.$inc->dir.$file.'" type="text/css" rel="stylesheet" />';
}

//load module stylesheets
foreach($moduleStyleheets as $ms){
    $header .= '<link href="'.$ms.'" type="text/css" rel="stylesheet" />';
}

//get the current theme
$_theme = $Settings->valueOf("theme");
//include the theme class
$theme_path = "themes/".$_theme;
include_once($theme_path."/".$_theme.".php");
//create an instance of the theme class
$$_theme = new $_theme;

//load the stylesheets
foreach($$_theme->stylesheet as $style){
    $header .= '<link href="'.$theme_path."/".$style.'" type="text/css" rel="stylesheet" />';
}

//load the scripts
foreach($$_theme->scripts as $script){
    $header .= '<script type="text/javascript" src="'.$theme_path."/".$script.'" ></script>';
}

//get the title
if($m!="") $title .= $varArray[1]." | ";
$header .= "<title>".$title._SITE_NAME_."</title>";

$site_name = _SITE_NAME_;

//prepare the message variable
$message = Tool::getMsg();

$footer = "<p>Copyrights &copy; "._SITE_NAME_." ".date("F Y")."</p>";

//redirect to front page
if($_GET["m"]=="" && $_GET["m"]!=_FRONT_)
    Tool::redirect("index.php?m="._FRONT_);

//render the theme    
$Theme->renderTheme($Settings->valueOf("theme"));



?>