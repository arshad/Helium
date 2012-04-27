$(document).ready(function(){
   
   //handles the job toggle button
   $(".toggleJobStatus").click(function(event){
    
    //prevent the default event
    event.preventDefault();
    
    //get the variables
    var job_id = $(this).attr("id");
    
    var status = 1;
    
    if($(this).hasClass("active")){
        status = 0;
    }
    
    if(confirm("Are you sure you want to toggle this job's status?")){            
            //toggle it class to inactive
            $(this).switchClass("active","inactive",1000);
            
            //toggle the class of the anchor
            $(this).parent().prev("a").switchClass("active","inactive",1000).effect("highlight",2000);
    }
    
    //send the ajax request to toggle status in the database
    $.post("?m=ajax/job/toggle",{
        "job_id" : job_id,
        "status" : status
    },function(m){
      if(m){
        $('body').FancyMsg({
          'message' : m
        });
      }
    });
    
   });
   
});


