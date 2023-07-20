<?php

namespace Services;

class stString
{
    public static function transliterate($string): string
    {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );
        $str = strtr($string, $converter);
        $str = strtolower($str);
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        $str = preg_replace('/(\S)\1{2,}/', '$1', $str);
        return trim($str, "-");
    }

    /*
     *
     * Первый символ
     *
     */
    public static function mb_ucfirst($str, $enc = 'utf-8'): string
    {
        return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc) . mb_substr($str, 1, mb_strlen($str, $enc), $enc);
    }

    /*
     *
     * Форматирует пробелы
     *
     */
    public static function gaps($text): string
    {
        // атрибуты
        $text = preg_replace("#(</?\w+)(?:\s(?:[^<>/]|/[^<>])*)?(/?>)#ui", '$1$2', $text);

        // пробелы
        $text = str_replace('&nbsp;', ' ', $text);
        $text = preg_replace("/\s{2,}/", ' ', $text);
        $text = preg_replace('/(!{1,}|\.{1,}|,{1,}|\?{1,})(\S)/', '$1 $2', $text);
        $text = preg_replace('/(\s)(!|\.|,|\?)/', '$2', $text);
        return trim($text);
    }

    /*
     * Функция генерации пароля
     *
     */
    public static function genPassword(): string
    {
        $arr = array('x', 'w', 'z', 'f', 'h', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
        $pass = "";
        for ($i = 0; $i < 8; $i++) {
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }

    /**
     * Функция склонения слов
     *
     * MedString::declension($seconds, array('секунда','секунды','секунд'));
     *
     * @param int $digit
     * @param array $expr
     * @param bool $onlyword
     * @return
     */
    public static function declension(int $digit, array $expr, bool $onlyword = false): string
    {
        if (!is_array($expr)) $expr = array_filter(explode(' ', $expr));
        if (empty($expr[2])) $expr[2] = $expr[1];
        $i = preg_replace('/[^0-9]+/s', '', $digit) % 100;
        if ($onlyword) $digit = '';
        if ($i >= 5 && $i <= 20) $res = $digit . ' ' . $expr[2];
        else {
            $i %= 10;
            if ($i == 1) $res = $digit . ' ' . $expr[0];
            elseif ($i >= 2 && $i <= 4) $res = $digit . ' ' . $expr[1];
            else $res = $digit . ' ' . $expr[2];
        }
        return trim($res);
    }
}
