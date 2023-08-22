$.quizLoadStartTime = new Date().getTime();
$(function () {
	function buildQuiz(label) {
		$.ajax({
			type: "GET",
			url: "/guess_the_city",
			data: null,
			success: function(result) {
				var total_time = new Date().getTime() - $.quizLoadStartTime;
				var photo_quality = total_time < 3000 ? 'full' : 'thumb';

				var photoData = result.photo[0];
				var html = '<h6>Угадай город по фото</h6>'
					+ '<img src="/' + photoData[photo_quality] + '" loading="lazy" class="form-quiz-photo-' + photo_quality + '" /><br/>';

				for (var i = 0; i < result.cities.length; i++) {
					var city = result.cities[i];
					html += '<button value="' + city.alias + '">' + city.name + '</button>';
				}

				var wrapper = $('<div/>').addClass('form-quiz-wrapper').html(html);

				$('.quiz-wrapper').append(wrapper);

				wrapper.find('button').on('click', function(e) {
					e.preventDefault();
					if (this.value === result.answer.alias) {
						alert('Поздравляем, вы угадали!');
					} else {
						alert('Ну что же Вы - это ж ' + result.answer.name + '!');
					}
					location.pathname = '/' + result.answer.alias + '/foto';
				});
			},
			error: function(xhr, status, error) {
				console.error("AJAX Error:", error);
			}
		});
	}

	$('form').each(function () {
		var form = $(this).data('ajax-form', 1),
		button = form.find('button[data-form-button]'),
		copy = form.find('input[name="copyright-agreement"]');

		if (copy.length) {
			copy.change(function () {
				if ($(this).is(':checked')) {
					button.parents('label').show();
				} else {
					button.parents('label').hide();
				}
			});
			button.parents('label').hide();
		}

		var label = form.find('label:first');
		if (label.length) {
			buildQuiz(label);
		}

		button.click(function (e) {
			e.preventDefault();
			$.xhr.send(form, new FormData(form.get(0)));
		});
	});
});
