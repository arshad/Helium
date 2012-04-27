$(document).ready(function(){
    $(".view-content:nth-child(2,5,8)").css({
        "marginLeft" : "10px",
        "marginRight" : "10px"
    });
    
    $(".view-content div.job-teaser").css("display","none");
    
    //tooltip implementation
    /* Tooltip */
    $('.tooltip').tooltip({
            bodyHandler: function() {
                    return $(this).siblings("div.job-teaser").html();
                },
            track: true,
            delay: 0,
            showURL: false,
            showBody: " - ",
            fade: 250
    });
    
});