$(document).ready(function(){
  
  //fetch a new job
  //initialise to an id that is already displayed so that we do not get duplicate on first run
  if($('.job-dashboard').length){
    var id = $('#industry-1 li').eq(0).attr('id').substring($('#industry-1 li').eq(0).attr('id').indexOf('-')+1,$('#industry-1 li').eq(0).attr('id').length-1);
    fetch(id);
  }
  
});

function fetch(id){
  if (window.XMLHttpRequest){
    xhttp=new XMLHttpRequest()
  } else {
    xhttp=new ActiveXObject("Microsoft.XMLHTTP")
  }
  
  xhttp.open("GET","?m=xml/job/xmljob/1/random",false);
  xhttp.send("");
  xmlDoc=xhttp.responseXML;
  
  var job_id = xmlDoc.getElementsByTagName("job_id")[0].childNodes[0].nodeValue;
  var title = xmlDoc.getElementsByTagName("job_title")[0].childNodes[0].nodeValue;
  var post_date = xmlDoc.getElementsByTagName("job_post_date")[0].childNodes[0].nodeValue.substr(0,xmlDoc.getElementsByTagName("job_post_date")[0].childNodes[0].nodeValue.indexOf(' '));
  var author = xmlDoc.getElementsByTagName("user_fname")[0].childNodes[0].nodeValue+' '+xmlDoc.getElementsByTagName("user_lname")[0].childNodes[0].nodeValue;
  var industry = xmlDoc.getElementsByTagName("industry_name")[0].childNodes[0].nodeValue;
  var url = xmlDoc.getElementsByTagName("url")[0].childNodes[0].nodeValue;
  var teaser = xmlDoc.getElementsByTagName("job_teaser")[0].childNodes[0].nodeValue;
  var industry_id = xmlDoc.getElementsByTagName("industry_id")[0].childNodes[0].nodeValue;
  
  
  
  //check if this job is not already in the list
  if(id!=job_id && !$('#job-'+job_id).length){
    var output = '<a class="tooltip" href="'+url+'">'+title+'</a>';  
    output += '<div class="job-teaser" style="display:none;">'+teaser+'</div>';
    
    var newRow = $('<li class="job-title hidden" id="job-'+id+'"></li>').html(output).hide();
    
    newRow.find('.tooltip').tooltip({
              bodyHandler: function() {
                      return $(this).siblings("div.job-teaser").html().substr(0,200);
                  },
              track: true,
              delay: 0,
              showURL: false,
              showBody: " - ",
              fade: 250
    });
    
    if(Number($('#industry-'+industry_id+' .update-list').html())<=5){
      $('#industry-'+industry_id+' ul.job-list').prepend(newRow);
    }
    /*
    $('#industry-'+industry_id+' ul.job-list li:last-child').slideUp('slow',function(){
      $(this).remove();
    });
    */
    
    
    $('#industry-'+industry_id+' .update-list')
        .html(Number($('#industry-'+industry_id+' .update-list').html())+1)
        .addClass('active')
        .effect('pulsate',1000)
        .click(function(e){          
          e.preventDefault();
          var rowNo = Number($('#industry-'+industry_id+' .update-list').html());
          if(rowNo!=0){
            $('#industry-'+industry_id+' ul.job-list li').slice($('#industry-'+industry_id+' ul.job-list li').length-rowNo,$('#industry-'+industry_id+' ul.job-list li').length).fadeOut('slow',function(){
                  $(this).remove();
                  $('#industry-'+industry_id+' ul.job-list .hidden').slideDown('slow');
            });            
            $(this).removeClass('active');
            $(this).html('0');
          }
        });
    
    
  }
  
  setTimeout("fetch("+job_id+")",5000);
  
}