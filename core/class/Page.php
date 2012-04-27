<?php

class Page{
	
	var $tab = "";
	var $m="";
	
	public function display(){
		if($this->tab!=""){
			$t = ucwords($this->tab);
			global $$t;
			$$t->display();
		}
	}
	
	public function sidebar(){
		if($this->tab!="" && $this->m!=""){
			$t = "Admin".ucwords($this->m);
			global $$t;
			if(method_exists($$t,"displaySidebar"))
				$$t->displaySidebar();
		}
		
		if($this->tab!=""){
			$t = ucwords($this->tab);
			global $$t;
			if(method_exists($$t,"displaySidebar"))
				$$t->displaySidebar();
		}
		
	}
	
}

?>