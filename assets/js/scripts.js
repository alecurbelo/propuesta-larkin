(function ($) {
    $(function () {
        $("#navbarSupportedContent a").click(function(e){
            $("#navbarSupportedContent").removeClass('show');
            $(".navbar-toggler").attr('aria-expanded', 'false');
        });
    });
})(jQuery);

