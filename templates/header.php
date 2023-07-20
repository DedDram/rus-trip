<?php defined('_DEF') or exit(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-dns-prefetch-control" content="on">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <meta name="keywords" content="<?= $metaKey ?? $_SERVER['HTTP_HOST'] ?>"/>
    <meta name="description" content="<?= $metaDesc ?? $_SERVER['HTTP_HOST'] ?>"/>
    <?= $robots ?? '' ?>
    <title><?= $title ?? $_SERVER['HTTP_HOST'] ?></title>
    <link rel="dns-prefetch" href="//api-maps.yandex.ru">
    <link rel="preconnect" href="//api-maps.yandex.ru">
    <link rel="shortcut icon" href="/static/website/img/favicon.ico"/>
    <!--css-->
    <?= $style ?? '' ?>
    <link rel="stylesheet" href="/static/website/css/theme.css">
    <!--cssEnd-->
    <?= $scriptNoCompress ?? '' ?>
    <!--js-->
    <script src="/templates/main/js/jquery-3.6.3.min.js"></script>
    <script src="/templates/main/js/site.js"></script>
    <?= $script ?? '' ?>
    <!--jsEnd-->
</head>
<body>
<header>
    <div class="headermap"><a href="/" title="Путеводитель по России">Путеводитель по России</a></div>
    <div class="cities-changer header-left-box opac"></div>
    <div class="header-left-box opac">
        <h2>Самые большие города</h2>
        <ul><li><a href="/moskva">Москва</a></li><li><a href="/sankt-peterburg">Санкт-Петербург</a></li><li><a href="/novosibirsk">Новосибирск</a></li><li><a href="/ekaterinburg">Екатеринбург</a></li><li><a href="/nizhniy-novgorod">Нижний Новгород</a></li></ul>
    </div>
    <div class="blazon-box header-right-box">
        <?= $gerb ?? '<img src="/static/uploads/blazonry/russia.png" alt="Герб России" title="Герб России">' ?>
    </div>
</header>