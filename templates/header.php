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
    <?php if($_SERVER['REQUEST_URI']=='/'): ?>
    <div class="header-left-box opac">
        <h2>Самые большие города</h2>
        <ul><li><a href="/moskva">Москва</a></li><li><a href="/sankt-peterburg">Санкт-Петербург</a></li><li><a href="/novosibirsk">Новосибирск</a></li><li><a href="/ekaterinburg">Екатеринбург</a></li><li><a href="/nizhniy-novgorod">Нижний Новгород</a></li></ul>
    </div>
    <?php endif; ?>
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
                <?php
                if(!empty($navLinks)){
                    foreach ($navLinks as $key=>$navLink){
                        echo "<li><a href=/$key>".$navLink."</a></li>";
                    }
                }else{
                    echo '<li><a href="/" class="nav-active">Главная</a></li>
                    <li><a href="/kontakty.html">Обратная связь</a></li>
                    <li><a href="/kak-proehat-ot-i-do.html">Как проехать ОТ и ДО</a></li>';
                }
                ?>
            </ul>
        </nav>
        <div class="w728">
            <!--module.db:ads-top-leaderboard-->
        </div>
    </div>