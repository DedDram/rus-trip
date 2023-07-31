<?php
//метод запроса(гет,пост) - паттерн - контроллер+метод, опционально аргумент метода (id категории из таблицы _categories)
return [
    //главная
    ['GET', '~^/($|\?(.*)$)~', [\Controllers\MainController::class, 'main']],
    //Разные страницы
    ['GET', '~^/cookie-policy$~', [\Controllers\ContentController::class, 'cookiePolicy']],
    ['GET', '~^/privacy-policy$~', [\Controllers\ContentController::class, 'privacyPolicy']],
    ['GET', '~^/contact-us$~', [\Controllers\ContentController::class, 'contact']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)$~', [\Controllers\ContentController::class, 'city']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/karta$~', [\Controllers\ContentController::class, 'map']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/memorials$~', [\Controllers\ContentController::class, 'memorials']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/memorial-([a-z\-]+)-(\d+)$~', [\Controllers\ContentController::class, 'memorial']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/hotels$~', [\Controllers\ContentController::class, 'hotels']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/hotel-([a-z\-]+)-(\d+)$~', [\Controllers\ContentController::class, 'hotel']],
    ['POST', '~^/hotels$~', [\Controllers\ContentController::class, 'getResponse']],
];