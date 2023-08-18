$(document).ready(function() {
    var batchSize = 20;
    var offset = batchSize;
    var $hotelContainer = $("#hotel-container");
    var $loadMoreButton = $("#load-more-wrapper");
    function loadMoreHotels() {
        var cityId = $("#city_id").text();
        $.ajax({
            url: "/restaurants",
            type: "POST",
            data: {
                cityId: cityId,
                object: 'restaurants',
                offset: offset,
                limit: batchSize
            },
            success: function(response) {
                if (response.length > 0) {
                    $hotelContainer.append(response);
                    offset += batchSize;
                } else {
                    $loadMoreButton.hide();
                }
            }
        });
    }
    $loadMoreButton.on("click", function() {
        loadMoreHotels();
    });
});