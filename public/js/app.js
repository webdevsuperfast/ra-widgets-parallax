(function($){
    $('.parallax-window').each(function(){
        var $this = $(this);
        $this.parallax({
            speed: $this.data('speed')
        });
    });
})(jQuery);