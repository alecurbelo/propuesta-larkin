(function ($) {
    $(function () {
        $("#navbarSupportedContent a:not(.dropdown-toggle),.close-menu-wrapper").click(function(e){
            $("#navbarSupportedContent").removeClass('show');
            $(".navbar-toggler").attr('aria-expanded', 'false');
            $('.movil-over-color').fadeOut();
        });

        $(".navbar-toggler").click(function(){
            $('.movil-over-color').fadeIn();
        });

    });
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

$(function(){
    $('.services').click(function(){
        $('html, body').animate({ scrollTop: $("#larkin-services").offset().top}, 800);
    }); 
});

})(jQuery);