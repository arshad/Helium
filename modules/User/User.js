$(document).ready(function(){
  
  //ajaxify the user profile form
  $('#user-profile-form').submit(function(){
    var fname = $('input[name=fname]').val();
    var lname = $('input[name=lname]').val();
    var email = $('input[name=email]').val();
    var password = $('input[name=password]').val();
    
    if(fname!='' || lname!='' || email!=''){
        $.post("?m=ajax/user/updateProfile",{
          "save" : true,
          "fname" : fname,
          "lname" : lname,
          "email" : email,
          "password" : password
      },function(m){
        if(m){
          $('body').FancyMsg({
            'message' : m
          });
        }
      });
      
      return false;  
        
    }
    
    return true;
    
  });
  
});