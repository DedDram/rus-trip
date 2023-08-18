$(function() {
	var objectid = $('form[data-objectid]').data('objectid');
	if (objectid) {
		$('#dating-location-fields').on('change', 'select', function() {
			$.xhr.send(null, {
				action: 'load/dating',
				objectid: objectid,
				country: $('#dating-location-country').val(),
				region: $('#dating-location-region').val(),
				city: $('#dating-location-city').val()
			}, function(result) {
				for (var name in result.data) {
					$('#dating-location-' + name).replaceWith(result.data[name]);
				}
			});
		});
	}
});