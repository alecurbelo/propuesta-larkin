(function ($) {
    $(function () {
        $("#navbarSupportedContent a").click(function(e){
            $("#navbarSupportedContent").removeClass('show');
            $(".navbar-toggler").attr('aria-expanded', 'false');
        });
        $(".close-menu-wrapper").click(function(e){
            e.preventDefault();
            $("#navbarSupportedContent").removeClass('show');
            $(".navbar-toggler").attr('aria-expanded', 'false');
        });

    });
})(jQuery);

