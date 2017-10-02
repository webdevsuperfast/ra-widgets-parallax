(function($){
    $('.parallax-window').each(function(){
        var $this = $(this);
        $this.css('background-image', 'url('+$this.data('image')+')' );
        $this.parallax({
            speed: $this.data('speed')
        });
    });
})(jQuery);