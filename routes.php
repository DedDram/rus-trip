<?php
//метод запроса(гет,пост) - паттерн - контроллер+метод, опционально аргумент метода (id категории из таблицы _categories)
return [
    //главная
    ['GET', '~^/($|\?(.*)$)~', [\Controllers\MainController::class, 'main']],
    //Политика
    ['GET', '~^/politics$~', [\Controllers\ContentController::class, 'politics']],
    //сообщение об ошибке
    ['GET', '~/posterror\?id=(\d+)&object_group=([a-z]+)$~', [\Controllers\PostErrorController::class, 'getError']],
    //POST запросы
    ['POST', '~^/post/comment$~', [\Controllers\PostCommentsController::class, 'getResponse']],
    ['POST', '~^/hotels$~', [\Controllers\ContentController::class, 'getResponse']],
    ['POST', '~^/restaurants$~', [\Controllers\ContentController::class, 'getResponse']],
    ['POST', '~/post/error$~', [\Controllers\PostErrorController::class, 'getResponse']],
    //пользователи, регистрация, авторизация
    ['GET|POST', '~^/users/(\d+)/activate/(.+)$~', [\Controllers\UsersController::class, 'activate']],
    ['GET|POST', '~^/users/register$~', [\Controllers\UsersController::class, 'signUp']],
    ['GET|POST', '~^/users/login$~', [\Controllers\UsersController::class, 'login']],
    ['GET|POST', '~^/users/logout$~', [\Controllers\UsersController::class, 'logOut']],
    ['GET|POST', '~^/users/reset$~', [\Controllers\UsersController::class, 'reset']],
    ['GET|POST', '~^/users/(\d+)/reset/(.+)$~', [\Controllers\UsersController::class, 'resetCheck']],
    ['GET|POST', '~^/users/(\d+)/password$~', [\Controllers\UsersController::class, 'newPassword']],
    ['GET|POST', '~^/users/profile$~', [Controllers\UsersController::class, 'profile']],
    ['GET', '~^/comments\?task=unsubscribe&object_group=([a-z]+)&object_id=(\d+)$~', [\Controllers\UsersController::class, 'getResponse']],
    //Cron
    ['GET', '~^/cron/comments\?(.*)$~', [\Controllers\CronController::class, 'getResponse']],
    ['GET', '~^/cron/sitemap$~', [\Controllers\CronController::class, 'siteMap']],
    //Угадывание городов
    ['GET', '~^/guess_the_city$~', [\Controllers\ContentController::class, 'guessCity']],
    //Редирект при переходе по внешним ссылкам
    ['GET', '~^/index.php\?redirect=(.*)$~', [\Controllers\ContentController::class, 'redirect']],
    //Редирект старых ссылок
    ['GET', '~^/(.*)/(\d+)-gostinitsi-(.*)$~', [\Controllers\MainController::class, 'main']],
    ['GET', '~^/(.*)/(\d+)-znakomstva-v-(.*)$~', [\Controllers\MainController::class, 'main']],
    ['GET', '~^/(.*)/(\d+)-karta-(.*)$~', [\Controllers\MainController::class, 'main']],
    ['GET', '~^/(.*)/(\d+)-foto-(.*)$~', [\Controllers\MainController::class, 'main']],
    ['GET', '~^/(.*)/taxi/(.*)$~', [\Controllers\MainController::class, 'main']],
    ['GET', '~^/content/view/(.*)$~', [\Controllers\MainController::class, 'main']],
    ['GET', '~^/kak-proehat-ot-i-do\.html(.*)$~', [\Controllers\ContentController::class, 'RedirectProehat']],
    ['GET', '~^/(.*)/kak-dobratsya(.*)$~', [\Controllers\ContentController::class, 'RedirectProehat']],
    ['GET', '~^/(.*)/taxi$~', [\Controllers\ContentController::class, 'RedirectMain']],
    //Разные страницы
    ['GET', '~^/cookie-policy$~', [\Controllers\ContentController::class, 'cookiePolicy']],
    ['GET', '~^/kak-proehat-ot-i-do($|\?(.*)$)~', [\Controllers\ContentController::class, 'kakProehat']],
    ['GET', '~^/contact$~', [\Controllers\ContentController::class, 'contact']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)($|\?(.*)$)~', [\Controllers\ContentController::class, 'city']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/karta($|\?(.*)$)~', [\Controllers\ContentController::class, 'map']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/memorials($|\?(.*)$)~', [\Controllers\ContentController::class, 'memorials']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/dostoprimechatelnosti($|\?(.*)$)~', [\Controllers\ContentController::class, 'RedirectMemorials']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/dostoprimechatelnosti/(.*)$~', [\Controllers\ContentController::class, 'RedirectMemorials']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/memorial-([a-z\d\-]+)-(\d+)($|\?(.*)$)~', [\Controllers\ContentController::class, 'memorial']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/hotels($|\?(.*)$)~', [\Controllers\ContentController::class, 'hotels']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/gostinicy($|\?(.*)$)~', [\Controllers\ContentController::class, 'RedirectHotels']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/gostinicy/(.*)$~', [\Controllers\ContentController::class, 'RedirectHotels']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/hotel-([a-z\d\-]+)-(\d+)$~', [\Controllers\ContentController::class, 'hotel']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/restaurants($|\?(.*)$)~', [\Controllers\ContentController::class, 'restaurants']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/restorany($|\?(.*)$)~', [\Controllers\ContentController::class, 'RedirectRestaurants']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/restorany/(.*)$~', [\Controllers\ContentController::class, 'RedirectRestaurants']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/restaurant-([a-z\d\-]+)-(\d+)($|\?(.*)$)~', [\Controllers\ContentController::class, 'restaurant']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/znakomstva($|\?(.*)$)~', [\Controllers\ContentController::class, 'znakomstva']],
    ['GET', '~^/([a-zA-Z]+(?:-[a-zA-Z]+)*)/foto($|\?(.*)$)~', [\Controllers\ContentController::class, 'foto']],
];