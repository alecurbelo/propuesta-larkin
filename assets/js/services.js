(function ($) {

$(function(){
    $('.services').click(function(){
        $('html, body').animate({ scrollTop: $("#larkin-services").offset().top}, 800);
    }); 
});

})(jQuery);
