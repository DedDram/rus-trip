$(function() {
	var $photosGallery = $('#photos-gallery');
	$photosGallery.on('click', 'img', function() {
		var $images = $photosGallery.find('img');
		var items = [];
		$images.each(function() {
			var $img = $(this);
			var title = $img.attr('title');
			var src = $img.data('mfp-src');
			items.push({
				src: src,
				title: title
			});
		});
		$.magnificPopup.open({
			items: items,
			type: 'image',
			gallery: {
				enabled: true
			}
		});
	});
	var pagenr = 0;
	var waitLoading = false;
	function loadPhotos() {
		if (!waitLoading && $(window).scrollTop() > $photosGallery.innerHeight()) {
			waitLoading = true;
			waitLoading = false;
		}
	}
	$(window).scroll(loadPhotos);
});