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
    <link rel="stylesheet" href="/static/website/css/theme.css">
    <?= $style ?? '' ?>
    <!--cssEnd-->
    <script>window.yaContextCb=window.yaContextCb||[]</script>
    <script src="https://yandex.ru/ads/system/context.js" async></script>
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
    <div class="header-left-box opac login">
        <h2>Авторизация</h2>
        <ul>
            <?php
            if (empty($user)) {
                echo '<li><a href="/users/register" rel="nofollow">Регистрация</a></li>
                      <li class=""><a href="/users/login" rel="nofollow">Вход</a></li>';
            }else{
                echo '<li><a href="/users/logout">Выход ' . '</a><li><a href="/users/profile">Настройки</a></li>';
            }
            echo '<li><a href="/politics">Политика</a></li>';
            ?>
        </ul>
    </div>
    <div class="cities-changer header-left-box opac"></div>
    <div class="blazon-box header-right-box">
        <?php
        if(!empty($city)){
            echo '<img src="/'.$city->blazon.'" alt="Герб города '.$city->name.'" title="Герб города '.$city->name.'" />';
        }else{
            echo '<img src="/static/uploads/blazonry/russia.png" alt="Герб России" title="Герб России">';
        }
        ?>
    </div>
</header>
<section>
    <div class="section-header">
        <nav class="main-nav">
            <ul class="w728">
                <a class="menu-item toggleMenu" href="#">☰</a>
                <div id="menu2">
                    <ul class="nav">
                        <?php
                        if (!empty($navLinks)) {
                            foreach ($navLinks as $key => $navLink) {
                                echo "<li><a href=/$key>" . $navLink . "</a></li>";
                            }
                        } else {
                            echo '<li><a href="/" class="nav-active">Главная</a></li>
                    <li><a href="/contact">Обратная связь</a></li>
                    <li><a href="/kak-proehat-ot-i-do">Как проехать ОТ и ДО</a></li>';
                        }
                        ?>
                    </ul>
                </div>
            </ul>
        </nav>
        <div class="w728">
            <!--module.db:ads-top-leaderboard-->
        </div>
    </div>