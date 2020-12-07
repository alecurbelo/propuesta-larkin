(function ($) {
    $(function () {
        $("#navbarSupportedContent a,.close-menu-icon").click(function(e){
            alert('dsd');
            $("#navbarSupportedContent").removeClass('show');
            $(".navbar-toggler").attr('aria-expanded', 'false');
        });


    });
})(jQuery);

