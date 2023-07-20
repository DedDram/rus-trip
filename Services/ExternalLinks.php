<?php

namespace Services;

class ExternalLinks
{
    static function replaceExternalLinks($string) {
        $domen = $_SERVER['HTTP_HOST']; // Получаем домен текущего сайта

        // Создаем регулярное выражение для поиска внешних ссылок
        $pattern = '~<a(.*)href(\s+|=)(\"|\')(http(s|)://|//)(?!' . preg_quote($domen) . ')(.*)(\"|\')(.*)>(.*)</a>~mU';
        // Заменяем найденные внешние ссылки
        $result = preg_replace_callback($pattern, function($matches) {
            $url = $matches[6];
            $linkText = $matches[9];
            return '<a rel="nofollow" target="_blank" href="/index.php?redirect=' . urlencode($url) . '">' . $linkText . '</a>';
        }, $string);

        return $result;
    }
}