(function ($) {

    $(document).ready(function() {
        $('#prueba').each(function(){
            $(this).find('table').DataTable({
                "paging":   true,
                "ordering": true,
                "info":     true
            } );
        } );
    } );
} );