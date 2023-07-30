$(function() {
	var $photosGallery = $('#photos-gallery');

	// При клике на изображение внутри #photos-gallery
	$photosGallery.on('click', 'img', function() {
		var $images = $photosGallery.find('img');
		var items = [];

		// Собираем все изображения в массив items
		$images.each(function() {
			var $img = $(this);
			var title = $img.attr('title');
			var src = $img.data('mfp-src');
			items.push({
				src: src,
				title: title
			});
		});

		// Открываем галерею magnificPopup с массивом items
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
			waitLoading = false; // Установите в false, когда загрузка завершится
		}
	}

	$(window).scroll(loadPhotos);
});