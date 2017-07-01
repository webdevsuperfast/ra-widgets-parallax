jQuery(function($){
	$('body').on('click', '.rawp_upload_image_button', function(e){
		e.preventDefault();
 
    		var button = $(this),
                inputText = button.prev('.widefat'),
    		    custom_uploader = wp.media({
			title: 'Insert image',
			library : {
				type : 'image'
			},
			button: {
				text: 'Use this image' // button label text
			},
			multiple: false // for multiple image selection set to true
		}).on('select', function() { // it also has "open" and "close" events 
			var attachment = custom_uploader.state().get('selection').first().toJSON();
            inputText.val(attachment.url);
		})
		.open();
	});
	$('body').on('click', '.toggle', function(e){
		$(this).toggleClass('open');
		$('.rawp-field').toggle();
	});
});