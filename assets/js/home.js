(function ($) {

$(function(){
    $("#read-more").click(function(){
        $('.welcome-read-more-text').slideDown(2000);
        $('#read-more').hide();
    });
    
    $("#view-less").click(function(){
            $('.welcome-read-more-text').slideUp(2000); 
            $('#read-more').show();   

    });  
});

})(jQuery);
