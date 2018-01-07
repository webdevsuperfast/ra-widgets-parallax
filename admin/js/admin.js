jQuery(function($){
	$(document).on("click", ".rawp_upload_image_button", function (e) {
		e.preventDefault();

		var $button = $(this);
   
		// Create the media frame.
		var file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Insert image',
			library : {
				type : 'image'
			},
			button: {
				text: 'Use this image' // button label text
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
   
		// When an image is selected, run a callback.
		file_frame.on('select', function () {
			// We set multiple to false so only get one image from the uploader
	
			var attachment = file_frame.state().get('selection').first().toJSON();
	
			$button.siblings('input').val(attachment.url);
			// imgText.src(attachment.url);
			$button.siblings('img').attr('src', attachment.url);
		});
   
		// Finally, open the modal
		file_frame.open();
	});

	function toggleIt() {
		$('body').on('click', '.rawp-toggle', function(e){
			e.preventDefault();

			$(this).toggleClass('open');
			$('.rawp-field').toggle();
		});
	}

	$(document).on('widget-updated', function(event, widget){
		$(widget).each(function(){
			$('.rawp-field').toggle();
		});
	});

	toggleIt();
});