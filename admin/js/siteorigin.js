(function($){
    $(document).on('panelsopen', function(e){
        var dialog = $(e.target);
        if ( !dialog.has('.so-panels-dialog-wrapper') ) return;
        $('.rawp-fields').hide();
    });
})(jQuery);