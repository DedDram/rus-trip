$(document).ready(function() {
    var batchSize = 20;
    var offset = batchSize;
    var $hotelContainer = $("#hotel-container");
    var $loadMoreButton = $("#load-more-wrapper");

    // Функция для выполнения AJAX-запроса и добавления отелей на страницу
    function loadMoreHotels() {
        var cityId = $("#city_id").text();
        $.ajax({
            url: "/hotels", // Укажите путь к PHP-обработчику AJAX-запроса
            type: "POST",
            data: {
                cityId: cityId, // Замените на идентификатор вашего города
                offset: offset,
                limit: batchSize
            },
            success: function(response) {
                if (response.length > 0) {
                    $hotelContainer.append(response); // Добавляем полученные отели в контейнер
                    offset += batchSize; // Увеличиваем смещение для следующего запроса
                } else {
                    $loadMoreButton.hide(); // Скрываем кнопку, если нет больше отелей
                }
            }
        });
    }

    // Обработчик клика на кнопке "Показать еще"
    $loadMoreButton.on("click", function() {
        loadMoreHotels();
    });
});