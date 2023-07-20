<?php
//метод запроса(гет,пост) - паттерн - контроллер+метод, опционально аргумент метода (id категории из таблицы _categories)
return [
    //главная
    ['GET', '~^/($|\?(.*)$)~', [\Controllers\MainController::class, 'main']],
    //Разные страницы
    ['GET', '~^/go/(\d+)(-$|$|-[^/]+)$~', [\Controllers\MapsController::class, 'sections']],
    ['GET', '~^/go/(\d+)(-$|$|-[^/]+)/(\d+)(-$|$|-[^/]+)$~', [\Controllers\MapsController::class, 'categories']],
    ['GET', '~^/go/(\d+)(-$|$|-[^/]+)/(\d+)(-$|$|-[^/]+)/(\d+)(-$|$|-[^/]+)$~', [\Controllers\MapsController::class, 'city']],
    ['POST', '~^/post$~', [\Controllers\MapsController::class, 'getResponse']],
    ['GET', '~^/cookie-policy$~', [\Controllers\ContentController::class, 'cookiePolicy']],
    ['GET', '~^/privacy-policy$~', [\Controllers\ContentController::class, 'privacyPolicy']],
    ['GET', '~^/contact-us$~', [\Controllers\ContentController::class, 'contact']],
];