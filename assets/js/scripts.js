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

})(jQuery);
