(function($) {
  $.fn.FancyMsg = function(options) {

    var opts = $.extend({}, $.fn.FancyMsg.defaults, options);
    
    $.fn.FancyMsg.defaults = {
      //animate : false
    }

    return this.each(function() {
      var MessageBox = $('<div></div>')
                        .addClass('MessageBox')
                        .addClass('ui-corner-all')
                        .html(opts.message);
                        
      $('body').append(MessageBox);
      
      MessageBox.slideDown('slow');
      
      $('body').click(function(){
        MessageBox.slideUp('slow',function(){
          $(this).remove();
        });
      });
    });

  }  
})(jQuery);