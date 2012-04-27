$(document).ready(function(){
   
   $('form').submit(function(){
    
    var valid = false;
    var msg = '';
    
    $('input.required').each(function(){
        if(validateBlank($(this).val())){
            if($(this).hasClass('email'))
                if(validateEmail($(this).val())){
                    valid = true;
                    $(this).removeClass('invalid');
                }
                else{
                    $(this).addClass('invalid');
                    msg += 'Email not valid.<br />';
                    valid = false;
                }
            else{
                valid = true;
                $(this).removeClass('invalid');
            }
        }
        else{
            $(this).addClass('invalid');
            var label = $(this).prev('label').html();
            msg += 'Field '+label+' left blank.<br />';
            valid = false;
        }            
    });
    
    if(!valid){
        if($('.validation-msg').length)
            $('.validation-msg').remove();
            
        $(this)
            .before('<div class="validation-msg">'+msg+'</div>')
            .parents('body')
            .find('.validation-msg')
            .hide()
            .slideDown('slow')
            .css({
                'padding' : '5px',
                'font-weight' : 'bold',
                'color' : '#FFFFFF'
            })
            .animate({
              'backgroundColor' : '#CC0000',
            },1000);
            
        return false;
    }
    
    return valid;
    
   });
    
});

function validateBlank(str){
    if(str=='')
        return false;
    return true;
}

function validateEmail(str){
    if(str.indexOf('@')==-1 || str.indexOf('.')==-1)
        return false;
    return true;
}