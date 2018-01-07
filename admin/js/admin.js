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
	
			$button.siblings('input').val(attachment.url).change();
			// imgText.src(attachment.url);
			$button.siblings('img').attr('src', attachment.url);

			$button.addClass('hidden');

			$button.siblings('button').removeClass('hidden');
		});
   
		// Finally, open the modal
		file_frame.open();
	});

	$(document).on('click', '.rawp_delete_image_button', function(e){
		e.preventDefault();

		var $button = $(this);

		$button.addClass('hidden');
		$button.siblings('button').removeClass('hidden');

		$button.siblings('img').attr('src', '');
		$button.siblings('input').val('').change();
	});

	$(document).on('click', '.rawp-toggle', function(e) {
		e.preventDefault();

		var toggler = $(this);

		toggler.toggleClass('open');
		toggler.next().toggle();

		// Add display to local storage
		localStorage.setItem('display', toggler.next().is(':visible'));
	});

	if (localStorage.getItem('display') == 'true') {
		$('.rawp-field').show();
	}

	$(document).on('widget-updated widget-added', function(event, widget){
		$(widget).each(function(){
			var toggler = $(this).find('.rawp-toggle');
			var display = localStorage.getItem('display');
			if (display == 'true') {
				toggler.toggleClass('open');
				toggler.next().show();
			}
		});
	});
});