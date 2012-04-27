//loads tinymce
tinyMCE.init({
	// General options
	mode : "textareas",
	theme : "advanced",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
        theme_advanced_resize_horizontal : false,


	// Office example CSS
	content_css : "css/office.css"
});

$(document).ready(function(){
    
	$("input.reset").click(function(){
		if(confirm("Are you sure you want to reset the form?")){			
			$("form").find("input.text").each(function(){
				$(this).val("");
			});
			
			$("form").find("textarea").each(function(){
				$(this).html("");
			});
		}
				
	});
    
    
    //set class active-menu-item to active anchor
    $('a').each(function(){
       var href = $(this).attr('href');
        if(href==window.location.search)
            $(this).addClass('active-menu-item')        
    });
    	
});