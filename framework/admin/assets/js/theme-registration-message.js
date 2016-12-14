(function ($) {

	$(document).ready(function() {
		var data = {
			action: 'znkl_dismiss_theme_register'
		};

		$(document).on('click', '.znkl-theme-registration-dismissal .notice-dismiss', function(e){
			$.ajax({
				url: ajaxurl,
				data: data
			});
		});
	});

})(jQuery);
